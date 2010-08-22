<?php

class Contest {
	protected $ID;
	protected $title;
	protected $description;
	protected $rules;
	protected $prizes;
	protected $start;
	protected $end;
	protected $subscriberType;
	protected $subscribers;
	protected $winners;
	
	/**
	 * Crea un oggetto post.
	 *
	 * param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo del post (string filtrata)
	 * description: descrizione
	 * rules: regole
	 * prizes: premi
	 * start: timestamp della data di inizio iscrizioni
	 * end: timestamp della data di fine iscrizioni
	 * subscriberType: tipo di post accettati nel contest. Di tipo PostType.
	 * subscribers: array di post iscritti
	 * 
	 * return: il contest creato.
	 */
	function __construct($data) {
		if(isset($data["title"]))
			$this->setTitle($data["title"]);
		if(isset($data["description"]))
			$this->setDescription($data["description"]);
		if(isset($data["rules"]))
			$this->setRules($data["rules"]);
		if(isset($data["prizes"]))
			$this->setPrizes($data["prizes"]);
		if(isset($data["subscriberType"]))
			$this->setSubscriberType($data["subscriberType"]);
		if(isset($data["start"]))
			$this->setStart($data["start"]);
		if(isset($data["end"]))
			$this->setEnd($data["end"]);
		// DEBUG
		if(isset($data["subscribers"]))
			$this->setEnds($data["subscribers"]);
		// END DEBUG
	}
	
	function getID() {
		return $this->ID;
	}
	function getTitle() {
		return $this->title;
	}
	function getDescription() {
		return $this->description;
	}
	function getRules() {
		return $this->rules;
	}
	function getPrizes() {
		return $this->prizes;
	}
	function getStart() {
		return $this->start;
	}
	function getEnd() {
		return $this->end;
	}
	function getSubscribers() {
		return $this->subscribers;
	}
	function getWinners() {
		return $this->winners;
	}
	function getSubscriberType() {
		return $this->subscriberType;
	}
	
	function setTitle($title) {
		$this->title = $title;
		return $this;
	}
	function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	function setRules($rules) {
		$this->rules = $rules;
		return $this;
	}
	function setPrizes($prizes) {
		$this->prizes = $prizes;
		return $this;
	}
	function setStart($start) {
		$this->start = $start;
		return $this;
	}
	function setEnd($end) {
		$this->end = $end;
		return $this;
	}
	function setWinners($winners) {
		$this->winners = $winners;
		return $this;
	}
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	function setSubscriberType($subscriberType) {
		$this->subscriberType = $subscriberType;
		return $this;
	}
	function setSubscribers($subscribers) {
		$this->subscribers = $subscribers;
		return $this;
	}
	
