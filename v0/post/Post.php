<?php

class Post {
	protected $ID;						// id recuperato dal database
	protected $type;					// appartenente a PostType
	protected $title;					// titolo
	protected $subtitle;				// sottotitolo
	protected $headline; 				// occhiello
	protected $author;					// id di oggetto User
	protected $creationDate;			// UNIX like TimeStamp
	protected $modificationDate;		// UNIX like TimeStamp
	protected $tags = array();			// stringa di tag separati da virgole
	protected $categories;				// stringa di categorie separate da virgole
	protected $comments = array();		// array di oggetti COMMENTO
	protected $votes = array();			// array di oggetto VOTO
	protected $content;					// testo del contenuto o indirizzo del video o foto o array di essi
	protected $visible;					// boolean
	protected $place;					// 
	protected $reports = array();		// array di oggetti Report
	
	/**
	 * Crea un oggetto post.
	 *
	 * param data: array associativo contenente i dati.
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
	 * place: db v0 id di un luogo in db, db v0.2 stringa (secondo le regole delle API Google)
	 * 
	 * return: l'articolo creato.
	 */
	function __construct($data) {
		if(!is_array($data) && is_numeric($data)) {
			$data = array("ID" => $data);
		}
		$this->setCreationDate(time());
		
		if(isset($data["title"]))
			$this->setTitle($data["title"]);
		if(isset($data["subtitle"]))
			$this->setSubtitle($data["subtitle"]);
		if(isset($data["author"]))
			$this->setAuthor($data["author"]);
		if(isset($data["headline"]))
			$this->setHeadline($data["headline"]);
		if(isset($data["tags"]))
			$this->setTags($data["tags"]);
		if(isset($data["categories"]))
			$this->setCategories($data["categories"]);
		if(isset($data["content"]))
			$this->setContent($data["content"]);
		if(isset($data["visible"]))
			$this->setVisible($data["visible"]);
		if(isset($data["type"]))
			$this->setType($data["type"]);
		if(isset($data["place"]))
			$this->setCategories($data["place"]);
	}
	
	function addComment($comment) {
		$this->comments[] = $comment;
		$this->update();
		return $this;
	}
	
