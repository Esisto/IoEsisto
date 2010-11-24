<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");

class VoteDao implements Dao {
	private $db;
	private $table_vote;
	
	function __construct() {
		$this->table_vote = Query::getDBSchema()->getTable(DB::TABLE_VOTE);
		
		$this->db = new DBManager();
		if($this->db->connect_errno())
			$this->db->display_connect_error("VoteDao::__construct()");
	}
	
	function getVote($post) { //TODO
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
	
	function save($post, $author, $vote) { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineVoteColumns();
			$table = Query::getDBSchema()->getTable(TABLE_VOTE);
			$data = array(VOTE_VOTE => $this->getVote() ? 1 : 0,
						  VOTE_POST => $this->getPost(),
						  VOTE_AUTHOR => $this->getAuthor());
			$db->execute($s = Query::generateInsertStm($table,$data),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			if($db->affected_rows() == 1) {
				$db->execute($s = Query::generateSelectStm(array($table),
															 array(),
															 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::EQUAL,$this->getAuthor()),
																   new WhereConstraint($table->getColumn(VOTE_POST),Operator::EQUAL,$this->getPost())),
															 array()),
								  $table->getName(), $this);
				//echo "<br />" . $s; //DEBUG
				if($db->num_rows() == 1) {
					$row = $db->fetch_result();
					$this->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[VOTE_CREATION_DATE])));
					//echo "<br />" . serialize($row[VOTE_CREATION_DATE]); //DEBUG
					//echo "<br />" . $this; //DEBUG
					return $this->creationDate;
				} else $db->display_error("Vote::save()");
			} else $db->display_error("Vote::save()");
		} else $db->display_connect_error("Vote::save()");
		return false;
	}
	
	function delete($author, $post) { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineVoteColumns();
			$table = Query::getDBSchema()->getTable(TABLE_VOTE);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::EQUAL,$this->getAuthor()),
															   new WhereConstraint($table->getColumn(VOTE_POST),Operator::EQUAL,$this->getPost()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Vote::delete()");
		} else $db->display_connect_error("Vote::delete()");
		return false;			
	}
}
?>