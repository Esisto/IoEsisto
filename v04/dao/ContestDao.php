<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Contest.php");

class ContestDao extends Dao {
	const OBJECT_CLASS = "Contest";
	private $table_cs;
	private $loadSubscribers = true;
	private $loadWiners = true;
	
	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_CONTEST);
		$this->table_cs = Query::getDBSchema()->getTable(DB::TABLE_CONTEST_SUBSCRIBER);
	}
	
	function save($contest) {
		parent::save($contest, self::OBJECT_CLASS);
		
		$data = array();
		if(!is_null($contest->getTitle()))
			$data[DB::CONTEST_TITLE] = $contest->getTitle();
		if(!is_null($contest->getDescription()))
			$data[DB::CONTEST_DESCRIPTION] = $contest->getDescription();
		if(!is_null($contest->getRules()))
			$data[DB::CONTEST_RULES] = $contest->getRules();
		if(!is_null($contest->getPrizes()))
			$data[DB::CONTEST_PRIZES] = $contest->getPrizes();
		if(!is_null($contest->getStart()))
			$data[DB::CONTEST_START] = date("Y/m/d G:i:s", $contest->getStart());
		if(!is_null($contest->getEnd()))
			$data[DB::CONTEST_END] = date("Y/m/d G:i:s", $contest->getEnd());
		if(!is_null($contest->getSubscriberType()))
			$data[DB::CONTEST_TYPE_OF_SUBSCRIBER] = $contest->getSubscriberType();
		
		$this->db->execute(Query::generateInsertStm($this->table,$data), $table->getName(), $this);

		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		
		$c = $this->quickLoad($this->db->last_inserted_id());
		return $c;
	}
	
	function update($contest, $editor) {
		parent::update($resource, $editor, self::OBJECT_CLASS);
		
		$old = $this->quickLoad($contest->getID());
		if(is_null($old))
			throw new Exception("L'oggetto da modificare non esiste.");
		
		$data = array();
		if($old->getTitle() != $contest->getTitle())
			$data[DB::CONTEST_TITLE] = $contest->getTitle();
		if($old->getDescription() != $contest->getDescription())
			$data[DB::CONTEST_DESCRIPTION] = $contest->getDescription();
		if($old->getEnd() != $contest->getEnd())
			$data[DB::CONTEST_END] = $contest->getEnd();
		if($old->getPrizes() != $contest->getPrizes())
			$data[DB::CONTEST_PRIZES] = $contest->getPrizes();
		if($old->getRules() != $contest->getRules())
			$data[DB::CONTEST_RULES] = $contest->getRules();
		if($old->getStart() != $contest->getStart())
			$data[DB::CONTEST_START] = $contest->getStart();
		if($old->getSubscriberType() == $contest->getSubscriberType()) {
			if(time() < $contest->getStart()) //se il contest non è ancora partito, posso modificare il tipo di post accettato.
				$data[DB::CONTEST_TYPE_OF_SUBSCRIBER] = $contest->getSubscriberType();
			else
				throw new Exception("Non puoi modificare il contest una vola aperto.");
		}
				
		
		$this->db->execute(Query::generateUpdateStm($this->table, $data,
													array(new WhereConstraint($table->getColumn(CONTEST_ID),Operator::EQUAL,$this->getID()))),
							  $this->table->getName(), $contest);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
		
		return $contest;
	}
	
	function delete($contest) {
		parent::delete($contest, self::OBJECT_CLASS);
		
		//carico la risorsa, completa dei suoi derivati (che andrebbero persi).
		$loadS = $this->loadSubscribers; $this->setLoadSubscribers(true);
		$loadW = $this->loadWinners; $this->setLoadWinners(true);
		$c_complete = null;
		try {
			$c_complete = $this->load($contest->getID());
			$this->setLoadSubscribers($loadS);
			$this->setLoadWinners($loadW);
		} catch(Exception $e) {
			$this->setLoadSubscribers($loadS);
			$this->setLoadWinners($loadW);
			throw $e;
		}
		
		$this->db->execute(Query::generateDeleteStm($this->table,
													array(new WhereConstraint($this->table->getColumn(DB::CONTEST_ID),Operator::EQUAL,$contest->getID()))),
							$this->table->getName(), $contest);
		
		//salvo la risorsa nella storia.
		$this->saveHistory($c_complete, "DELETED");
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore eliminando il dato. Riprovare.");
		return $c_complete;
	}
	
	/**
	 * Crea un contest caricando i dati dal database.
	 * @param $id: l'ID del contest da caricare.
	 * @return: il contest caricato.
	 * @throws Exception se non trova il contest.
	 */
	function load($id) {
		parent::load($id);
		$this->db->execute(Query::generateSelectStm(array($this->table),
														 array(),
														 array(new WhereConstraint($this->table->getColumn(DB::CONTEST_ID),Operator::EQUAL,intval($id))),
														 array()));
			
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
			
		$row = $this->db->fetch_result();
		$c = $this->createFromDBRow($row);
		return $c;
	}
	
	function quickLoad($id) {
		$loadS = $this->loadSubscribers; $this->setLoadWinners(false);
		$loadW = $this->loadWinners; $this->setLoadSubscribers(false);
		$c = null;
		try {
			$c = $this->load($id);
			$this->loadSubscribers = $loadS;
			$this->loadWinners = $loadW;
		} catch(Exception $e) {
			$this->loadSubscribers = $loadS;
			$this->loadWinners = $loadW;
			throw $e;
		}
		return $c;
	}
	
	private function createFromDBRow($row) {
		$data = array("title" => $row[DB::CONTEST_TITLE],
					  "description" => $row[DB::CONTEST_DESCRIPTION],
					  "rules" => $row[DB::CONTEST_RULES],
					  "prizes"=> $row[DB::CONTEST_PRIZES],
					  "start" => date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::CONTEST_START])),
					  "end" => date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::CONTEST_END])),
					  "subscriberType" => $row[DB::CONTEST_TYPE_OF_SUBSCRIBER]);
		
		$c = new Contest($data);
		$c->setID(intval($row[DB::CONTEST_ID]));
		
		if($this->loadSubscribers)
			$this->loadSubscribers($c);
		if($this->loadWinners)
			$this->loadWinners($c);
		return $c;
	}

	private function loadSubscribers($contest, $limit = null) {
		parent::load($contest);
		if(!is_subclass_of($contest, self::OBJECT_CLASS))
			throw new Exception("Attenzione! Il parametro di ricerca non è un contest.");
		
		$options = array();
		if(!is_null($limit) && is_numeric($limit))
			$options = array("limit" => intval($limit));
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table_cs),
														 array(),
														 array(new WhereConstraint($this->table_cs->getColumn(DB::CONTEST_SUBSCRIBER_CONTEST),Operator::EQUAL,$contest->getID())),
														 $options),
							  $this->table_cs->getName(), $contest);

		$subscribers = array();
		while($row = $this->db->fetch_result()) {
			require_once 'dao/PostDao.php';
			$postdao = new PostDao();
			$subscribers[] = $postdao->quickLoad(intval($row[CONTEST_SUBSCRIBER_POST]));
		}
		return $contest->setSubscribers($subscribers);
	}
	
	private function loadWinners() {
		parent::load($contest);
		if(!is_subclass_of($contest, self::OBJECT_CLASS))
			throw new Exception("Attenzione! Il parametro di ricerca non è un contest.");
		
		$s = "SELECT * FROM " . $this->table_cs->getName() . " WHERE " . DB::CONTEST_SUBSCRIBER_PLACEMENT . " IS NOT NULL ORDER BY " . DB::CONTEST_SUBSCRIBER_PLACEMENT . " ASC";
		$rs = $this->db->execute($s, $this->table_cs->getName(), $contest);

		$subscribers = array();
		while($row = $this->db->fetch_result()) {
			require_once 'dao/PostDao.php';
			$postdao = new PostDao();
			$subscribers[] = $postdao->quickLoad(intval($row[CONTEST_SUBSCRIBER_POST]));
		}
		return $contest->setSubscribers($subscribers);
	}

	function subscribePost($post, $contest) {
		parent::save($contest, self::OBJECT_CLASS);
		parent::save($post, "Post");
		if($post->getType() != $contest->getSubscriberType())
			throw new Exception("Non puoi iscrivere questo post nel contest selezionato.");
		if(time() > $contest->getEnd())
			throw new Exception("Non puoi iscrivere questo post perché scaduto il termine di iscrizione.");
		
		$this->db->execute($s = Query::generateInsertStm($this->table_cs, array(DB::CONTEST_SUBSCRIBER_POST => $post->getID(),
																		  DB::CONTEST_SUBSCRIBER_CONTEST => $contest->getID())), $table->getName(), $contest);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Errore iscrivendo il post nel contest. Il post potrebbe essere già iscritto.");
				
		//$this->loadSubscribers($contest); //FIXME vale la pena ricaricare il contest?
		return $contest;
	}
	
	function unsubscribePost($post, $contest) {
		parent::delete($contest, self::OBJECT_CLASS);
		if(!is_subclass_of($post, "Post"))
			throw new Exception("Attenzione! Il parametro di ricerca non è un post.");
		
		if(time() > $contest->getEnd())
			throw new Exception("Questo contest è chiuso, non puoi cancellare la tua iscrizione.");
		
		$this->db->execute($s = Query::generateDeleteStm($this->table_cs,
												   array(new WhereConstraint($this->table_cs->getColumn(DB::CONTEST_SUBSCRIBER_POST), Operator::EQUAL, $post->getID()),
														 new WhereConstraint($this->table_cs->getColumn(DB::CONTEST_SUBSCRIBER_CONTEST), Operator::EQUAL, $this->getID()))), $this->table_cs->getName(), $contest);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore eliminando l'oggetto. Riprovare.");
		
		//$this->loadSubscribers($contest); //FIXME vale la pena ricaricare il contest?
		return $contest;
	}
	
	function addWinner($post, $contest, $position) {
		parent::update($contest, self::OBJECT_CLASS);
		if(!is_subclass_of($post, "Post"))
			throw new Exception("Attenzione! Il parametro di ricerca non è un post.");
		
		$this->db->execute($s = Query::generateUpdateStm($this->table_cs, array(DB::CONTEST_SUBSCRIBER_PLACEMENT => intval($position)),
												   array(new WhereConstraint($this->table_cs->getColumn(DB::CONTEST_SUBSCRIBER_POST), Operator::EQUAL, $post->getID()),
														 new WhereConstraint($this->table_cs->getColumn(DB::CONTEST_SUBSCRIBER_CONTEST), Operator::EQUAL, $contest->getID()))), $table->getName(), $contest);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore aggiornando l'oggetto. Riprovare.");
		return $contest;
	}
	
	function setLoadSubscribers($load) {
		settype($load, "boolean");
		$this->loadSubscribers = $load;
		return $this;
	}
	
	function setLoadWinners($load) {
		settype($load, "boolean");
		$this->loadWinners = $load;
		return $this;
	}
	
	function exists($contest) {
		try {
			$c = $this->quickLoad($contest->getID());
			return is_subclass_of($c, self::OBJECT_CLASS);
		} catch(Exception $e) {
			return false;
		}
	}
	
	private function getAccessCount($contest) {
		parent::getAccessCount($contest, $this->table, DB::CONTEST_ID);
	}
}
?>