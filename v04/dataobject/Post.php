<?php
require_once 'dataobject/Editable.php';
require_once("settings.php");

class Post extends Editable {
	const DEFAULT_CATEGORY = "News";
	const TITLE = "title";
	const SUBTITLE = "subtitle";
	const HEADLINE = "headline";
	const AUTHOR = "author";
	const TAGS = "tags";
	const CATEGORIES = "categories";
	const CONTENT = "content";
	const VISIBLE = "visible";
	const TYPE = "type";
	const PLACE = "place";
	const PLACE_NAME = "place";
	const NEWS = "news";
	const VIDEOREP = "videoreportage";
	const PHOTOREP = "photoreportage";
	const COLLECTION = "collection";
	const ALBUM = "album";
	const MAGAZINE = "magazine";
	const PLAYLIST = "playlist";
	static $TYPES = array(self::NEWS, self::VIDEOREP, self::PHOTOREP, self::COLLECTION, self::ALBUM, self::MAGAZINE, self::PLAYLIST);
	
	protected $ID;						// id recuperato dal database
	protected $permalink;				// permalink generato automaticamente dal titolo ecc…
	protected $type;					// appartenente a PostType
	protected $title;					// titolo
	protected $subtitle;				// sottotitolo
	protected $headline; 				// occhiello
	protected $author;					// id di oggetto User
	protected $creationDate;			// UNIX-like TimeStamp
	protected $modificationDate;		// UNIX-like TimeStamp
	protected $tags;					// stringa di tag separati da virgole
	protected $categories;				// stringa di categorie separate da virgole
	protected $comments;				// array di oggetti COMMENTO
	protected $content;					// testo del contenuto o indirizzo del video o foto o array di essi
	protected $visible;					// boolean
	protected $place;					// 
	protected $placeName;				// 
	protected $reports;					// array di oggetti Report
	protected $vote;					// float voto medio
	protected $accessCount = 1;			// numero di accessi
		
	/**
	 * Crea un oggetto post.
	 *
	 * @param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo del post (string filtrata)
	 * subtitle: sottotitolo del post (string filtrata)
	 * headline: occhiello del post (string filtrata)
	 * author: id dell'autore (long)
	 * tags: stringa di tag separati da virgole
	 * categories: stringa di categorie separate da virgole
	 * content: il testo di un articolo (string filtrata), l'indirizzo del videoreportage o l'elenco di indirizzi delle foto di un fotoreportage
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 * type: tipo di post, deve essere incluso in PostType
	 * place: una coppia latitudine, longitudine (inattivo fino a che non paghiamo le API di Google)
	 * place_name: nome di un luogo
	 * 
	 * @return: l'articolo creato.
	 */
	function __construct($data) {
		if(!is_array($data) && is_numeric($data)) {
			$data = array("ID" => $data);
		}
		
		$this->setCreationDate($_SERVER["REQUEST_TIME"]);
		if(isset($data[self::TITLE]))
			$this->setTitle($data[self::TITLE]);
		if(isset($data[self::SUBTITLE]))
			$this->setSubtitle($data[self::SUBTITLE]);
		if(isset($data[self::AUTHOR]))
			$this->setAuthor($data[self::AUTHOR]);
		if(isset($data[self::HEADLINE]))
			$this->setHeadline($data[self::HEADLINE]);
		if(isset($data[self::TAGS]))
			$this->setTags($data[self::TAGS]);
		if(isset($data[self::CATEGORIES]))
			$this->setCategories($data[self::CATEGORIES]);
		if(isset($data[self::CONTENT]))
			$this->setContent($data[self::CONTENT]);
		if(isset($data[self::VISIBLE]))
			$this->setVisible($data[self::VISIBLE]);
		if(!isset($data[self::TYPE]) || array_search($data[self::TYPE], self::$TYPES) === false);
			$data[self::TYPE] = self::NEWS;
		$this->setType($data[self::TYPE]);
		if(MAPS_ENABLED && isset($data[self::PLACE]))
			$this->setPlace($data[self::PLACE]);
		if(isset($data[self::PLACE_NAME]))
			$this->setPlace($data[self::PLACE_NAME]);
		if(isset($data[Post::RED_CONTENT]))
			$post->setContent($data[Post::RED_CONTENT]);
		if(isset($data[Post::YELLOW_CONTENT]))
			$post->setContent($data[Post::YELLOW_CONTENT]);
	}
	
