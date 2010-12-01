<?php

class Post {
	const DEFAULT_CATEGORY = "News";	//TODO: trovare un valore per questo…
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
	protected $votes;					// array di oggetto VOTO
	protected $content;					// testo del contenuto o indirizzo del video o foto o array di essi
	protected $visible;					// boolean
	protected $place;					// 
	protected $reports;					// array di oggetti Report
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
	 * place: db v0 id di un luogo in db, db v0.2 stringa (secondo le regole delle API Google)
	 * 
	 * @return: l'articolo creato.
	 */
	function __construct($data) {
		if(!is_array($data) && is_numeric($data)) {
			$data = array("ID" => $data);
		}
		
		$this->setCreationDate($_SERVER["REQUEST_TIME"]);
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
			$this->setPlace($data["place"]);
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
		if(!isset($this->comments) || !is_array($this->comments))
			return array();
		return $this->comments;
	}
	/**
	 * @deprecated il voto viene caricato attraverso getAvgVote()
	 */
	function getVotes() {
		return $this->votes;
	}
	function getAccessCount() {
		return $this->accessCount;
	}
	
	/**
	 * Recupera il voto come la media tra tutti i valori salvati nella tabella Vote.
	 * Sta al livello di vista decidere come visualizare questo parametro: stelline, valore scritto, faccine...
	 * @return: un valore float.
	 */
	private $avgVote = null;
	function getAvgVote() {
		if(!is_null($this->avgVote)) return $this->avgVote;
		
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineVoteColumns();
			$table = Query::getDBSchema()->getTable(TABLE_VOTE);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(VOTE_POST),Operator::EQUAL,$this->getID())),
														 array("avg" => $table->getColumn(VOTE_VOTE))));
			//echo "<p>" . $s . "</p>"; //DEBUG;
			if($db->num_rows() == 1) {
				$row = $db->fetch_row();
				//echo serialize($row);
				$this->avgVote = floatval($row[0]);
				return $this->avgVote;
			} else $db->display_error("Post::getAvgVote()");
		} else $db->display_connect_error("Post::getAvgVote()");
		return false;
	}
	function getReports() {
		return $this->reports;
	}
	function getPlace() {
		return $this->place;
	}
	function getAuthorName() {
		require_once("user/UserManager.php");
		if(is_null($this->getAuthor()))
			return "Anonimous";
		$u = UserManager::loadUser($this->getAuthor(), false);
		if(!is_null($u->getNickname()))
			return $u->getNickname();
		return $this->getAuthor();
	}
	/**
	 * Restituisce il permalink dell'articolo. Può essere forzato il caricamento del pramalink dai dati.
	 *
	 * @param $reload: true o false (deafult false). Se true ricarica il permalink dai dati ma non ne salva lo stato.
	 */
	function getPermalink($reload = false) {
		if($reload) {
			$s = $this->getRelativePermalink();
			return $s;
		} else {
			if(isset($this->permalink) && !is_null($this->permalink))
			   return $this->permalink;
			else
				return $this->getPermalink(true);
		}
	}
	
	/**
	 * Secondo la convenzione decisa e approvata da me� XD
	 * il permalink �: /Post/%nome autore%/%data di creazione%/%titolo%
	 */
	private function getRelativePermalink() {
		require_once("common.php");
		$s = "Post/";
		$s.= Filter::textToPermalink($this->getAuthorName());
		$s.= "/";
		if(isset($this->creationDate)) {
			$s.= date("Y-m-d", $this->getCreationDate());
			$s.= "/";
		}
		$s.= Filter::textToPermalink($this->getTitle());
		return $s;
	}
	function getFullPermalink() {
		require_once("file_manager.php");
		$s = FileManager::getServerPath();
		$s.= dirname($_SERVER["PHP_SELF"]);
		$s.= "/";
		$s.= $this->getPermalink();
		return $s;
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
	function setPermalink($permalink, $update = false) {
		$this->permalink = $permalink;
		if($update) $this->update();
		return $this;
	}
	function setAccessCount($ac) {
		$this->accessCount = $ac;
		return $this;
	}
	
	/**
	 * Salva il post e le sue dipendenze nel database.
	 * Le dipendenze salvate sono quelle che dipendono dall'autore ovvero: tag e categorie.
	 * Potrebbe salvare alcune tuple in Tag.
	 *
	 * @return: ID della tupla inserita (o aggiornata), FALSE se c'è un errore.
	 */
	function save() {
		require_once("post/PostCommon.php");
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); definePostColumns();
			$table = Query::getDBSchema()->getTable(TABLE_POST);
			$data = array(POST_TYPE => $this->getType());
			if(isset($this->title) && !is_null($this->getTitle()))
				$data[POST_TITLE] = $this->getTitle();
			if(isset($this->subtitle) && !is_null($this->getSubtitle()))
				$data[POST_SUBTITLE] = $this->getSubtitle();
			if(isset($this->headline) && !is_null($this->getHeadline()))
				$data[POST_HEADLINE] = $this->getHeadline();
			if(isset($this->tags) && !is_null($this->getTags()))
				$data[POST_TAGS] = $this->getTags();
			if(isset($this->categories) && !is_null($this->getCategories())) {
				// check sulle categorie, eliminazione di quelle che non esistono nel sistema, se vuoto inserimento di quella di default
				$new_cat = CategoryManager::filterWrongCategories(explode(",", $this->getCategories()));
				if(!isset($this->categories) || is_null($this->getCategories()) || count($new_cat) == 0)
					$new_cat[] = self::DEFAULT_CATEGORY;
				$this->setCategories(Filter::arrayToText($new_cat));
				$data[POST_CATEGORIES] = $this->getCategories();
			}
			if(isset($this->content) && !is_null($this->getContent())) {
				if($this->type == "post" || $this->type == "news" || $this->type == "videoreportage")
					$data[POST_CONTENT] = $this->getContent();
				else
					$data[POST_CONTENT] = serialize($this->getContent());
			}
			if(isset($this->visible) && !is_null($this->isVisible()))
				$data[POST_VISIBLE] = $this->isVisible() ? 1 : 0;
			if(isset($this->author) && !is_null($this->getAuthor()))
				$data[POST_AUTHOR] = $this->getAuthor();
			if(isset($this->place) && !is_null($this->getPlace()))
				$data[POST_PLACE] = $this->getPlace();
			$data[POST_PERMALINK] = $this->getPermalink();
			if(self::permalinkExists($this->getPermalink())) //finché esiste già un permalink del genere, ne genero uno radfom.
				$data[POST_PERMALINK] = $this->getPermalink() . "(" . rand(1024, $_SERVER["REQUEST_TIME"]) . ")";
			$data[POST_CREATION_DATE] = date("Y-m-d G:i:s", $_SERVER["REQUEST_TIME"]);
			
			$rs = $db->execute($s = Query::generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . $db->affected_rows(); //DEBUG
			if($db->affected_rows() == 1) {
				$this->setID($db->last_inserted_id());
				//echo "<br />" . serialize($this->ID); //DEBUG
				$rs = $db->execute($s = Query::generateSelectStm(array($table),
															 array(),
															 array(new WhereConstraint($table->getColumn(POST_ID),Operator::EQUAL,$this->getID())),
															 array()),
								  $table->getName(), $this);
				//echo "<br />" . serialize($rs); //DEBUG
				if($db->num_rows() == 1) {
					$row = $db->fetch_result();
					//echo "<br />" . $row; //DEBUG
					$this->setPermalink($row[POST_PERMALINK]);
					$this->setModificationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[POST_CREATION_DATE])));
					
					//salvo i tag che non esistono
					if(isset($data[POST_TAGS]) && !is_null($data[POST_TAGS]) && trim($data[POST_TAGS]) != "")
						TagManager::createTags(explode(",", $data[POST_TAGS]));
					
					//echo "<br />" . serialize($row[POST_CREATION_DATE]); //DEBUG
					//echo "<br />" . $this; //DEBUG
					return $this->ID;
				} else $db->display_error("Post::save()");
			} else $db->display_error("Post::save()");
		} else $db->display_connect_error("Post::save()");
		return false;
	}
	
	/**
	 * Aggiorna il post e le sue dipendenze nel database.
	 * Le dipendenze aggiornate sono quelle che dipendono dall'autore ovvero: tag e categorie
	 * Potrebbe salvare alcune tuple in Tag.
	 *
	 * @return: modificationDate o FALSE se c'è un errore.
	 */
	function update() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); definePostColumns();
			$table = Query::getDBSchema()->getTable(TABLE_POST);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(POST_ID),Operator::EQUAL,$this->getID())),
														 array()),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			$data = array();
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				//cerco le differenze e le salvo.
				if($row[POST_TITLE] != $this->getTitle())
					$data[POST_TITLE] = $this->getTitle();
				if($row[POST_SUBTITLE] != $this->getSubtitle())
					$data[POST_SUBTITLE] = $this->getSubtitle();
				if($row[POST_HEADLINE] != $this->getHeadline())
					$data[POST_HEADLINE] = $this->getHeadline();
				if($row[POST_CONTENT] != $this->getContent()) {
					if($this->type == "post" || $this->type == "news" || $this->type == "videoreportage")
						$data[POST_CONTENT] = $this->getContent();
					else
						$data[POST_CONTENT] = serialize($this->getContent());
				}
				if($row[POST_PLACE] != $this->getPlace())
					$data[POST_PLACE] = $this->getPlace();
				if($row[POST_TAGS] != $this->getTags())
					$data[POST_TAGS] = $this->getTags();
				if($row[POST_CATEGORIES] != $this->getCategories()) {
					// check sulle categorie, eliminazione di quelle che non esistono nel sistema, se vuoto inserimento di quella di default
					$new_cat = CategoryManager::filterWrongCategories(explode(",", $this->getCategories()));
					if(count($new_cat) == 0)
						$new_cat[] = self::DEFAULT_CATEGORY;
					$this->setCategories(Filter::arrayToText($new_cat));
					$data[POST_CATEGORIES] = $this->getCategories();
				}
				settype($row[POST_VISIBLE], "boolean");
				if($row[POST_VISIBLE] !== $this->isVisible())
					$data[POST_VISIBLE] = $this->isVisible() ? 1 : 0;
				if($row[POST_PERMALINK] != $this->getPermalink()) {
					if(self::permalinkExists($this->getPermalink())) return false;
					$data[POST_PERMALINK] = $this->getPermalink();
				}
					
				if(count($data) == 0) return $this->getModificationDate();
				$data[POST_MODIFICATION_DATE] = date("Y/m/d G:i:s", $_SERVER["REQUEST_TIME"]); // se mi dicono di fare l'update, cambio modificationDate
				//echo "<br />" . serialize($data); //DEBUG
				
				$rs = $db->execute($s = Query::generateUpdateStm($table,
															 $data,
															 array(new WhereConstraint($table->getColumn(POST_ID),Operator::EQUAL,$this->getID()))),
								  $table->getName(), $this);
				//echo "<br />" . $s; //DEBUG
				//echo "<br />" . mysql_affected_rows(); //DEBUG
				if($db->affected_rows() == 1) {
					//salvo i tag che non esistono
					if(isset($data[POST_TAGS]) && !is_null($data[POST_TAGS]) && trim($data[POST_TAGS]) != "")
						TagManager::createTags(explode(",", $data[POST_TAGS]));
					
					//echo "<br />" . $this; //DEBUG
					return $this->getModificationDate();
				} else $db->display_error("Post::update()");
			} else $db->display_error("Post::update()");
		} else $db->display_connect_error("Post::update()");
		return false;
	}
	
	/**
	 * Cancella il post dal database.
	 * Con le Foreign Key e ON DELETE, anche le dipendenze dirette vengono cancellate.
	 * Non vengono cancellate le dipendenze nelle Collection.
	 *
	 * @return: l'oggetto cancellato o FALSE se c'è un errore.
	 */
	function delete() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); definePostColumns();
			$table = Query::getDBSchema()->getTable(TABLE_POST);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(POST_ID),Operator::EQUAL,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $db->affected_rows() . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Post::delete()");
		} else $db->display_connect_error("Post::delete()");
		return false;
	}
	
	static function permalinkExists($permalink) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); definePostColumns();
			$table = Query::getDBSchema()->getTable(TABLE_POST);
			$rs = $db->execute($s = Query::generateSelectStm(array($table), array(),
															 array(new WhereConstraint($table->getColumn(POST_PERMALINK),Operator::EQUAL,$permalink)),
															 array("count" => 2)));
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				return $row["COUNT(*)"] > 0;
			} else $db->display_error("Post::permalinkExists()");
		} else $db->display_connect_error("Post::permalinkExists()");
		return false;
	}
	
	static function createFromDBResult($row, $loadComments = true) {
		if($row[POST_TYPE] == "news" || $row[POST_TYPE] == "post" || $row[POST_TYPE] == "videoreportage")
			$content = $row[POST_CONTENT];
		else
			$content = unserialize($row[POST_CONTENT]);
		$data = array("title" => $row[POST_TITLE],
					  "subtitle" => $row[POST_SUBTITLE],
					  "headline" => $row[POST_HEADLINE],
					  "author"=> intval($row[POST_AUTHOR]),
					  "tags" => $row[POST_TAGS],
					  "categories" => $row[POST_CATEGORIES],
					  "content" => $content,
					  "visible" => $row[POST_VISIBLE] > 0,
					  "type" => $row[POST_TYPE],
					  "place" => $row[POST_PLACE]);
		require_once("post/PostCommon.php");
		require_once("post/collection/Collection.php");
		if($row[POST_TYPE] == PostType::NEWS)
			$p = new News($data);
		else if($row[POST_TYPE] == PostType::VIDEOREPORTAGE)
			$p = new VideoReportage($data);
		else if($row[POST_TYPE] == PostType::ALBUM)
			$p = new Album($data);
		else if($row[POST_TYPE] == PostType::MAGAZINE)
			$p = new Magazine($data);
		else if($row[POST_TYPE] == PostType::PHOTOREPORTAGE)
			$p = new PhotoReportage($data);
		else if($row[POST_TYPE] == PostType::PLAYLIST)
			$p = new Playlist($data);
		else if($row[POST_TYPE] == PostType::COLLECTION)
			$p = new Collection($data);
		else
			$p = new Post($data);
		$p->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[POST_CREATION_DATE])));
		$p->setID(intval($row[POST_ID]));
		if(!is_null($row[POST_MODIFICATION_DATE]))
			$p->setModificationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[POST_MODIFICATION_DATE])));
		else $p->setModificationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[POST_CREATION_DATE])));
		if($loadComments) $p->loadComments();
		
		$user = Session::getUser();
		if($user !== false && $user->getRole() == "admin")
			$p->loadReports();
		
		$p->setPermalink($row[POST_PERMALINK]);
		
		require_once("common.php");
		$p->setAccessCount(LogManager::getAccessCount("Post", $p->getID()));
		
		return $p;
	}
	
	/**
	 * Crea un post caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Post().
	 *
	 * @param $id: l'ID del post da caricare.
	 * @return: il post caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($id, $loadComments = true) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); definePostColumns();
			$table = Query::getDBSchema()->getTable(TABLE_POST);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(POST_ID),Operator::EQUAL,$id)),
														 array()),
							  $table->getName(), null);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			//echo "<p>" . $db->num_rows() . "</p>"; //DEBUG
			if($db->num_rows() == 1) {
				//echo serialize($db->fetch_result()); //DEBUG
				$row = $db->fetch_result();
				$p = self::createFromDBResult($row, $loadComments);
				//echo "<p>" .$p ."</p>";
				return $p;
			} else $db->display_error("Post::loadFromDatabase()");
		} else $db->display_connect_error("Post::loadFromDatabase()");
		return false;
	}
	
	static function loadByPermalink($permalink, $loadComments = true) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); definePostColumns();
			$table = Query::getDBSchema()->getTable(TABLE_POST);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(POST_PERMALINK),Operator::EQUAL,$permalink)),
														 array()),
							  $table->getName(), null);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			//echo "<p>" . $db->num_rows() . "</p>"; //DEBUG
			if($db->num_rows() == 1) {
				//echo serialize($db->fetch_result()); //DEBUG
				$row = $db->fetch_result();
				$p = self::createFromDBResult($row, $loadComments);
				//echo "<p>" .$p ."</p>";
				return $p;
			} else $db->display_error("Post::loadByPermalink()");
		} else $db->display_connect_error("Post::loadByPermalink()");
		return false;
	}
	
	/**
	 * Carica in this i commenti recuperati dal database per questo post (deve avere un ID!).
	 */
	function loadComments() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineCommentColumns();
			$table = Query::getDBSchema()->getTable(TABLE_COMMENT);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(COMMENT_POST),Operator::EQUAL,$this->getID())),
														 array()),
							  $table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG;
			if($db->num_rows() > 0) {
				$comm = array();
				while($row = $db->fetch_result()) {
					require_once("post/PostCommon.php");
					$com = new Comment(array("author" => intval($row[COMMENT_AUTHOR]),
											 "post" => intval($row[COMMENT_POST]),
											 "comment" => $row[COMMENT_COMMENT]));
					$com->setID($row[COMMENT_ID])->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[COMMENT_CREATION_DATE])));
					$comm[] = $com;
				}
				$this->setComments($comm);
			} else {
				if($db->errno())
					$db->display_error("Post::loadComments()");
			}
		} else $db->display_connect_error("Post::loadComments()");
		return $this;
	}
	
	/**
	 * @deprecated il voto viene caricato attraverso getAvgVote()
	 * Carica in this i voti recuperati dal database per questo post (deve avere un ID!).
	 */
	function loadVotes() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineVoteColumns();
			$table = Query::getDBSchema()->getTable(TABLE_VOTE);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(VOTE_POST),Operator::EQUAL,$this->getID())),
														 array()),
							  $table->getName(), $this);
			//echo "<p>" . $s . "</p>"; //DEBUG;
			if($db->num_rows() > 0) {
				$votes = array();
				while($row = $db->fetch_result()) {
					require_once("post/PostCommon.php");
					$vote = new Vote(intval($row[VOTE_AUTHOR]), intval($row[VOTE_POST]), $row[VOTE_VOTE] > 0);
					$vote->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[VOTE_CREATION_DATE])));
					$votes[] = $vote;
				}
				//echo "<p>" . serialize($votes) . "</p>"; //DEBUG;
				$this->setVotes($votes);
			} else {
				if($db->errno())
					$db->display_error("Post::loadVotes()");
			}
		} else $db->display_connect_error("Post::loadVotes()");
		return $this;
	}
	
	/**
	 * Carica in this i report recuperati dal database per questo post (deve avere un ID!).
	 */
	function loadReports() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineReportColumns();
			$table = Query::getDBSchema()->getTable(TABLE_REPORT);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(REPORT_POST),Operator::EQUAL,$this->getID())),
														 array()),
							  $table->getName(), $this);
			if($rs !== false) {
				$reports = array();
				while($row = $db->fetch_result()) {
					require_once("common.php");
					$report = new Report(intval($row[REPORT_USER]), intval($row[REPORT_POST]), $row[REPORT_TEXT]);
					$report->setID($row[REPORT_ID]);
					$reports[] = $report;
				}
				$this->setReports($reports);
			} else {
				if($db->errno())
					$db->display_error("Post::loadReports()");
			}
		} else $db->display_connect_error("Post::loadReports()");
		return $this;
	}
	
	/**
	 * TODO Da testare
	 * @deprecated troppo lunga da implementare�
	 */
	function equals($post) {
		if(is_a($post, "Post") || get_parent_class($post) == "post") {
			if($this->getTitle() == $post->getTitle() &&
				$this->getSubtitle() == $post->getSubtitle() &&
				$this->getHeadline() == $post->getHeadline() &&
				$this->getAuthor() == $post->getAuthor() &&
				$this->getTags() == $post->getTags() &&
				$this->getCategories() == $post->getCategories() &&
				$this->getContent() == $post->getContent() &&
				$this->isVisible() == $post->isVisible() &&
				$this->getComments() == $post->getComments() && //TODO controllare il contenuto di comments
				$this->getContent() == $post->getContent() && //TODO controllare il contenuto di contents
				$this->getCreationDate() == $post->getCreationDate() &&
				$this->getID() == $post->getID() &&
				$this->getModificationDate() == $post->getModificationDate() &&
				$this->getPlace() == $post->getPlace() &&
				$this->getReports() == $post->getReports() && //TODO controllare il contenuto di reports
				$this->getType() == $post->getType() &&
				$this->getVotes() == $podt->getVotes()) //TODO controllare il contenuto di votes
				return true;
		}
		return false;
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
		for($i=0; $i<count($this->getReports()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->reports[$i];
		}
		$s.= "))";
		return $s;
	}
}

class VideoReportage extends Post {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::VIDEOREPORTAGE);
	}
	
	/**
	 * @Override
	 */
	function setContent($content) {
		require_once("common.php");
		if($content->getType() == ResourceType::$VIDEO)
			$this->content = $content;
		return $this;
	}
}

class News extends Post {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::NEWS);
	}
}
?>
