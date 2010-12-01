<?php
require_once("settings.php");
require_once("strings/strings.php");
define("PHONE", "phone");
define("ADDRESS", "address");
define("EMAIL", "email");
define("WEBSITE", "website");
define("IM", "IM");
define("NAME", "name");
define("CONTACT", "contact");
define("TYPE", "type");
define("USER", "user");

class Contact {
	static $PHONE = PHONE;
	static $ADDRESS = ADDRESS;
	static $EMAIL = EMAIL;
	static $WEBSITE = WEBSITE;
	static $IM = IM;
	
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
	
	function save() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			$data = array(CONTACT_CONTACT => $this->getContact(),
						  CONTACT_NAME => $this->getName(),
						  CONTACT_USER => $this->getUser());
			
			$db->execute($s = Query::generateInsertStm($table, $data), $table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			if($db->affected_rows() == 1) {
				$this->setID($db->last_inserted_id());
				
				return $this;
			} else $db->display_error("Contact::save()");
		} else $db->display_connect_error("Contact::save()");
		return false;
	}
	
	function update() {
		$old = self::loadFromDatabase($this->getID());
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			
			$data = array();
			if($this->getContact() != $old->getContact())
				$data[CONTACT_CONTACT] = $this->getContact();
			if($this->getName() != $old->getName())
				$data[CONTACT_NAME] = $this->getName();
				
			$db->execute($s = Query::generateUpdateStm($table, $data, array(new WhereConstraint($table->getColumn(CONTACT_ID), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Contact::update()");
		} else $db->display_connect_error("Contact::update()");
		return false;
	}
	
	function delete() {
        require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			
			$db->execute($s = Query::generateDeleteStm($table, array(new WhereConstraint($table->getColumn(CONTACT_ID), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Contact::delete()");
		} else $db->display_connect_error("Contact::delete()");
		return false;
	}
	
	static function loadFromDatabase($id) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns(); defineContactTypeColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			$table1 = Query::getDBSchema()->getTable(TABLE_CONTACT_TYPE);
			
			$db->execute($s = Query::generateSelectStm(array($table, $table1),
												   array(new JoinConstraint($table->getColumn(CONTACT_NAME), $table1->getColumn(CONTACT_TYPE_NAME))),
												   array(new WhereConstraint($table->getColumn(CONTACT_ID), Operator::EQUAL, $id)),
												   array()));
			
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				$data = array(NAME => $row[CONTACT_NAME],
							  CONTACT => $row[CONTACT_CONTACT],
							  USER => $row[CONTACT_USER]);
				
				$c = new Contact($data);
				$c->setType($row[CONTACT_TYPE_TYPE]);
				return $c->setID(intval($row[CONTACT_ID]));
			} else $db->display_error("Contact::loadFromDatabase()");
		} else $db->display_connect_error("Contact::loadFromDatabase()");
		return false;
	}
	
	static function loadContactsForUser($userid) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns(); defineContactTypeColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			$table1 = Query::getDBSchema()->getTable(TABLE_CONTACT_TYPE);
			
			$rs = $db->execute($s = Query::generateSelectStm(array($table, $table1),
												   array(new JoinConstraint($table->getColumn(CONTACT_NAME), $table1->getColumn(CONTACT_TYPE_NAME))),
												   array(new WhereConstraint($table->getColumn(CONTACT_USER), Operator::EQUAL, $userid)),
												   array()));
			
			//echo "<p>" . mysql_affected_rows() . mysql_num_rows($rs) . $s . "</p>"; //DEBUG
			$conts = array();
			if($db->num_rows() > 0) {
				while($row = $db->fetch_result()) {
					$data = array(NAME => $row[CONTACT_NAME],
								  CONTACT => $row[CONTACT_CONTACT],
								  USER => $row[CONTACT_USER]);
					
					$c = new Contact($data);
					$c->setType($row[CONTACT_TYPE_TYPE]);
					$c->setID(intval($row[CONTACT_ID]));
					$conts[] = $c;
				}
			} else  {
				if($db->errno())
					$db->display_error("Contact::loadContactsForUser()");
			}
			return $conts;
		} else $db->display_connect_error("Contact::loadContactsForUser()");
	}
	
	static function getContactsNames() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactTypeColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT_TYPE);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(), array(), array()));
			
			$names = array();
			if($db->num_rows() > 0) {
				while($row = $db->fetch_result()) {
					$names[$row[CONTACT_TYPE_NAME]] = $row[CONTACT_TYPE_TYPE];
				}
			} else  {
				if($db->errno())
					$db->display_error("Contact::getContactsNames()");
			}
			return $names;
		} else $db->display_connect_error("Contact::getContactsNames()");
	}
	
	function __toString() {
		$s = "CONTACT (name = " . $this->getName() .
					" | value = " . $this->getContact() .
					" | type = " . $this->getType() .
					" | user = " . $this->getUser() . ")";
		return $s;
	}
}

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

