<?php //TODO
define("ID", "ID");
define("NICKNAME", "nickname");
//EMAIL è già definito in questo file
define("PASSWORD", "password");
//NAME è già definito in questo file
define("SURNAME", "surname");
define("GENDER", "gender");
define("BIRTHDAY", "birthday");
define("BIRTHPLACE", "birthplace");
define("LIVING_PLACE", "livingPlace");
define("AVATAR", "avatar");
define("HOBBIES", "hobbies");
define("JOB", "job");
define("ROLE", "role");
define("CREATION_DATE", "creationDate");
define("VISIBLE", "visible");
define("VERIFIED", "verified");

define("CONTACTS", "contacts");
define("FEEDBACK", "feedback");
define("FOLLOWS", "follows");
define("FOLLOWERS", "followers");
define("REPORTS", "reports");

class User {
	protected $ID;				//
	protected $nickname;		//**
	protected $email;			//**
	protected $password;		//**
	protected $name;			//**
	protected $surname;			//**
	protected $gender;			//**
	protected $birthday;		//** TIMESTAMP
	protected $birthplace;		//**
	protected $livingPlace;		//**
	protected $avatar;			//**
	protected $hobbies;			//**
	protected $job;				//**
	protected $role;			//**
	protected $creationDate;	//
	protected $visible;			//**
	protected $verified;		// *
	protected $accessCount;		// *
	
	protected $contacts = array();		//
	protected $feedback;				//valore totale feedback
	protected $follows = array();		//chi segue
	protected $followers = array();		//chi lo segue
	protected $reports = array(); 		//TODO da implementare
	
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
		if(!isset($this->feedback))
			if(isset($this->ID))
				$this->loadFeedback();
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

	/**
	 * @param $data: array associativo con le seguenti chiavi:
	 * nickname			//
	 * email			//
	 * password			//
	 * name				//
	 * surname			//
	 * gender			//
	 * birthday			//
	 * birthplace		//
	 * livingPlace		//
	 * avatar			//
	 * hobbies			//
	 * job				//
	 * role				//
	 * visible			//
	 */
	function __construct($data) {
		$data[VERIFIED] = false;
		$this->setValuesFromArray($data);
	}
	
	private function setValuesFromArray($data) {
		if(isset($data[NICKNAME]))
			$this->setNickname($data[NICKNAME]);
		if(isset($data[EMAIL]))
			$this->setEMail($data[EMAIL]);
		if(isset($data[PASSWORD]))
			$this->setPassword($data[PASSWORD]);
		if(isset($data[NAME]))
			$this->setName($data[NAME]);
		if(isset($data[SURNAME]))
			$this->setSurname($data[SURNAME]);
		if(isset($data[GENDER]))
			$this->setGender($data[GENDER]);
		if(isset($data[BIRTHDAY]))
			$this->setBirthday(intval($data[BIRTHDAY]));
		if(isset($data[BIRTHPLACE]))
			$this->setBirthplace($data[BIRTHPLACE]);
		if(isset($data[LIVING_PLACE]))
			$this->setLivingPlace($data[LIVING_PLACE]);
		if(isset($data[AVATAR]))
			$this->setAvatar($data[AVATAR]);
		if(isset($data[HOBBIES]))
			$this->setHobbies($data[HOBBIES]);
		if(isset($data[JOB]))
			$this->setJob($data[JOB]);
		if(isset($data[ROLE]))
			$this->setRole($data[ROLE]);
		if(isset($data[VISIBLE]))
			$this->setVisible($data[VISIBLE]);
		if(isset($data[VERIFIED]))
			$this->setVerified($data[VERIFIED]);
	}
	
	function edit($data) {
		$this->setValuesFromArray($data);
		return $this->update();
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