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
														 array(new WhereConstraint($table->getColumn("cm_ID"),Operator::$UGUALE,$this->getID())),
														 array()));
			//echo "<br />" . $s; //DEBUG
			while($row = mysql_fetch_assoc($rs)) {
				$this->creationDate = time($row["cm_creationDate"]);
				//echo "<br />" . serialize($row["cm_creationDate"]); //DEBUG
				break;
			}
			//echo "<br />" . $this; //DEBUG
			require_once("common.php");
			LogManager::addLogEntry($this->getAuthor(), LogManager::$INSERT, $this);
			return $this->ID;
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
														 array(new WhereConstraint($table->getColumn("cm_ID"),Operator::$UGUALE,$this->getID()))));
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
	 * Crea un commento caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Comment().
	 *
	 * param $id: l'ID del commento da caricare.
	 * return: il commento caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($id) {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Comment");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("cm_ID"),Operator::$UGUALE,$id)),
													 array()));
		if($rs !== false && mysql_num_rows($rs) == 1) {
			while($row = mysql_fetch_assoc($rs)) {
				$data = array("comment" => $row["cm_comment"],
							  "author" => intval($row["cm_author"]),
							  "post" => intval($row["cm_post"]));
				$c = new Comment($data);
				$c->setID(intval($row["cm_ID"]));
				$c->setCreationDate(time($row["cm_creationDate"]));
				break;
			}
			$c->loadReports();
			return $c;
		} else{
			$GLOBALS["query_error"] = "NOT FOUND";
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
														 array(new WhereConstraint($table->getColumn("vt_author"),Operator::$UGUALE,$this->getAuthor()),
															   new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$this->getPost())),
														 array()));
			//echo "<br />" . $s; //DEBUG
			while($row = mysql_fetch_assoc($rs)) {
				$this->creationDate = time($row["vt_creationDate"]);
				//echo "<br />" . serialize($row["vt_creationDate"]); //DEBUG
				break;
			}
			//echo "<br />" . $this; //DEBUG
			require_once("common.php");
			LogManager::addLogEntry($this->getAuthor(), LogManager::$INSERT, $this);
			return $this->creationDate;
		}
		return false;
	}
		
	function update() {
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable("Vote");
			$rs = $q->execute($s = $q->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn("vt_author"),Operator::$UGUALE,$this->getAuthor()),
															   new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$this->getPost())),
														 array()));
			//echo "<br />" . $s; //DEBUG
			$data = array();
			while($row = mysql_fetch_assoc($rs)) {
				//cerco le differenze e le salvo.
				if($row["vt_vote"] != $this->getVote())
					$data["vt_vote"] = $this->getVote();
				break;
			}
			//echo "<br />" . serialize($data); //DEBUG
			
			$rs = $q->execute($s = $q->generateUpdateStm($table,
														 $data,
														 array(new WhereConstraint($table->getColumn("vt_author"),Operator::$UGUALE,$this->getAuthor()),
															   new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$this->getPost()))));
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . $rs; //DEBUG
			if(mysql_affected_rows() == 0)
				return false;
			
			//echo "<br />" . $this; //DEBUG
			LogManager::addLogEntry($this->getAuthor(), LogManager::$UPDATE, $this);
			return $this;
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
														 array(new WhereConstraint($table->getColumn("vt_author"),Operator::$UGUALE,$this->getAuthor()),
															   new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$this->getPost()))));
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
	 * Crea un voto caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Vote().
	 *
	 * param $id: l'ID del voto da caricare.
	 * return: il voto caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($author, $post) {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Vote");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("vt_author"),Operator::$UGUALE,$author),
														   new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$post)),
													 array()));
		if($rs !== false && mysql_num_rows($rs) == 1) {
			while($row = mysql_fetch_assoc($rs)) {
				$v = new Vote(intval($row["vt_author"]), intval($row["vt_post"]), $row["vt_vote"] > 0);
				$v->setCreationDate(time($row["cm_creationDate"]));
				return $v;
			}
		} else{
			$GLOBALS["query_error"] = "NOT FOUND";
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