	function getID() {
		return $this->ID;
	}
	function getTitle() {
		return $this->title;
	}
	function getSubtitle() {
		return $this->subtitle;
	}
	function getHeadline() {
		return $this->headline;
	}
	function getAuthor() {
		return $this->author;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function getModificationDate() {
		return $this->modificationDate;
	}
	function getTags() {
		return $this->tags;
	}
	function getCategories() {
		return $this->categories;
	}
	function getContent() {
		return $this->content;
	}
	function isVisible() {
		return $this->visible;
	}
	function getType() {
		return $this->type;
	}
	function getComments() {
		if(!isset($this->comments) || !is_array($this->comments))
			return array();
		return $this->comments;
	}
	function getReports() {
		return $this->reports;
	}
	function getPlace() {
		if(MAPS_ENABLED && !is_null($this->place))
			return $this->place;
		else
			return $this->placeName;
	}
	function getPlaceName() {
		return $this->placeName;
	}
	function getPermalink() {
		return $this->permalink;
	}
	function getVote() {
		return $this->vote;
	}
	
	function setID($id) {
		$this->ID = intval($id);
		return $this;
	}
	function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	function setSubtitle($subtitle) {
		$this->subtitle = $subtitle;
		return $this;
	}
	function setHeadline($occh) {
		$this->headline = $occh;
		return $this;
	}
	function setAuthor($author) {
		$this->author = $author;
		return $this;
	}
	function setCreationDate($cDate) {
		$this->creationDate = $cDate;
		return $this;
	}
	function setModificationDate($mDate) {
		$this->modificationDate = $mDate;
		return $this;
	}
	function setTags($tags) {
		$this->tags = $tags;
		return $this;
	}
	function setCategories($categories) {
		$this->categories = $categories;
		return $this;
	}
	function setContent($content) {
		$this->content = $content;
		return $this;
	}
	function setVisible($visible) {
		settype($visible,"boolean"); // forza $visible ad essere boolean
		$this->visible = $visible;
		return $this;
	}
	function setType($type) {
		$this->type = $type;
		return $this;
	}
	function setComments($comments) {
		$this->comments = $comments;
		return $this;
	}
	function setVote($vote) {
		if(!is_numeric($vote)) return $this;
			$this->vote = $vote;
		return $this;
	}
	function setPlace($place) {
		$this->place = $place;
		return $this;
	}
	function setPlaceName($placename) {
		$this->placeName = $placename;
		return $this;
	}
	function setReports($reports) {
		$this->reports = $reports;
		return $this;
	}
	function setPermalink($permalink) {
		$this->permalink = $permalink;
		return $this;
	}
	function setAccessCount($accessCount) {
		if(is_numeric($accessCount))
			$this->accessCount = intval($accessCount);
		return $this;
	}
	
	function addComment($comment) {
		if(!is_array($this->comments))
			$this->comments = array();
		$this->comments[] = $comment;
		return $this;
	}
	
	function edit($data) {
		if(isset($data[Post::TITLE]))
			$this->setTitle($data[Post::TITLE]);
		if(isset($data[Post::SUBTITLE]))
			$this->setSubtitle($data[Post::SUBTITLE]);
		if(isset($data[Post::HEADLINE]))
			$this->setHeadline($data[Post::HEADLINE]);
		if(isset($data[Post::TAGS]))
			$this->setTags($data[Post::TAGS]);
		if(isset($data[Post::CATEGORIES]))
			$this->setCategories($data[Post::CATEGORIES]);
		if(isset($data[Post::CONTENT]))
			$this->setContent($data[Post::CONTENT]);
		if(isset($data[Post::RED_CONTENT]))
			$this->setContent($data[Post::RED_CONTENT]);
		if(isset($data[Post::YELLOW_CONTENT]))
			$this->setContent($data[Post::YELLOW_CONTENT]);
		if(isset($data[Post::BLACK_CONTENT]) && AuthorizationManager::canUserDo(AuthorizationManager::SET_BLACK, $this))
			$this->setContent($data[Post::BLACK_CONTENT]);
		if(isset($data[Post::VISIBLE]))
			$this->setVisible($data[Post::VISIBLE]);

		return $this;
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "<font color='" . $this->getContentColor() . "'>Post (ID = " . $this->getID() .
			 " | postType = " . $this->getType() .
			 " | title = " . $this->getTitle() .
			 " | subtitle = " . $this->getSubtitle() .
			 " | headline = " . $this->getHeadline() .
			 " | author = " . $this->getAuthor() .
			 " | creationDate = " . date("d/m/Y G:i:s", $this->getCreationDate()) .
			 " | modificationDate = " . date("d/m/Y G:i:s", $this->getModificationDate()) .
			 " | tags = (" . $this->tags . 
			 ") | categories = (" . $this->categories .
			 ") | comments = (";
		for($i=0; $i<count($this->getComments()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->comments[$i];
		}
		$s.= ") | votes = (";
		for($i=0; $i<count($this->getVotes()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->votes[$i];
		}
		$s.= ") | content = " . $this->getContent();
		$vis = $this->isVisible() ? "true" : "false";
		$s.= " | visible = " . $vis .
			 " | reports = (";
		for($i=0; $i<count($this->getReports()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->reports[$i];
		}
		$s.= "))</font>";
		return $s;
	}
}
?>