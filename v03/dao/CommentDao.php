<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");

class CommentDao implements Dao {
	private $db;
	private $table_comment;
	
	function __construct() {
		$this->table_comment = Query::getDBSchema()->getTable(DB::TABLE_COMMENT);
		
		$this->db = new DBManager();
		if($this->db->connect_errno())
			$this->db->display_connect_error("CommentDao::__construct()");
	}
	
	static function loadFromDatabase($id) { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_COMMENT);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::EQUAL,$id)),
														 array()),
							  $table->getName(), null);
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				$data = array("comment" => $row[COMMENT_COMMENT],
							  "author" => intval($row[COMMENT_AUTHOR]),
							  "post" => intval($row[COMMENT_POST]));
				$c = new Comment($data);
				$c->setID(intval($row[COMMENT_ID]));
				$c->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[COMMENT_CREATION_DATE])));
				//$c->loadReports();
				return $c;
			} else $db->display_error("Comment::loadFromDatabase()");
		} else $db->display_connect_error("Comment::loadFromDatabase()");
		return false;
	}
	
	function loadAll($post) { //TODO
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
	
	function save() { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineCommentColumns();
			$table = Query::getDBSchema()->getTable(TABLE_COMMENT);
			$data = array(COMMENT_COMMENT => $this->getComment(),
						  COMMENT_POST => $this->getPost(),
						  COMMENT_AUTHOR => $this->getAuthor());
			$rs = $db->execute($s = Query::generateInsertStm($table,$data),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			if($db->affected_rows() == 1) {
				$this->ID = $db->last_inserted_id();
				//echo "<br />" . serialize($this->ID); //DEBUG
				$rs = $db->execute($s = Query::generateSelectStm(array($table),
															 array(),
															 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::EQUAL,$this->getID())),
															 array()),
								  $table->getName(), $this);
				//echo "<br />" . $s; //DEBUG
				if($db->num_rows() == 1) {
					$row = $db->fetch_result();
					$this->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[COMMENT_CREATION_DATE])));
					//echo "<br />" . serialize($row[COMMENT_CREATION_DATE]); //DEBUG
					//echo "<br />" . $this; //DEBUG
					return $this->ID;
				} else $db->display_error("Comment::save()");
			} else $db->display_error("Comment::save()");
		} else $db->display_connect_error("Comment::save()");
		return false;
	}
	
	function delete() { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineCommentColumns();
			$table = Query::getDBSchema()->getTable(TABLE_COMMENT);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::EQUAL,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Comment::delete()");
		} else $db->display_connect_error("Comment::delete()");
		return false;
	}
}
?>