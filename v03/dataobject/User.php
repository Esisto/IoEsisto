<?php

class User extends Editable {
	private $accessCount;		// *
	private $avatar;			//**
	private $birthday;			//**
	private $birthplace;		//**
	private $creationDate;		//
	private $email;				//**
	private $gender;			//**
	private $hobbies;			//**
	private $ID;				//
	private $job;				//**
	private $livingPlace;		//**
	private $name;				//**
	private $nickname;			//**
	private $password;			//**
	private $role;				//**
	private $surname;			//**
	private $verified;			// *
	private $visible;			//**
	
	private $contacts;		//
	private $feedback;		//valore totale feedback
	private $follows;		//chi segue
	private $followers;		//chi lo segue
	private $reports; 		//le segnalazioni

	function __construct($nickname, $email, $password) {
		$this->setNickname($nickname);
		$this->setEMail($email);
		$this->setPassword($password);
		$this->setVerified(false);
	}
	
	function getID() {
		return $this->ID;
	}
	function getNickname() {
		return $this->nickname;
	}
	function getEMail() {
		return $this->email;
	}
	function getPassword() {
		return $this->password;
	}
	function getName() {
		return $this->name;
	}
	function getSurname() {
		return $this->surname;
	}
	function getGender() {
		return $this->gender;
	}
	function getBirthday() {
		return $this->birthday;
	}
	function getBirthplace() {
		return $this->birthplace;
	}
	function getLivingPlace() {
		return $this->livingPlace;
	}
	function getAvatar() {
		return $this->avatar;
	}
	function getHobbies() {
		return $this->hobbies;
	}
	function getJob() {
		return $this->job;
	}
	function getRole() {
		return $this->role;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function getVisible() {
		return $this->visible;
	}
	function getVerified() {
		return $this->verified;
	}
	function getContacts() {
		return $this->contacts;
	}
	function getFeedback() {
		return $this->feedback;
	}
	function getFollows() {
		return $this->follows;
	}
	function getFollowers() {
		return $this->followers;
	}
	function getAccessCount() {
		return $this->accessCount;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	function setNickname($nickname) {
		$this->nickname = $nickname;
		return $this;
	}
	function setEMail($email) {
		$this->email = $email;
		return $this;
	}
	function setPassword($password) {
		$this->password = $password;
		return $this;
	}
	function setName($name) {
		$this->name = $name;
		return $this;
	}
	function setSurname($surname) {
		$this->surname = $surname;
		return $this;
	}
	function setGender($gender) {
		$this->gender = $gender;
		return $this;
	}
	function setBirthday($birthday) {
		$this->birthday = $birthday;
		return $this;
	}
	function setBirthplace($birthplace) {
		$this->birthplace = $birthplace;
		return $this;
	}
	function setLivingPlace($livingPlace) {
		$this->livingPlace= $livingPlace;
		return $this;
	}
	function setAvatar($avatar) {
		$this->avatar = $avatar;
		return $this;
	}
	function setHobbies($hobbies) {
		$this->hobbies = $hobbies;
		return $this;
	}
	function setJob($job) {
		$this->job = $job;
		return $this;
	}
	function setRole($role) {
		$this->role = $role;
		return $this;
	}
	function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
		return $this;
	}
	function setVisible($visible) {
		$this->visible = $visible;
		return $this;
	}
	function setVerified($verified) {
		$this->verified = $verified;
		return $this;
	}
	function setContacts($contacts) {
		$this->contacts = $contacts;
		return $this;
	}
	function setFeedback($feedback) {
		$this->feedback = $feedback;
		return $this;
	}
	function setFollows($follows) {
		$this->follows = $follows;
		return $this;
	}
	function setFollowers($followers) {
		$this->followers = $followers;
		return $this;
	}
	function setAccessCount($ac) {
		$this->accessCount = $ac;
		return $this;
	}
	
	function addContact($contact) {
		return $this->loadContacts();
	}
	
	function __toString() {
		$s = "<b>USER</b> (ID = " . $this->getID() .
						"<br /> nickname = " . $this->getNickname() .
						"<br /> mail = " . $this->getEMail() .
						"<br /> password = " . $this->getPassword() .
						"<br /> name = " . $this->getName() .
						"<br /> surname = " . $this->getSurname() .
						"<br /> gender = " . $this->getGender() .
						"<br /> birthday = " . date("d/m/Y", $this->getBirthday()) .
						"<br /> birthplace = " . $this->getBirthplace() .
						"<br /> living place = " . $this->getLivingPlace() .
						"<br /> avatar = " . $this->getAvatar() .
						"<br /> creation date = " . date("d/m/Y G:i:s", $this->getCreationDate()) .
						"<br /> feedback = " . $this->getFeedback() .
						"<br /> hobbies = " . $this->getHobbies() .
						"<br /> job = " . $this->getJob() .
						"<br /> role = " . $this->getRole() .
						"<br /> verified = " . ($this->getVerified() ? "true" : "false") .
						"<br /> visible = " . ($this->getVisible() ? "true" : "false") .
						"<br /> contacts = (";
		$first = true;
		foreach($this->getContacts() as $cont) {
			if($first) $first = false;
			else $s.= ", ";
			$s.= $cont;
		}
		$s.= ")<br /> followers = <font color='red'>(";
		$first = true;
		foreach($this->getFollowers() as $follower) {
			if($first) $first = false;
			else $s.= ", ";
			$s.= $follower;
		}
		$s.= ")</font><br /> follows = <font color='green'>(";
		$first = true;
		foreach($this->getFollows() as $follows) {
			if($first) $first = false;
			else $s.= ", ";
			$s.= $follows;
		}
		$s.= ")</font>)";
		return $s;
	}
}
?>