	function addVote($vote) {
		$this->votes[] = $vote;
		$this->update();
		return $this;
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
		return $this->comments;
	}
	function getVotes() {
		return $this->votes;
	}
	function getReports() {
		return $this->reports;
	}
	function getPlace() {
		return $this->place;
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
	function setVotes($votes) {
		$this->votes = $votes;
		return $this;
	}
	function setPlace($place) {
		$this->place = $place;
		return $this;
	}
	function setReports($reports) {
		$this->reports = $reports;
		return $this;
	}
	
	/**
	 * Salva il post e le sue dipendenze nel database.
	 * Le dipendenze salvate sono quelle che dipendono dall'autore ovvero: tag e categorie.
	 * Potrebbe salvare alcune tuple in Tag.
	 *
	 * return: ID della tupla inserita (o aggiornata), FALSE se c'è un errore.
	 */
	function save() {
		require_once("post/PostCommon.php");
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $q->getDBSchema();
			$table = $dbs->getTable("Post");
			$data = array("ps_type" => $this->getType());
			if(isset($this->title) && !is_null($this->getTitle()))
				$data["ps_title"] = $this->getTitle();
			if(isset($this->subtitle) && !is_null($this->getSubtitle()))
				$data["ps_subtitle"] = $this->getSubtitle();
			if(isset($this->headline) && !is_null($this->getHeadline()))
				$data["ps_headline"] = $this->getHeadline();
			if(isset($this->tags) && !is_null($this->getTags()))
				$data["ps_tags"] = $this->getTags();
			if(isset($this->categories) && !is_null($this->getCategories()))
				$data["ps_categories"] = $this->getCategories();
			if(isset($this->content) && !is_null($this->getContent()))
				$data["ps_content"] = serialize($this->getContent());
			if(isset($this->visible) && !is_null($this->isVisible()))
				$data["ps_visible"] = $this->isVisible() ? 1 : 0;
			if(isset($this->author) && !is_null($this->getAuthor()))
				$data["ps_author"] = $this->getAuthor();
			if(isset($this->place) && !is_null($this->getPlace()))
				$data["ps_place"] = $this->getPlace(); //TODO Non ancora implementato.
			
			$rs = $q->execute($s = $q->generateInsertStm($table,$data));
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			$this->ID = mysql_insert_id();
			//echo "<br />" . serialize($this->ID); //DEBUG
			$rs = $q->execute($s = $q->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$this->getID())),
														 array()));
			//echo "<br />" . $s; //DEBUG
			while($row = mysql_fetch_assoc($rs)) {
				$this->setCreationDate(time($row["ps_creationDate"]));
				$this->setModificationDate(time($row["ps_creationDate"]));
				//echo "<br />" . serialize($row["ps_creationDate"]); //DEBUG
				break;
			}
			//TODO inserire tags, categories e place.
			//echo "<br />" . $this; //DEBUG
			require_once("common.php");
			LogManager::addLogEntry($this->getAuthor(), LogManager::$INSERT, $this);
			return $this->ID;
		}
		return false;
	}
	
	/**
	 * Aggiorna il post e le sue dipendenze nel database.
	 * Le dipendenze aggiornate sono quelle che dipendono dall'autore ovvero: tag e categorie
	 * Potrebbe salvare alcune tuple in Tag.
	 *
	 * return: modificationDate o FALSE se c'è un errore.
	 */
	function update() {
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable("Post");
			$rs = $q->execute($s = $q->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$this->getID())),
														 array()));
			//echo "<br />" . $s; //DEBUG
			$data = array();
			while($row = mysql_fetch_assoc($rs)) {
				//cerco le differenze e le salvo.
				if($row["ps_title"] != $this->getTitle())
					$data["ps_title"] = $this->getTitle();
				if($row["ps_subtitle"] != $this->getSubtitle())
					$data["ps_subtitle"] = $this->getSubtitle();
				if($row["ps_headline"] != $this->getHeadline())
					$data["ps_headline"] = $this->getHeadline();
				if(unserialize($row["ps_content"]) != $this->getContent())
					$data["ps_content"] = serialize($this->getContent());
				if($row["ps_place"] != $this->getPlace())
					$data["ps_place"] = $this->getPlace();
				if($row["ps_tags"] != $this->getTags())
					$data["ps_tags"] = $this->getTags();
				//TODO salvare tag non esistenti
				if($row["ps_categories"] != $this->getCategories())
					$data["ps_categories"] = $this->getCategories();
				settype($row["ps_visible"], "boolean");
				if($row["ps_visible"] !== $this->isVisible())
					$data["ps_visible"] = $this->isVisible() ? 1 : 0;
				break;
			}
			
			$data["ps_modificationDate"] = date("Y/m/d G:i:s", time()); // se mi dicono di fare l'update, cambio modificationDate
			//echo "<br />" . serialize($data); //DEBUG
			//TODO controllare tag e categorie
			
			$rs = $q->execute($s = $q->generateUpdateStm($table,
														 $data,
														 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$this->getID()))));
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . mysql_affected_rows(); //DEBUG
			if(mysql_affected_rows() == 0)
				return false;
			
			//echo "<br />" . $this; //DEBUG
			LogManager::addLogEntry($this->getAuthor(), LogManager::$UPDATE, $this);
			return $this->getModificationDate();
		}
		return false;
	}
	
	/**
	 * Cancella il post dal database.
	 * Con le Foreign Key e ON DELETE, anche le dipendenze dirette vengono cancellate.
	 * Non vengono cancellate le dipendenze nelle Collection.
	 *
	 * return: l'oggetto cancellato o FALSE se c'è un errore.
	 */
	function delete() {
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $q->getDBSchema();
			$table = $dbs->getTable("Post");
			$rs = $q->execute($s = $q->generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$this->getID()))));
			//echo "<br />" . $s; //DEBUG
			if(mysql_affected_rows() == 1) {
				require_once("common.php");
				LogManager::addLogEntry($this->getAuthor(), LogManager::$DELETE, $this);
				return $this;
			}
		}
		return false;
	}
	
	/**
	 * Crea un post caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Post().
	 *
	 * param $id: l'ID del post da caricare.
	 * return: il post caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($id) {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Post");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$id)),
													 array()));
		
		//echo "<p>" . $s . "</p>"; //DEBUG
		//echo "<p>" . mysql_num_rows($rs) . "</p>"; //DEBUG
		if($rs !== false && mysql_num_rows($rs) == 1) {
			// echo serialize(mysql_fetch_assoc($rs)); //DEBUG
			while($row = mysql_fetch_assoc($rs)) {
				$data = array("title" => $row["ps_title"],
							  "subtitle" => $row["ps_subtitle"],
							  "headline" => $row["ps_headline"],
							  "author"=> intval($row["ps_author"]),
							  "tags" => $row["ps_tags"],
							  "categories" => $row["ps_categories"],
							  "content" => unserialize($row["ps_content"]),
							  "visible" => $row["ps_visible"] > 0,
							  "type" => $row["ps_type"],
							  "place" => $row["ps_place"]);
				if($row["ps_type"] == PostType::$NEWS)
					$p = new News($data);
				else if($row["ps_type"] == PostType::$VIDEOREPORTAGE)
					$p = new VideoReportage($data);
				else if($row["ps_type"] == PostType::$ALBUM)
					$p = new Album($data);
				else if($row["ps_type"] == PostType::$MAGAZINE)
					$p = new Magazine($data);
				else if($row["ps_type"] == PostType::$PHOTOREPORTAGE)
					$p = new PhotoReportage($data);
				else if($row["ps_type"] == PostType::$PLAYLIST)
					$p = new Playlist($data);
				else if($row["ps_type"] == PostType::$COLLECTION)
					$p = new Collection($data);
				else
					$p = new Post($data);
				$p->setCreationDate(time($row["ps_creationDate"]));
				$p->setID(intval($row["ps_ID"]));
				$p->setModificationDate(time($row["ps_modificationDate"]));
				break;
			}
			$p->loadComments()->loadVotes()->loadReports();
			//echo "<p>" .$p ."</p>";
			return $p;
		} else {
			$GLOBALS["query_error"] = "NOT FOUND";
			return false;
		}
	}
	
	/**
	 * Carica in this i commenti recuperati dal database per questo post (deve avere un ID!).
	 */
	function loadComments() {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Comment");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("cm_post"),Operator::$UGUALE,$this->getID())),
													 array()));
		
		//echo "<p>" . $s . "</p>"; //DEBUG;
		if($rs !== false) {
			$comm = array();
			while($row = mysql_fetch_assoc($rs)) {
				require_once("post/PostCommon.php");
				$com = new Comment(array("author" => intval($row["cm_author"]),
										 "post" => intval($row["cm_post"]),
										 "comment" => $row["cm_comment"]));
				$com->setID($row["cm_ID"])->setCreationDate(time($row["cm_creationDate"]));
				$comm[] = $com;
			}
			$this->setComments($comm);
		}
		return $this;
	}
	
	/**
	 * Carica in this i voti recuperati dal database per questo post (deve avere un ID!).
	 */
	function loadVotes() {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Vote");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$this->getID())),
													 array()));
		//echo "<p>" . $s . "</p>"; //DEBUG;
		if($rs !== false) {
			$votes = array();
			while($row = mysql_fetch_assoc($rs)) {
				require_once("post/PostCommon.php");
				$vote = new Vote(intval($row["vt_author"]), intval($row["vt_post"]), $row["vt_vote"] > 0);
				$vote->setCreationDate(time($row["vt_creationDate"]));
				$votes[] = $vote;
			}
			//echo "<p>" . serialize($votes) . "</p>"; //DEBUG;
			$this->setVotes($votes);
		}
		return $this;
	}
	
	/**
	 * Carica in this i report recuperati dal database per questo post (deve avere un ID!).
	 */
	function loadReports() {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Report");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("rp_post"),Operator::$UGUALE,$this->getID())),
													 array()));
		if($rs !== false) {
			$reports = array();
			while($row = mysql_fetch_assoc($rs)) {
				require_once("common.php");
				$report = new Report(intval($row["rp_user"]), intval($row["rp_post"]), $row["rp_report"]);
				$report->setID($row["rp_id"]);
				$reports[] = $report;
			}
			$this->setReports($reports);
		}
		return $this;
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "Post (ID = " . $this->getID() .
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
		for($i=0; $i<count($this->getReports); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->reports[$i];
		}
		$s.= "))";
		return $s;
	}
}
?>
