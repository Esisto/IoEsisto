<?php

class PostType {
	static $PHOTOREPORTAGE = "photoreportage";
	static $VIDEOREPORTAGE = "videoreportage";
	static $NEWS = "news";
	static $COLLECTION = "collection";
	static $ALBUM = "album";
	static $MAGAZINE = "magazine";
	static $PLAYLIST = "playlist";
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
	
	
	function __construct($data){
		$this->author = $data["author"];
		$this->post = $data["post"];
		$this->comment = $data["comment"];
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
	
	function loadReports() {
		//TODO
	}
	
	/**
	 * Salva il commento nel database.
	 * 
	 * param savingMode: uno dei valori della classe SavingMode.
	 * se INSERT: crea una nuova tupla in Post.
	 * se UPDATE: confronta il Post con quello presente nel database e aggiorna le differenze.
	 */
	function save() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $_SESSION["q"]->getDBSchema();
			define_tables(); defineCommentColumns();
			$table = $dbs->getTable(TABLE_COMMENT);
			$data = array(COMMENT_COMMENT => $this->getComment(),
						  COMMENT_POST => $this->getPost(),
						  COMMENT_AUTHOR => $this->getAuthor());
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateInsertStm($table,$data),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			$this->ID = $_SESSION["q"]->last_inserted_id();
			//echo "<br />" . serialize($this->ID); //DEBUG
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::$UGUALE,$this->getID())),
														 array()),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			while($_SESSION["q"]->hasNext()) {
				$row = $_SESSION["q"]->next();
				$this->creationDate = time($row[COMMENT_CREATION_DATE]);
				//echo "<br />" . serialize($row[COMMENT_CREATION_DATE]); //DEBUG
				break;
			}
			//echo "<br />" . $this; //DEBUG
			return $this->ID;
		}
		return false;
	}
	
	function delete() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $_SESSION["q"]->getDBSchema();
			$table = $dbs->getTable(TABLE_COMMENT);
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::$UGUALE,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($_SESSION["q"]->affected_rows() == 1) {
				return $this;
			}
		}
		return false;
	}
	
	/**
	 * Crea un commento caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Comment().
	 *
	 * param $id: l'ID del commento da caricare.
	 * return: il commento caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($id) {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		$table = $_SESSION["q"]->getDBSchema()->getTable(TABLE_COMMENT);
		$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::$UGUALE,$id)),
													 array()),
						  $table->getName(), null);
		if($rs !== false && $_SESSION["q"]->num_rows() == 1) {
			while($_SESSION["q"]->hasNext()) {
				$row = $_SESSION["q"]->next();
				$data = array("comment" => $row[COMMENT_COMMENT],
							  "author" => intval($row[COMMENT_AUTHOR]),
							  "post" => intval($row[COMMENT_POST]));
				$c = new Comment($data);
				$c->setID(intval($row[COMMENT_ID]));
				$c->setCreationDate(time($row[COMMENT_CREATION_DATE]));
				break;
			}
			$c->loadReports();
			return $c;
		} else{
			$GLOBALS["query_error"] = NOT_FOUND;
			return false;
		}
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
	function save() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $_SESSION["q"]->getDBSchema();
			define_tables(); defineVoteColumns();
			$table = $dbs->getTable(TABLE_VOTE);
			$data = array(VOTE_VOTE => $this->getVote(),
						  VOTE_POST => $this->getPost(),
						  VOTE_AUTHOR => $this->getAuthor());
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateInsertStm($table,$data),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$UGUALE,$this->getAuthor()),
															   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$UGUALE,$this->getPost())),
														 array()),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			while($_SESSION["q"]->hasNext()) {
				$row = $_SESSION["q"]->next();
				$this->creationDate = time($row[VOTE_CREATION_DATE]);
				//echo "<br />" . serialize($row[VOTE_CREATION_DATE]); //DEBUG
				break;
			}
			//echo "<br />" . $this; //DEBUG
			return $this->creationDate;
		}
		return false;
	}
		
	function update() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $_SESSION["q"]->getDBSchema()->getTable(TABLE_VOTE);
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$UGUALE,$this->getAuthor()),
															   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$UGUALE,$this->getPost())),
														 array()),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			$data = array();
			while($_SESSION["q"]->hasNext()) {
				$row = $_SESSION["q"]->next();
				//cerco le differenze e le salvo.
				if($row[VOTE_VOTE] != $this->getVote())
					$data[VOTE_VOTE] = $this->getVote();
				break;
			}
			//echo "<br />" . serialize($data); //DEBUG
			
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateUpdateStm($table,
														 $data,
														 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$UGUALE,$this->getAuthor()),
															   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$UGUALE,$this->getPost()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . $rs; //DEBUG
			if($_SESSION["q"]->affected_rows() == 0)
				return false;
			
			//echo "<br />" . $this; //DEBUG
			return $this;
		}
		return false;
	}
	
	function delete() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $_SESSION["q"]->getDBSchema();
			$table = $dbs->getTable(TABLE_VOTE);
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$UGUALE,$this->getAuthor()),
															   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$UGUALE,$this->getPost()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($_SESSION["q"]->affected_rows() == 1) {
				return $this;
			}
		}
		return false;			
	}
	
	/**
	 * Crea un voto caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Vote().
	 *
	 * param $id: l'ID del voto da caricare.
	 * return: il voto caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($author, $post) {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		$table = $_SESSION["q"]->getDBSchema()->getTable(TABLE_VOTE);
		$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$UGUALE,$author),
														   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$UGUALE,$post)),
													 array()),
						  $table->getName(), null);
		if($rs !== false && $_SESSION["q"]->num_rows() == 1) {
			while($_SESSION["q"]->hasNext()) {
				$row = $_SESSION["q"]->next();
				$v = new Vote(intval($row[VOTE_AUTHOR]), intval($row[VOTE_POST]), $row[VOTE_VOTE] > 0);
				$v->setCreationDate(time($row[VOTE_CREATION_DATE]));
				return $v;
			}
		} else{
			$GLOBALS["query_error"] = NOT_FOUND;
			return false;
		}
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "Vote (author = " . $this->author .
			 " | post = " . $this->post .
			 " | vote = " . $this->vote .
			 ")";
		return $s;
	}
}

?>