class User{
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

	function addFollower($user) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFollowColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FOLLOW);
			
			$db->execute($s = Query::generateInsertStm($table, array(FOLLOW_FOLLOWER => $user->getID(),
																 FOLLOW_SUBJECT => $this->getID())),
						$table->getName(), $this);
			
			if($db->affected_rows() != 1)
				$db->display_error("User::addFollower()"); //Genera un errore ma ritorna comunque $this
		} else $db->display_connect_error("User::addFollower()");
		return $this->loadFollowers();
	}
	
	function removeFollower($user) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFollowColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FOLLOW);
			
			$db->execute($s = Query::generateDeleteStm($table, array(new WhereConstraint($table->getColumn(FOLLOW_FOLLOWER), Operator::EQUAL, $user->getID()),
																 new WhereConstraint($table->getColumn(FOLLOW_SUBJECT), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			
			if($db->affected_rows() > 1)
				$db->display_error("User::removeFollower()"); //Genera un errore ma ritorna comunque $this
		} else $db->display_connect_error("User::removeFollower()");
		return $this->loadFollowers();
	}
	
	function follow($user) {
		$f = $user->getFollowers();
		if(!isset($f[$this->getID()]))
			$user->addFollower($this);
		return $this->loadFollows();
	}
	
	function stopFollowing($user) {
		if($user->removeFollower($this) !== false) {
			$user->loadFollows();
			return $this->loadFollows();
		}
		return false;
	}
	
	function addFeedbackFrom($user, $value) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFeedbackColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FEEDBACK);
			
			$db->execute($s = Query::generateInsertStm($table, array(FEEDBACK_CREATOR => $user->getID(),
																 FEEDBACK_SUBJECT => $this->getID(),
																 FEEDBACK_VALUE => ($value ? 1 : 0))),
						$table->getName(), $this);
			
			if($db->affected_rows() != 1)
				$db->display_error("User::addFeedbackFrom()"); //Genera un errore ma ritorna comunque $this
		} else $db->display_connect_error("User::addFeedbackFrom()");
		return $this->loadFeedback();
	}

	
	function addContact($contact) {
		return $this->loadContacts();
	}
	
	function removeContact($contact) {
		$contact->delete();
		return $this->loadContacts();
	}

	function save() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineUserColumns();
			$table = Query::getDBSchema()->getTable(TABLE_USER);
			$data = array();
			
			if(isset($this->avatar))
				$data[USER_AVATAR] = $this->getAvatar();
			if(isset($this->birthday))
				$data[USER_BIRTHDAY] = date("Y/m/d", $this->getBirthday());
			if(isset($this->birthplace))
				$data[USER_BIRTHPLACE] = $this->getBirthplace();
			if(isset($this->email))
				$data[USER_E_MAIL] = $this->getEMail();
			if(isset($this->gender))
				$data[USER_GENDER] = $this->getGender();
			if(isset($this->hobbies))
				$data[USER_HOBBIES] = $this->getHobbies();
			if(isset($this->job))
				$data[USER_JOB] = $this->getJob();
			if(isset($this->livingPlace))
				$data[USER_LIVINGPLACE] = $this->getLivingPlace();
			if(isset($this->name))
				$data[USER_NAME] = $this->getName();
			if(isset($this->nickname))
				$data[USER_NICKNAME] = $this->getNickname();
			if(isset($this->password))
				$data[USER_PASSWORD] = $this->getPassword();
			if(isset($this->role))
				$data[USER_ROLE] = $this->getRole();
			if(isset($this->surname))
				$data[USER_SURNAME] = $this->getSurname();
			if(isset($this->visible))
				$data[USER_VISIBLE] = $this->getVisible() ? 1 : 0;
			
			$db->execute($s = Query::generateInsertStm($table, $data), $table->getName(), $this);
			
			if($db->affected_rows() == 1) {
				$this->setID($db->last_inserted_id());
				$db->execute(Query::generateSelectStm(array($table),
												  array(),
												  array(new WhereConstraint($table->getColumn(USER_ID), Operator::EQUAL, $this->getID())),
												  array()));
				
				if($db->num_rows() == 1) {
					$row = $db->fetch_result();
					$this->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[USER_CREATION_DATE])));
					return $this;
				} else $db->display_error("User::save()");
			} else $db->display_error("User::save()");
		} else $db->display_connect_error("User::save()");
		return false;
	}
	
	function update() {
		$old = self::loadFromDatabase($this->getID());
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineUserColumns();
			$table = Query::getDBSchema()->getTable(TABLE_USER);
			$data = array();
			
			if($this->getAvatar() != $old->getAvatar())
				$data[USER_AVATAR] = $this->getAvatar();
			if($this->getBirthday() != $old->getBirthday())
				$data[USER_BIRTHDAY] = $this->getBirthday();
			if($this->getBirthplace() != $old->getBirthplace())
				$data[USER_BIRTHPLACE] = $this->getBirthplace();
			if($this->getEMail() != $old->getEMail())
				$data[USER_E_MAIL] = $this->getEMail();
			if($this->getGender() != $old->getGender())
				$data[USER_GENDER] = $this->getGender();
			if($this->getHobbies() != $old->getHobbies())
				$data[USER_HOBBIES] = $this->getHobbies();
			if($this->getJob() != $old->getJob())
				$data[USER_JOB] = $this->getJob();
			if($this->getLivingPlace() != $old->getLivingPlace())
				$data[USER_LIVINGPLACE] = $this->getLivingPlace();
			if($this->getName() != $old->getName())
				$data[USER_NAME] = $this->getName();
			if($this->getNickname() != $old->getNickname())
				$data[USER_NICKNAME] = $this->getNickname();
			if($this->getPassword() != $old->getPassword())
				$data[USER_PASSWORD] = $this->getPassword();
			if($this->getRole() != $old->getRole())
				$data[USER_ROLE] = $this->getRole();
			if($this->getSurname() != $old->getSurname())
				$data[USER_SURNAME] = $this->getSurname();
			if($this->getVisible() != $old->getVisible())
				$data[USER_VISIBLE] = $this->getVisible() ? 1 : 0;
			
			$db->execute($s = Query::generateUpdateStm($table, $data,
												   array(new WhereConstraint($table->getColumn(USER_ID), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			
			//echo "<p>" . $db->affected_rows() . $s . "</p>"; // DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("User::update()");
		} else $db->display_connect_error("User::update()");
		return false;
	}
	
	function delete() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineUserColumns();
			$table = Query::getDBSchema()->getTable(TABLE_USER);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(USER_ID),Operator::EQUAL,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . serialize($db->affected_rows()) . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("User::delete()");
		} else $db->display_connect_error("User::delete()");
		return false;
	}
	
	static function loadByNickname($nickname, $loadDependences = true) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineUserColumns();
			$table = Query::getDBSchema()->getTable(TABLE_USER);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(USER_NICKNAME), Operator::EQUAL, $nickname)),
												   array()));
			
			if($db->num_rows() == 1) {
				$u = self::createFromDBManager($db);
				if($loadDependences) {
					$loadDependences = false;
					$u->loadContacts()->loadFeedback()->loadFollows()->loadFollowers();
					$loadDependences = true;
				}
				return $u;
			} else $db->display_error("User::loadByNickname()");
		} else $db->display_connect_error("User::loadByNickname()");
		return false;
	}
	
	private static function createFromDBManager($db) {
		$row = $db->fetch_result();
		define_tables(); defineUserColumns();
		$data = array(NICKNAME => $row[USER_NICKNAME],
					  EMAIL => $row[USER_E_MAIL],
					  PASSWORD => $row[USER_PASSWORD],
					  NAME => $row[USER_NAME],
					  SURNAME => $row[USER_SURNAME],
					  GENDER => $row[USER_GENDER],
					  BIRTHDAY => date_timestamp_get(date_create($row[USER_BIRTHDAY])),
					  BIRTHPLACE => $row[USER_BIRTHPLACE],
					  LIVING_PLACE => $row[USER_LIVINGPLACE],
					  AVATAR => $row[USER_AVATAR],
					  HOBBIES => $row[USER_HOBBIES],
					  JOB => $row[USER_JOB],
					  ROLE => $row[USER_ROLE],
					  VISIBLE => $row[USER_VISIBLE]);
		
		$user = new User($data);
		$user->setID(intval($row[USER_ID]))->
			   setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[USER_CREATION_DATE])))->
			   setVerified($row[USER_VERIFIED]);
		
		require_once("common.php");
		$user->setAccessCount(LogManager::getAccessCount("User", $user->getID()));
		
		return $user;
	}
	
	static function loadByMail($mail, $loadDependences = true) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineUserColumns();
			$table = Query::getDBSchema()->getTable(TABLE_USER);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(USER_NICKNAME), Operator::EQUAL, $nickname)),
												   array()));
			
			if($db->num_rows() == 1) {
				$u = self::createFromDBManager($db);
				if($loadDependences) {
					$loadDependences = false;
					$u->loadContacts()->loadFeedback()->loadFollows()->loadFollowers();
					$loadDependences = true;
				}
				return $u;
			} else $db->display_error("User::loadByMail()");
		} else $db->display_connect_error("User::loadByMail()");
		return false;
	}
	
	static function loadFromDatabase($id, $loadDependences = true) {
		// per fermare la ricorsività implicita di questa funzione, infatti loadFromDatabase chiama loadFollows che chiama loadFromDatabase.
		
		//DEBUG
		if(!$loadDependences && DEBUG)
			echo "<font color='blue'>NON carico le dipenzenze.</font>";
		//END DEBUG
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineUserColumns();
			$table = Query::getDBSchema()->getTable(TABLE_USER);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(USER_ID), Operator::EQUAL, $id)),
												   array()));
			
			//echo "<p>" . $db->num_rows() . $s . "</p>"; //DEBUG
			if($db->num_rows() == 1) {
				$user = self::createFromDBManager($db);
				if($loadDependences) {
					$loadDependences = false;
					$user->loadContacts()->loadFeedback()->loadFollows()->loadFollowers();
					$loadDependences = true;
				}
				//echo "<p>" . $user . "</p>"; //DEBUG
				return $user;
			} else $db->display_error("User::loadFromDatabase()");
		} else $db->display_connect_error("User::loadFromDatabase()");
		return false;
	}
	
	function loadFollows() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFollowColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FOLLOW);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(FOLLOW_FOLLOWER), Operator::EQUAL, $this->getID())),
												   array()));
			
			if($db->num_rows() > 0) {
				$fols = array();
				while($row = $db->fetch_result()) {
					define_tables(); defineFollowColumns();
					$f = self::loadFromDatabase(intval($row[FOLLOW_SUBJECT]), false);
					if($f !== false)
						$fols[$f->getID()] = $f;
				}
				return $this->setFollows($fols);
			} else {
				if($db->errno())
					$db->display_error("User::loadFollows()");
			}
		} else $db->display_connect_error("User::loadFollows()");
		return $this->setFollows(array());
	}
	
	function loadFollowers() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFollowColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FOLLOW);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(FOLLOW_SUBJECT), Operator::EQUAL, $this->getID())),
												   array()));
			if($db->num_rows() > 0) {
				$fols = array();
				while($row = $db->fetch_result()) {
					$f = self::loadFromDatabase(intval($row[FOLLOW_FOLLOWER]), false);
					if($f !== false)
						$fols[$f->getID()] = $f;
				}
				return $this->setFollowers($fols);
			} else {
				if($db->errno())
					$db->display_error("User::loadFollowers()");
			}
		} else $db->display_connect_error("User::loadFollowers()");
		return $this->setFollowers(array());
	}
	
	function loadContacts() {
		return $this->setContacts(Contact::loadContactsForUser($this->getID()));
	}
	
	function loadFeedback() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFeedbackColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FEEDBACK);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(FEEDBACK_SUBJECT), Operator::EQUAL, $this->getID())),
												   array()));
			if($db->num_rows() > 0) {
				require_once("strings/strings.php");
				$fb = FEEDBACK_INITIAL_VALUE;
				while($row = $db->fetch_result()) {
					$fb+= (intval($row[FEEDBACK_VALUE]) > 0 ? 1 : -1); //se sul DB è 0 allora è -1 se è positivo allora +1;
				}
				return $this->setFeedback($fb);
			} else if($db->errno()) $db->display_error("User::loadFeedback()");
		} else $db->display_connect_error("User::loadFeedback()");
		return $this->setFeedback(FEEDBACK_INITIAL_VALUE);
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
	
	static function exists($user) {
		//TODO da implementare
	}
}

?>
