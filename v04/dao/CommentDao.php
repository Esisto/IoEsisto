<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Comment.php");

class CommentDao extends Dao {
	const OBJECT_CLASS = "Comment";
	
	private $loadReports = false;
	
	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_COMMENT);
	}

	function setLoadReports($load) {
		settype($load, "boolean");
		$this->loadReports = $load;
		return $this;
	}
	
	function quickLoad($id) {
		$loadR = $this->loadReports; $this->loadReports = false;
		$p = null;
		try {
			$p = $this->load($id);
			$this->loadReports = $loadR;
		} catch(Exception $e) {
			$this->loadReports = $loadR;
			throw $e;
		}
		$return = $p;
	}
	
	function load($id) {
		parent::load($id);
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table),
														 array(),
														 array(new WhereConstraint($this->table->getColumn(DB::COMMENT_ID),Operator::EQUAL,intval($id))),
														 array()));
		
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato.");
		
		$row = $this->db->fetch_result();
		$c = new Comment($row[DB::COMMENT_COMMENT], intval($row[DB::COMMENT_POST]), intval($row[DB::COMMENT_AUTHOR]));
		$c->setID(intval($row[DB::COMMENT_ID]))->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::COMMENT_CREATION_DATE])));
		if($this->loadReports && AuthorizationManager::canUserDo(AuthorizationManager::READ_REPORTS, $c)) {
			require_once 'dao/ReportDao.php';
			$redao = new ReportDao();
			$redao->loadAll($c);
		}
		return $c;
	}
	
	function loadAll($post) {
		parent::load($post);
		if(!is_subclass_of($post, "Post"))
			throw new Exception("Attenzione! Il parametro di ricerca non è un post.");
		
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table),
														 array(),
														 array(new WhereConstraint($this->table->getColumn(DB::COMMENT_POST),Operator::EQUAL,intval($post->getID()))),
														 array()), $this->table->getName(), $this);
		
		$comm = array();
		if($this->db->num_rows() > 0) {
			while($row = $this->db->fetch_result()) {
				$c = new Comment($row[DB::COMMENT_COMMENT], intval($row[DB::COMMENT_POST]), intval($row[DB::COMMENT_AUTHOR]));
				$com->setID($row[DB::COMMENT_ID])->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::COMMENT_CREATION_DATE])));
				$comm[] = $com;
			}
		}
		
		return $post->setComments($comm);
	}
	
	function save($comm) {
		parent::save($comm, self::OBJECT_CLASS);
		$data = array(DB::COMMENT_COMMENT => $comm->getComment(),
					  DB::COMMENT_POST => $comm->getPost(),
					  DB::COMMENT_AUTHOR => $comm->getAuthor());
		$rs = $this->db->execute($s = Query::generateInsertStm($this->table, $data), $this->table->getName(), $comm);

		if($this->db->affected_rows() != 1)
			throw new Exception("Errore durante l'inserimento dell'oggetto.");
		//carico il post inserito.
		$c = $this->quickLoad(intval($this->db->last_inserted_id()));
		return $c;
	}
	
	function delete($comm) {
		parent::delete($comm, self::OBJECT_CLASS);
		$rs = $this->db->execute($s = Query::generateDeleteStm($this->table,
														 array(new WhereConstraint($this->table->getColumn(DB::COMMENT_ID),Operator::EQUAL,$comm->getID()))), $this->table->getName(), $comm);
		if($this->db->affected_rows() != 1)
			throw new Exception("Errore durante l'eliminazione dell'oggetto.");
		return $comm;
	}
	
	function exists($comment) {
		try {
			$c = $this->quickLoad($comment->getID());
			return is_subclass_of($c, self::OBJECT_CLASS);
		} catch(Exception $e) {
			return false;
		}
	}
	
	function updateState($comment) {
		parent::updateState($comment, $this->table, DB::COMMENT_ID);
	}
}
?>