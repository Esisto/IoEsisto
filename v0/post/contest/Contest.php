<?php

class Contest {
	protected $ID;
	protected $title;
	protected $description;
	protected $rules;
	protected $prizes;
	protected $begins;
	protected $ends;
	protected $subscribers;
	protected $winners;
	
	function __construct($data) {
		if(isset($data["title"]))
			$this->setTitle($data["title"]);
		if(isset($data["description"]))
			$this->setDescription($data["description"]);
		if(isset($data["rules"]))
			$this->setRules($data["rules"]);
		if(isset($data["prizes"]))
			$this->setPrizes($data["prizes"]);
		if(isset($data["begins"]))
			$this->setBegins($data["begins"]);
		if(isset($data["ends"]))
			$this->setEnds($data["ends"]);
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
	function getBegins() {
		return $this->begins;
	}
	function getEnds() {
		return $this->ends;
	}
	function getSubscribers() {
		return $this->subscribers;
	}
	function getWinners() {
		return $this->winners;
	}
	
	function setTitle($title) {
		$this->title = $title;
	}
	function setDescription($description) {
		$this->description = $description;
	}
	function setRules($rules) {
		$this->rules = $rules;
	}
	function setPrizes($prizes) {
		$this->prizes = $prizes;
	}
	function setBegins($begins) {
		$this->begins = $begins;
	}
	function setEnds($ends) {
		$this->ends = $ends;
	}
	function addSubscriber($subscriber) {
		$this->subscribers[] = $subscriber;
	}
	function removeSubcriber($subscriber) {
		unset($this->subscribers[array_search($subscriber, $this->subscribers)]);
	}
	function setWinners($winners) {
		$this->winners = $winners;
	}
	
	function subscribePost($post) {
		$this->addSubscriber($post);
		
		$this->save(SavingMode::$UPDATE);
	}
	
	function unsubscribePost($post) {
		$this->removeSubscribers($post);
		
		return $post;
	}
	
	function save($savingMonde) {}
	
	function delete() {}
}

class GoldenPaper extends Contest {
	
	function __construct($data) {
		parent::__construct($data);
	}
}

?>