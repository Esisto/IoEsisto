<?php
//TODO
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
			$this->setSubscribers($data["subscribers"]);
		// END DEBUG
	}
	
	function edit($data) { //TODO
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
			$this->setSubscribers($data["subscribers"]);
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