	function subscribePost($post) {
		if($post->getType() != $this->getSubscriberType())
			return false;
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable("ContestSubscriber");
			$q->execute($s = $q->generateInsertStm($table, array("cs_post" => $post->getID(),
																 "cs_contest" => $this->getID())));
			
			if(mysql_affected_rows() == 0)
				return false;
		}
		$this->subscribers[] = $post->getID();
		require_once("common.php");
		require_once("session.php");
		LogManager::addLogEntry(Session::getUser(), LogManager::$INSERT, $this);
		return $this;
	}
	
	function unsubscribePost($post) {
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable("ContestSubscriber");
			$q->execute($s = $q->generateDeleteStm($table,
												   array(new WhereConstraint($table->getColumn("cs_post"), Operator::$UGUALE, $post->getID()),
														 new WhereConstraint($table->getColumn("cs_contest"), Operator::$UGUALE, $this->getID()))));
			
			if(mysql_affected_rows() == 0)
				return false;
		}
		unset($this->subscribers[array_search($post, $this->subscribers)]);
		
		require_once("common.php");
		require_once("session.php");
		LogManager::addLogEntry(Session::getUser(), LogManager::$DELETE, $this);
		return $this->getID();
		return $post;
	}
	
	/**
	 * Salva il contest e le sue dipendenze nel database.
	 *
	 * return: ID della tupla inserita, FALSE se c'è un errore.
	 */
	function save() {
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $q->getDBSchema();
			$table = $dbs->getTable("Contest");
			$data = array();
			if(isset($this->title) && !is_null($this->getTitle()))
				$data["ct_title"] = $this->getTitle();
			if(isset($this->description) && !is_null($this->getDescription()))
				$data["ct_description"] = $this->getDescription();
			if(isset($this->rules) && !is_null($this->getRules()))
				$data["ct_rules"] = $this->getRules();
			if(isset($this->prizes) && !is_null($this->getPrizes()))
				$data["ct_prizes"] = $this->getPrizes();
			if(isset($this->start) && !is_null($this->getStart()))
				$data["ct_start"] = date("Y/m/d G:i", $this->getStart());
			if(isset($this->end) && !is_null($this->getEnd()))
				$data["ct_end"] = date("Y/m/d G:i", $this->getEnd());
			if(isset($this->subscriberType) && !is_null($this->getSubscriberType()))
				$data["ct_typeofsubscriber"] = $this->getSubscriberType();
			
			$rs = $q->execute($s = $q->generateInsertStm($table,$data));
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			$this->ID = mysql_insert_id();
			//echo "<br />" . serialize($this->ID); //DEBUG
			
			//echo "<br />" . $this; //DEBUG
			require_once("common.php");
			require_once("session.php");
			LogManager::addLogEntry(Session::getUser(), LogManager::$INSERT, $this);
			return $this->ID;
		}
		return false;
	}
	
	/**
	 * Aggiorna il post e le sue dipendenze nel database.
	 * Le dipendenze aggiornate sono quelle che dipendono dall'autore ovvero: tag e categorie
	 * Potrebbe salvare alcune tuple in Tag.
	 *
	 * return: $this o FALSE se c'è un errore.
	 */
	function update() {
		$old = Contest::loadFromDatabase($this->getID());
		
		$data = array();
		if($old->getTitle() != $this->getTitle())
			$data["ct_title"] = $this->getTitle();
		if($old->getDescription() != $this->getDescription())
			$data["ct_description"] = $this->getDescription();
		if($old->getEnd() != $this->getEnd())
			$data["ct_end"] = $this->getEnd();
		if($old->getPrizes() != $this->getPrizes())
			$data["ct_prizes"] = $this->getPrizes();
		if($old->getRules() != $this->getRules())
			$data["ct_rules"] = $this->getRules();
		if($old->getStart() != $this->getStart())
			$data["ct_start"] = $this->getStart();
		if($old->getWinners() != $this->getWinners())
			$data["ct_winners"] = $this->getWinners(); //TODO aggiornare i vincitori.
		if($old->getSubscriberType() == $this->getSubscriberType())
			$data["ct_typeofsubscriber"] = $this->getSubscriberType();
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			$q = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $q->getDBSchema()->getTable("Contest");
			
			$rs = $q->execute($s = $q->generateUpdateStm($table,
														 $data,
														 array(new WhereConstraint($table->getColumn("ct_ID"),Operator::$UGUALE,$this->getID()))));
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . mysql_affected_rows(); //DEBUG
			if(mysql_affected_rows() == 0)
				return false;
			
			//echo "<br />" . $this; //DEBUG
			require_once("session.php");
			LogManager::addLogEntry(Session::getUser(), LogManager::$UPDATE, $this);
			return $this->getID();
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
			$table = $dbs->getTable("Contest");
			$rs = $q->execute($s = $q->generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn("ct_ID"),Operator::$UGUALE,$this->getID()))));
			//echo "<br />" . $s; //DEBUG
			if(mysql_affected_rows() == 1) {
				require_once("common.php");
				require_once("session.php");
				LogManager::addLogEntry(Session::getUser(), LogManager::$DELETE, $this);
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
		$table = $q->getDBSchema()->getTable("Contest");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("ct_ID"),Operator::$UGUALE,$id)),
													 array()));
		
		//echo "<p>" . $s . "</p>"; //DEBUG
		//echo "<p>" . mysql_num_rows($rs) . "</p>"; //DEBUG
		if($rs !== false && mysql_num_rows($rs) == 1) {
			// echo serialize(mysql_fetch_assoc($rs)); //DEBUG
			while($row = mysql_fetch_assoc($rs)) {
				$data = array("title" => $row["ct_title"],
							  "description" => $row["ct_description"],
							  "rules" => $row["ct_rules"],
							  "prizes"=> $row["ct_prizes"],
							  "start" => time($row["ct_start"]),
							  "end" => time($row["ct_end"]),
							  "winners" => unserialize($row["ct_winners"]),
							  "subscriberType" => $row["ct_typeofsubscriber"]);
				$c = new Contest($data);
				$c->setID(intval($row["ct_ID"]));
				break;
			}
			$c->loadSubscribers();
			//echo "<p>" .$p ."</p>";
			return $c;
		} else {
			$GLOBALS["query_error"] = "NOT FOUND";
			return false;
		}
	}

	function loadSubscribers() {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable("ContestSubscriber");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("cs_contest"),Operator::$UGUALE,$this->getID())),
													 array()));
		
		//echo "<p>" . $s . "</p>"; //DEBUG;
		//echo "<p>" . mysql_num_rows($rs) . "</p>"; //DEBUG;
		if($rs !== false) {
			$sub = array();
			while($row = mysql_fetch_assoc($rs))
				$sub[] = $row["cs_post"];
			$this->setSubscribers($sub);
		}
		return $this;
	}
	
	function __toString() {
		$s = "Contest (ID = " . $this->getID() .
			 " | title = " . $this->getTitle() .
			 " | description = " . $this->getDescription() .
			 " | rules = " . $this->getRules() .
			 " | prizes = " . $this->getPrizes() .
			 " | start = " . date("d/m/Y G:i:s", $this->getStart()) .
			 " | end = " . date("d/m/Y G:i:s", $this->getEnd()) .
			 " | subscriberType = " . $this->getSubscriberType() .
			 ") | subscribers = (";
		for($i=0; $i<count($this->getSubscribers()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->subscribers[$i];
		}
		$s.= ") | winners = (";
		for($i=0; $i<count($this->getWinners()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->winners[$i];
		}
		$s.= "))";
		return $s;
	}
}

?>