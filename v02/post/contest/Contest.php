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
	 * @param data: array associativo contenente i dati.
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
	 * @return: il contest creato.
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
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContestSubscriberColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST_SUBSCRIBER);
			$db->execute($s = Query::generateInsertStm($table, array(CONTEST_SUBSCRIBER_POST => $post->getID(),
																 CONTEST_SUBSCRIBER_CONTEST => $this->getID())),
						$table->getName(), $this);
			if($db->affected_rows() == 1) {
				$this->subscribers[] = $post->getID();
				return $this;
			} else $db->display_error("Contest::subscribePost()");
		} else $db->display_connect_error("Contest::subscribePost()");
		return false;
	}
	
	function unsubscribePost($post) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContestSubscriberColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST_SUBSCRIBER);
			$db->execute($s = Query::generateDeleteStm($table,
												   array(new WhereConstraint($table->getColumn(CONTEST_SUBSCRIBER_POST), Operator::EQUAL, $post->getID()),
														 new WhereConstraint($table->getColumn(CONTEST_SUBSCRIBER_CONTEST), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			if($db->affected_rows() == 0) {
				$this->loadSubscribers();
				return $post;
			} else $db->display_error("Contest::unsubscribePost()");
		} else $db->display_connect_error("Contest::unsubscribePost()");
		return false;
	}
	
	function addWinner($post, $position) {
		if($post->getType() != $this->getSubscriberType())
			return false;
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContestSubscriberColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST_SUBSCRIBER);
			$db->execute($s = Query::generateUpdateStm($table, array(CONTEST_SUBSCRIBER_PLACEMENT => $position),
												   array(new WhereConstraint($table->getColumn(CONTEST_SUBSCRIBER_POST), Operator::EQUAL, $post->getID()),
														 new WhereConstraint($table->getColumn(CONTEST_SUBSCRIBER_CONTEST), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			if($db->affected_rows() == 0) {
				$this->winners[$position] = $post->getID();
				return $this;
			} else $db->display_error("Contest::addWinner()");
		} else $db->display_connect_error("Contest::addWinner()");
		return false;
	}
	
	/**
	 * Salva il contest e le sue dipendenze nel database.
	 *
	 * @return: ID della tupla inserita, FALSE se c'è un errore.
	 */
	function save() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContestColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST);
			$data = array();
			if(isset($this->title) && !is_null($this->getTitle()))
				$data[CONTEST_TITLE] = $this->getTitle();
			if(isset($this->description) && !is_null($this->getDescription()))
				$data[CONTEST_DESCRIPTION] = $this->getDescription();
			if(isset($this->rules) && !is_null($this->getRules()))
				$data[CONTEST_RULES] = $this->getRules();
			if(isset($this->prizes) && !is_null($this->getPrizes()))
				$data[CONTEST_PRIZES] = $this->getPrizes();
			if(isset($this->start) && !is_null($this->getStart()))
				$data[CONTEST_START] = date("Y/m/d G:i:s", $this->getStart());
			if(isset($this->end) && !is_null($this->getEnd()))
				$data[CONTEST_END] = date("Y/m/d G:i:s", $this->getEnd());
			if(isset($this->subscriberType) && !is_null($this->getSubscriberType()))
				$data[CONTEST_TYPE_OF_SUBSCRIBER] = $this->getSubscriberType();
			
			$rs = $db->execute($s = Query::generateInsertStm($table,$data),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			if($db->affected_rows() == 1) {
				$this->setID($db->last_inserted_id());
				//echo "<br />" . serialize($this->ID); //DEBUG
				
				//echo "<br />" . $this; //DEBUG
				return $this->ID;
			} else $db->display_error("Contest::save()");
		} else $db->display_connect_error("Contest::save()");
		return false;
	}
	
	/**
	 * Aggiorna il post e le sue dipendenze nel database.
	 * Le dipendenze aggiornate sono quelle che dipendono dall'autore ovvero: tag e categorie
	 * Potrebbe salvare alcune tuple in Tag.
	 *
	 * @return: $this o FALSE se c'è un errore.
	 */
	function update() {
		$old = Contest::loadFromDatabase($this->getID());
		
		$data = array();
		if($old->getTitle() != $this->getTitle())
			$data[CONTEST_TITLE] = $this->getTitle();
		if($old->getDescription() != $this->getDescription())
			$data[CONTEST_DESCRIPTION] = $this->getDescription();
		if($old->getEnd() != $this->getEnd())
			$data[CONTEST_END] = $this->getEnd();
		if($old->getPrizes() != $this->getPrizes())
			$data[CONTEST_PRIZES] = $this->getPrizes();
		if($old->getRules() != $this->getRules())
			$data[CONTEST_RULES] = $this->getRules();
		if($old->getStart() != $this->getStart())
			$data[CONTEST_START] = $this->getStart();
		if($old->getWinners() != $this->getWinners())
			$data["ct_winners"] = $this->getWinners(); //TODO aggiornare i vincitori.
		if($old->getSubscriberType() == $this->getSubscriberType())
			$data[CONTEST_TYPE_OF_SUBSCRIBER] = $this->getSubscriberType();
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST);
			
			$rs = $db->execute($s = Query::generateUpdateStm($table,
														 $data,
														 array(new WhereConstraint($table->getColumn(CONTEST_ID),Operator::EQUAL,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . mysql_affected_rows(); //DEBUG
			if($db->affected_rows() == 1) {
				//echo "<br />" . $this; //DEBUG
				return $this->getID();
			} else $db->display_error("Contest::update()");
		} else $db->display_connect_error("Contest::update()");
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
			define_tables(); defineContestColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(CONTEST_ID),Operator::EQUAL,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Contest::delete()");
		} else $db->display_connect_error("Contest::delete()");
		return false;
	}
	
	/**
	 * Crea un post caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Post().
	 *
	 * @param $id: l'ID del post da caricare.
	 * @return: il post caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($id) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(CONTEST_ID),Operator::EQUAL,$id)),
														 array()),
							  $table->getName(), null);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			//echo "<p>" . mysql_num_rows($rs) . "</p>"; //DEBUG
			if($db->num_rows() == 1) {
				// echo serialize(mysql_fetch_assoc($rs)); //DEBUG
				$row = $db->fetch_result();
				$data = array("title" => $row[CONTEST_TITLE],
							  "description" => $row[CONTEST_DESCRIPTION],
							  "rules" => $row[CONTEST_RULES],
							  "prizes"=> $row[CONTEST_PRIZES],
							  "start" => date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[CONTEST_START])),
							  "end" => date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[CONTEST_END])),
							  //"winners" => unserialize($row["ct_winners"]),
							  "subscriberType" => $row[CONTEST_TYPE_OF_SUBSCRIBER]);
				$c = new Contest($data);
				$c->setID(intval($row[CONTEST_ID]));
				$c->loadSubscribers()->loadWinners();
				//echo "<p>" .$p ."</p>";
				return $c;
			} else $db->display_error("Contest::loadFromDatabase()");
		} else $db->display_connect_error("Contest::loadFromDatabase()");
		return false;
	}

	function loadSubscribers() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContestSubscriberColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST_SUBSCRIBER);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(CONTEST_SUBSCRIBER_CONTEST),Operator::EQUAL,$this->getID())),
														 array()),
							  $table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG;
			//echo "<p>" . mysql_num_rows($rs) . "</p>"; //DEBUG;
			if($db->num_rows() > 0) {
				$sub = array();
				while($row = $db->fetch_result()) {
					$sub[] = $row[CONTEST_SUBSCRIBER_POST];
				}
				$this->setSubscribers($sub);
			} else {
				if($db->errno())
					$db->display_error("Contest::loadSubscribers()");
			}
		} else $db->display_connect_error("Contest::loadSubscribers()");
		return $this;
	}
	
	function loadWinners() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_CONTEST_SUBSCRIBER);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(CONTEST_SUBSCRIBER_CONTEST),Operator::EQUAL,$this->getID()),
															   new WhereConstraint($table->getColumn(CONTEST_SUBSCRIBER_PLACEMENT),Operator::GREATER,0)),
														 array()),
							  $table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG;
			//echo "<p>" . mysql_num_rows($rs) . "</p>"; //DEBUG;
			if($db->num_rows() > 0) {
				$win = array();
				while($row = $db->fetch_result()) {
					$win[$row[CONTEST_SUBSCRIBER_PLACEMENT]] = $row[CONTEST_SUBSCRIBER_POST];  // Con in cs_winner il numero di posizione
					//$win[] = $row[CONTEST_SUBSCRIBER_POST];  // Con in cs_winner true o false
				}
				$this->setWinners($win);
			} else {
				if($db->errno())
					$db->display_error("Contest::loadWinners()");
			}
		} else $db->display_connect_error("Contest::loadWinners()");
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