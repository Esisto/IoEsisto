<?php
class Contact {
	const PHONE = "phone";
	const ADDRESS = "address";
	const EMAIL = "email";
	const WEBSITE = "website";
	const IM = "IM";
	const NAME = "name";
	const CONTACT = "contact";
	const TYPE = "type";
	const USER = "user";
	
	private $ID;
	private $contact;
	private $type;
	private $name;
	private $user;
	
	function getID() {
		return $this->ID;
	}
	function getContact() {
		return $this->contact;
	}
	function getType() {
		return $this->type;
	}
	function getUser() {
		return $this->user;
	}
	function getName() {
		return $this->name;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	function setContact($contact) {
		$this->contact = $contact;
		return $this;
	}
	function setType($type) {
		//if($type == self::$ADDRESS || $type == self::$EMAIL || $type == self::$IM || $type == self::$PHONE || $type == self::$WEBSITE)
			$this->type = $type;
		return $this;
	}
	function setName($name) {
		$this->name = $name;
		return $this;
	}
	//L'utente non può cambiare quindi non c'è setUser().
	
	function __construct($data) {
		if(isset($data[USER]))
			$this->user = $data[USER];
		if(isset($data[CONTACT]))
			$this->setContact($data[CONTACT]);
		if(isset($data[NAME]))
			$this->setName($data[NAME]);
		if(isset($date[TYPE]))
			$this->setType($data[TYPE]);
	}
	
	function edit($data) {
		if(isset($data[CONTACT]))
			$this->setContact($data[CONTACT]);
		if(isset($data[NAME]))
			$this->setName($data[NAME]);
		if(isset($data[TYPE]))
			$this->setType($data[TYPE]);
			
		$this->update();
	}
	
	function __toString() {
		$s = "CONTACT (name = " . $this->getName() .
					" | value = " . $this->getContact() .
					" | type = " . $this->getType() .
					" | user = " . $this->getUser() . ")";
		return $s;
	}
}
?>