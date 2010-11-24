<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");

class UserDao implements Dao {
	private $db;
	private $table_user;
	
	function __construct() {
		$this->table_user = Query::getDBSchema()->getTable(DB::TABLE_USER);
		
		$this->db = new DBManager();
		if($this->db->connect_errno())
			$this->db->display_connect_error("UserDao::__construct()");
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
	
	static function exists($user) {
		//TODO da implementare
	}
}
?>