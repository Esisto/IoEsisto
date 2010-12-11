<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");

class VoteDao implements Dao {
	const DEFAULT_VOTE = 0;
	
	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_VOTE);
	}
	
	function getVote($post) {	
		parent::load($post);
		if(!is_subclass_of($post, "Post"))
			throw new Exception("Attenzione! Il parametro di ricerca non è un post.");
		
		$rs = $this->db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($this->table->getColumn(DB::VOTE_POST),Operator::EQUAL,intval($post->getID()))),
														 array("avg" => $this->table->getColumn(VOTE_VOTE))));
		
		if($db->num_rows() != 1)
			return self::DEFAULT_VOTE;
				
		$row = $db->fetch_row();
		$this->avgVote = floatval($row[0]);
		return $this->avgVote;
	}
	
	function quickLoad($post) {
		return $this->getVote($post);
	}
	
	function save($post, $author, $vote) {
		parent::save($post, "Post");
		parent::save($author, "User");
		settype($vote, "boolean");
		
		//elimino il vecchio voto
		$this->db->execute(Query::generateDeleteStm($this->table, 
													 array(new WhereConstraint($this->table->getColumn(DB::VOTE_AUTHOR),Operator::EQUAL,intval($author->getID()),
														   new WhereConstraint($this->table->getColumn(DB::VOTE_POST),Operator::EQUAL,intval($post->getID()))))),
							$this->table->getName(), $data);
		//non controllo se è stato cancellato perché può non esserci
		
		$data = array(DB::VOTE_VOTE => ($vote ? 1 : 0),
					  DB::VOTE_POST => intval($post->getID()),
					  DB::VOTE_AUTHOR => intval($author->getID()));
		$this->db->execute($s = Query::generateInsertStm($this->table, $data),
							$this->table->getName(), $data);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
			
		//carico il voto inserito.
		$this->db->execute($s = Query::generateSelectStm(array($this->table),
													 array(),
													 array(new WhereConstraint($this->table->getColumn(DB::VOTE_AUTHOR),Operator::EQUAL,intval($author->getID()),
														   new WhereConstraint($this->table->getColumn(DB::VOTE_POST),Operator::EQUAL,intval($post->getID())))),
													 array()), $this->table->getName(), $data);
		
		if($db->num_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		return true;
	}
	
	function delete($author, $post) {
		parent::delete($author, "User");
		parent::delete($author, "Post");
		
		$rs = $this->db->execute($s = Query::generateDeleteStm($this->table,
								 array(new WhereConstraint($this->table->getColumn(DB::VOTE_AUTHOR),Operator::EQUAL,intval($author->getID())),
								 		new WhereConstraint($this->table->getColumn(DB::VOTE_POST),Operator::EQUAL,intval($post->getID())))),
							  	 $this->table->getName(), array("Vote", intval($author->getID()), intval($post->getID())));
		
		if($db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore eliminando l'oggetto. Riprovare.");
		return true;
	}
	
	
	function updateState($comment) {
		return null;
	}
}
?>