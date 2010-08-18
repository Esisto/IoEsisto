<?php
	class PostType {
		static $PHOTOREPORTAGE = "photoreportage";
		static $VIDEOREPORTAGE = "videoreportage";
		static $NEWS = "news";
		static $COLLECTION = "collection";
	}
	
	class SavingMode {
		static $INSERT = "insert";
		static $UPDATE = "update";
	}
	
	class Post {
		protected $ID;				// id recuperato dal database
		protected $postType;		//
		protected $title;			//
		protected $subtitle;		//
		protected $headline; 		// occhiello
		protected $author;			//
		protected $creationDate;	//
		protected $tags = array();			// array di oggetti TAG
		protected $categories = array();		// array di oggetti CATEGORY
		protected $comments = array();		// array di oggetti COMMENTO
		protected $votes = array();			// array di oggetto VOTO
		protected $content;		// testo del contenuto o indirizzo del video o foto o array di essi
		protected $visible;		// boolean
		protected $reports = array();		// array di oggetti Report
		
		function __construct($data) {
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
			$this->setCreationDate(time());
		}
		
		function addComment($comment) {
			$this->comments[] = $comment;
			
			$this->save(SavingMode::$UPDATE);
			return $this;
		}
		
		function addVote($vote) {
			$this->votes[] = $vote;
			
			$this->save(SavingMode::$UPDATE);
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
			return $this->postType;
		}
		function getComments() {
			return $this->comments;
		}
		function getVotes() {
			return $this->votes;
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
		function setPostType($type) {
			$this->postType = $type;
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
		
		/**
		 * Salva il post nel database.
		 * 
		 * param savingMode: uno dei valori della classe SavingMode.
		 * se INSERT: crea una nuova tupla in Post.
		 * se UPDATE: confronta il Post con quello presente nel database e aggiorna le differenze.
		 *
		 * return: ID della tupla inserita (o aggiornata), FALSE se c'è un errore.
		 */
		function save($savingMode) {
			if($savingMode == SavingMode::$INSERT) {
				require_once("query.php");
				if(!isset($GLOBALS["q"]))
					$q = new Query();
				if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
					$dbs = $q->getDBSchema();
					$table = $dbs->getTable("Post");
					$data = array("ps_type" => $this->getType(),
								  "ps_title" => $this->getTitle(),
								  "ps_subtitle" => $this->getSubtitle(),
								  "ps_headline" => $this->getHeadline(),
								  "ps_content" => $this->getContent(),
								  "ps_visible" => $this->isVisible() ? 1 : 0,
								  "ps_author" => $this->getAuthor()/*,
								  "ps_place" => $this->getPlace()*/ //TODO Non ancora implementato.
								  );
					$rs = $q->execute($s = $q->generateInsertStm($table,$data));
					//echo "<br />" . $s; //DEBUG
					//echo "<br />" . serialize($rs); //DEBUG
					$this->ID = mysql_insert_id();
					//echo "<br />" . serialize($this->ID); //DEBUG
					$rs = $q->execute($s = $q->generateSelectStm(array($table),
																 array(),
																 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$this->ID)),
																 array()));
					//echo "<br />" . $s; //DEBUG
					while($row = mysql_fetch_assoc($rs)) {
						$this->setCreationDate(time($row["ps_creationDate"]));
						//echo "<br />" . serialize($row["ps_creationDate"]); //DEBUG
						break;
					}
					//TODO inserire tags, categories e place.
					//echo "<br />" . $this; //DEBUG
					return $this->ID;
				}
				return false;
			} else if($savingMode == SavingMode::$UPDATE) {
				
				// TODO
				return false;	
			}
			
			return false;
		}
		
		function delete() {
			require_once("query.php");
			if(!isset($GLOBALS["q"]))
				$q = new Query();
			if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
				$dbs = $q->getDBSchema();
				$table = $dbs->getTable("Post");
				$rs = $q->execute($s = $q->generateDeleteStm($table,
															 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$this->ID))));
				//echo "<br />" . $s; //DEBUG
				if(mysql_affected_rows() == 1)
					return $this;
			}
			return false;
		}
		
		/**
		 * @Override
		 */
		function __toString() {
			$s = "Post (ID = " . $this->ID .
				 " | postType = " . $this->postType .
				 " | title = " . $this->title .
				 " | subtitle = " . $this->subtitle .
				 " | headline = " . $this->headline .
				 " | author = " . $this->author .
				 " | creationDate = " . date("d/m/Y G:i:s", $this->creationDate) .
				 " | tags = (";
			for($i=0; $i<count($this->tags); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->tags[$i];
			}
			$s.= ") | categories = (";
			for($i=0; $i<count($this->categories); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->categories[$i];
			}
			$s.= ") | comments = (";
			for($i=0; $i<count($this->comments); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->comments[$i];
			}
			$s.= ") | votes = (";
			for($i=0; $i<count($this->votes); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->votes[$i];
			}
			$s.= ") | content = " . $this->content .
				 " | visible = " . $this->visible .
				 " | reports = (";
			for($i=0; $i<count($this->reports); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->reports[$i];
			}
			$s.= "))";
			return $s;
		}
	}
	
	class News extends Post {
		
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$NEWS);
		}
	}
	
	class PhotoReportage extends Post {
		
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$PHOTOREPORTAGE);
		}
		
		/**
		 * @Override
		 */
		function setContent($content) {
			if(!is_array($content))
				$content = array($content);
			$this->content = $content;
			return $this;
		}
		
		/**
		 * @Override
		 */
		function __toString() {
			$s = "Post (ID = " . $this->ID .
				 " | postType = " . $this->postType .
				 " | title = " . $this->title .
				 " | subtitle = " . $this->subtitle .
				 " | headline = " . $this->headline .
				 " | author = " . $this->author .
				 " | creationDate = " . date("d/m/Y G:i:s", $this->creationDate) .
				 " | tags = (";
			for($i=0; $i<count($this->tags); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->tags[$i];
			}
			$s.= ") | categories = (";
			for($i=0; $i<count($this->categories); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->categories[$i];
			}
			$s.= ") | comments = (";
			for($i=0; $i<count($this->comments); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->comments[$i];
			}
			$s.= ") | votes = (";
			for($i=0; $i<count($this->votes); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->votes[$i];
			}
			$s.= ") | content = (";
			for($i=0; $i<count($this->content); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->content[$i];
			}
			$s.= ") | visible = " . $this->visible .
				 " | reports = (";
			for($i=0; $i<count($this->reports); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->reports[$i];
			}
			$s.= "))";
			return $s;
		}
		
		/**
		 * @Override
		 * Salva il fotoreportage nel database.
		 * 
		 * param savingMode: uno dei valori della classe SavingMode.
		 * se INSERT: crea una nuova tupla in Post.
		 * se UPDATE: confronta il Post con quello presente nel database e aggiorna le differenze.
		 */
		function save($savingMode) {
			if($savingMode == SavingMode::$INSERT) {
				require_once("query.php");
				if(!isset($GLOBALS["q"]))
					$q = new Query();
				if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
					$dbs = $q->getDBSchema();
					$table = $dbs->getTable("Post");
					$data = array("ps_type" => $this->getType(),
								  "ps_title" => $this->getTitle(),
								  "ps_subtitle" => $this->getSubtitle(),
								  "ps_headline" => $this->getHeadline(),
								  "ps_content" => serialize($this->getContent()),
								  "ps_visible" => $this->isVisible(),
								  "ps_author" => $this->getAuthor()/*,
								  "ps_place" => $this->getPlace()*/ //TODO Non ancora implementato.
								  );
					$rs = $q->execute($s = $q->generateInsertStm($table,$data));
					//echo "<br />" . $s; //DEBUG
					//echo "<br />" . serialize($rs); //DEBUG
					$this->ID = mysql_insert_id();
					//echo "<br />" . serialize($this->ID); //DEBUG
					$rs = $q->execute($s = $q->generateSelectStm(array($table),
																 array(),
																 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$this->ID)),
																 array()));
					//echo "<br />" . $s; //DEBUG
					while($row = mysql_fetch_assoc($rs)) {
						$this->setCreationDate(time($row["ps_creationDate"]));
						//echo "<br />" . serialize($row["ps_creationDate"]); //DEBUG
						break;
					}
					//TODO inserire tags, categories e place.
					//echo "<br />" . $this; //DEBUG
					return $this->ID;
				}
				return false;
			} else if($savingMode == SavingMode::$UPDATE) {
				
				// TODO
				return false;	
			}
			
			return false;
		}
	}
	
	class VideoReportage extends Post {
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$VIDEOREPORTAGE);
		}
	}
	
	class Category {
		private $name;
		
		/**
		 * @Override
		 */
		function __toString() {
			return $name;
		}
	}

	class Tag {
		private $name;
		
		/**
		 * @Override
		 */
		function __toString() {
			return $name;
		}
	}
	
	class Comment {
		private $ID;
		private $author;
		private $post;
		private $comment;
		private $creationDate;
		private $reports;
		
		function __construct($author,$post,$comment){
			$this->author = $author;
			$this->comment = $comment;
			$this->post = $post;
		}
		
		function getAuthor() {
			return $this->author;
		}
		
		function getPost() {
			return $this->post;
		}
		
		function getComment() {
			return $this->comment;
		}
		
		function getCreationDate() {
			return $this->creationDate;
		}
		
		function getID() {
			return $this->ID;
		}
		
		function setCreationDate($creationDate) {
			$this->creationDate = $creationDate;
			return $this;
		}
		
		function setID($id) {
			$this->ID = $id;
			return $this;
		}
		
		/**
		 * Salva il commento nel database.
		 * 
		 * param savingMode: uno dei valori della classe SavingMode.
		 * se INSERT: crea una nuova tupla in Post.
		 * se UPDATE: confronta il Post con quello presente nel database e aggiorna le differenze.
		 */
		function save($savingMode) {
			if($savingMode == SavingMode::$INSERT) {
				require_once("query.php");
				if(!isset($GLOBALS["q"]))
					$q = new Query();
				if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
					$dbs = $q->getDBSchema();
					$table = $dbs->getTable("Comment");
					$data = array("cm_comment" => $this->getComment(),
								  "cm_post" => $this->getPost(),
								  "cm_author" => $this->getAuthor());
					$rs = $q->execute($s = $q->generateInsertStm($table,$data));
					//echo "<br />" . $s; //DEBUG
					//echo "<br />" . serialize($rs); //DEBUG
					$this->ID = mysql_insert_id();
					//echo "<br />" . serialize($this->ID); //DEBUG
					$rs = $q->execute($s = $q->generateSelectStm(array($table),
																 array(),
																 array(new WhereConstraint($table->getColumn("cm_ID"),Operator::$UGUALE,$this->ID)),
																 array()));
					//echo "<br />" . $s; //DEBUG
					while($row = mysql_fetch_assoc($rs)) {
						$this->creationDate = time($row["cm_creationDate"]);
						//echo "<br />" . serialize($row["cm_creationDate"]); //DEBUG
						break;
					}
					//echo "<br />" . $this; //DEBUG
					return $this->ID;
				}
				return false;
			} else if($savingMode == SavingMode::$UPDATE) {
				
				// TODO
				return true;	
			}
			
			return false;
		}
		
		function delete() {
			require_once("query.php");
			if(!isset($GLOBALS["q"]))
				$q = new Query();
			if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
				$dbs = $q->getDBSchema();
				$table = $dbs->getTable("Comment");
				$rs = $q->execute($s = $q->generateDeleteStm($table,
															 array(new WhereConstraint($table->getColumn("cm_ID"),Operator::$UGUALE,$this->ID))));
				//echo "<br />" . $s; //DEBUG
				if(mysql_affected_rows() == 1)
					return $this;
			}
			return false;
		}
		
		/**
		 * @Override
		 */
		function __toString() {
			$s = "Comment (ID = " . $this->ID .
				 " | author = " . $this->author .
				 " | post = " . $this->post .
				 " | comment = " . $this->comment .
				 " | creationDate = " . date("d/m/Y G:i:s", $this->creationDate) .
				 " | reports = (";
			for($i=0; $i<count($this->reports); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->reports[$i];
			}
			$s.= "))";
			return $s;
		}
	}
	
	class Vote {
		private $author;
		private $post;
		private $vote;
		private $creationDate;
		
		function __construct($author,$post,$vote){
			$this->author = $author;
			$this->post = $post;
			$this->vote = $vote;
		}
		
		function getAuthor() {
			return $this->author;
		}
		
		function getPost() {
			return $this->post;
		}
		
		function getVote() {
			return $this->vote;
		}
		
		function getCreationDate() {
			return $this->vote;
		}
		
		function setCreationDate($creationDate) {
			$this->creationDate = $creationDate;
			return $this;
		}
		
		/**
		 * Salva il voto nel database.
		 * 
		 * param savingMode: uno dei valori della classe SavingMode.
		 * se INSERT: crea una nuova tupla in Post.
		 * se UPDATE: confronta il Post con quello presente nel database e aggiorna le differenze.
		 */
		function save($savingMode) {
			if($savingMode == SavingMode::$INSERT) {
				require_once("query.php");
				if(!isset($GLOBALS["q"]))
					$q = new Query();
				if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
					$dbs = $q->getDBSchema();
					$table = $dbs->getTable("Vote");
					$data = array("vt_vote" => $this->getVote(),
								  "vt_post" => $this->getPost(),
								  "vt_author" => $this->getAuthor());
					$rs = $q->execute($s = $q->generateInsertStm($table,$data));
					//echo "<br />" . $s; //DEBUG
					//echo "<br />" . serialize($rs); //DEBUG
					$rs = $q->execute($s = $q->generateSelectStm(array($table),
																 array(),
																 array(new WhereConstraint($table->getColumn("vt_author"),Operator::$UGUALE,$this->author),
																	   new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$this->post)),
																 array()));
					//echo "<br />" . $s; //DEBUG
					while($row = mysql_fetch_assoc($rs)) {
						$this->creationDate = time($row["vt_creationDate"]);
						//echo "<br />" . serialize($row["vt_creationDate"]); //DEBUG
						break;
					}
					//echo "<br />" . $this; //DEBUG
					return $this->creationDate;
				}
				return false;
			} else if($savingMode == SavingMode::$UPDATE) {
				
				// TODO
				return true;	
			}
			
			return false;
		}
		
		function delete() {
			require_once("query.php");
			if(!isset($GLOBALS["q"]))
				$q = new Query();
			if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
				$dbs = $q->getDBSchema();
				$table = $dbs->getTable("Vote");
				$rs = $q->execute($s = $q->generateDeleteStm($table,
															 array(new WhereConstraint($table->getColumn("vt_author"),Operator::$UGUALE,$this->author),
																   new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$this->post))));
				//echo "<br />" . $s; //DEBUG
				if(mysql_affected_rows() == 1)
					return $this;
			}
			return false;			
		}
		
		/**
		 * @Override
		 */
		function __toString() {
			$s = "Vote (author = " . $this->author .
				 " | post = " . $this->post .
				 " | comment = " . $this->vote .
				 ")";
			return $s;
		}
	}
?>
