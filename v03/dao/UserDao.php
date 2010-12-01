<?php //TODO
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Post.php");

class UserDao implements Dao {
	const OBJECT_CLASS = "Post";
	private $loadDependences = true;
	private $loadReports = false;
	
	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_USER);
	}

	function setLoadReports($load) {
		settype($load, "boolean");
		$this->loadReports = $load;
		return $this;
	}
	function setLoadDependences($load) {
		settype($load, "boolean");
		$this->loadDependences = $load;
		return $this;
	}
	
	function load($id) {
		parent::load($id);
		$this->db->execute($s = Query::generateSelectStm(array($this->table), array(),
												   array(new WhereConstraint($this->table->getColumn(DB::USER_ID), Operator::EQUAL, intval($id))),
												   array()));
			
		if($db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $db->fetch_result();
		$user = $this->createFromDBRow($row);
		return $user;
	}

	static function loadByMail($mail) {
		parent::load($mail);
		$this->db->execute($s = Query::generateSelectStm(array($this->table), array(),
												   array(new WhereConstraint($this->table->getColumn(DB::USER_E_MAIL), Operator::EQUAL, $mail)),
												   array()));
			
		if($db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");

		$row = $db->fetch_result();
		$user = $this->createFromDBRow($row);
		return $user;
	}

	static function loadByNickname($nickname) {
		parent::load($nickname);
		$this->db->execute($s = Query::generateSelectStm(array($this->table), array(),
												   array(new WhereConstraint($this->table->getColumn(DB::USER_NICKNAME), Operator::EQUAL, $nickname)),
												   array()));
			
		if($db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");

		$row = $db->fetch_result();
		$user = $this->createFromDBRow($row);
		return $user;
	}

	private function createFromDBRow($row) {
		$user = new User($row[DB::USER_NICKNAME], $row[DB::USER_E_MAIL], $row[DB::USER_PASSWORD]);
		$user->setName($row[DB::USER_NAME])
			 ->setSurname($row[DB::USER_SURNAME])
			 ->setGender($row[DB::USER_GENDER])
			 ->setBirthday(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::USER_BIRTHDAY])))
			 ->setBirthplace($row[DB::USER_BIRTHPLACE])
			 ->setLivingPlace($row[DB::USER_LIVINGPLACE])
			 ->setHobbies($row[DB::USER_HOBBIES])
			 ->setJob($row[DB::USER_JOB])
			 ->setRole($row[DB::USER_ROLE])
			 ->setVisible($row[DB::USER_VISIBLE])
			 ->setID(intval($row[DB::USER_ID]))
			 ->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::USER_CREATION_DATE])))
			 ->setVerified($row[DB::USER_VERIFIED]);
		try {
			require_once("dao/ResourceDao.php");
			$resourceDao = new ResourceDao();
			$user->setAvatar($resourceDao->quickLoad($row[DB::USER_AVATAR]));
		} catch(Exception $e) {
			$user->setAvatar($resourceDao->quickLoad(EMPTY_AVATAR));
		}
		
		if($this->loadDependences) {
			require_once("dao/ContactDao.php");
			$contactDao = new ContactDao();
			$contactDao->loadAll($user);
			require_once("dao/FollowDao.php");
			$followDao = new FollowDao();
			$followDao->loadAllFollowers($user);
			$followDao->loadAllFollows($user);
			require_once("dao/FeedbackDao.php");
			$feedbackDao = new FeedbackDao();
			$feedbackDao->loadAll($user);
		}
		$user = Session::getUser();
		if($this->loadReports && $user->isUserManager()) { //FIXME usa authorizationManager o roleManager
			require_once 'dao/ReportDao.php';
			$reportDao = new ReportDao();
			$reportDao->loadAll($p);
		}
		
		//setto lo stato
		$user->setEditable($row[DB::EDITABLE])
			 ->setRemovable($row[DB::REMOVABLE]);
		$user->setBlackContent($row[DB::BLACK_CONTENT])
			 ->setRedContent($row[DB::RED_CONTENT])
			 ->setYellowContent($row[DB::YELLOW_CONTENT])
			 ->setAutoBlackContent($row[DB::AUTO_BLACK_CONTENT]);
			 
		$user->setAccessCount($this->getAccessCount($user));
		return $user;
	}

	function save($user) {
		parent::save($user, self::OBJECT_CLASS);
		$data = array();
			
		if(isset($user->avatar))
			$data[DB::USER_AVATAR] = $user->getAvatar();
		if(isset($user->birthday))
			$data[DB::USER_BIRTHDAY] = date("Y/m/d", $user->getBirthday());
		if(isset($user->birthplace))
			$data[DB::USER_BIRTHPLACE] = $user->getBirthplace();
		if(isset($user->email))
			$data[DB::USER_E_MAIL] = $user->getEMail();
		if(isset($user->gender))
			$data[DB::USER_GENDER] = $user->getGender();
		if(isset($user->hobbies))
			$data[DB::USER_HOBBIES] = $user->getHobbies();
		if(isset($user->job))
			$data[DB::USER_JOB] = $user->getJob();
		if(isset($user->livingPlace))
			$data[DB::USER_LIVINGPLACE] = $user->getLivingPlace();
		if(isset($user->name))
			$data[DB::USER_NAME] = $user->getName();
		if(isset($user->nickname))
			$data[DB::USER_NICKNAME] = $user->getNickname();
		if(isset($user->password))
			$data[DB::USER_PASSWORD] = $user->getPassword();
		if(isset($user->role))
			$data[DB::USER_ROLE] = $user->getRole();
		if(isset($user->surname))
			$data[DB::USER_SURNAME] = $user->getSurname();
		if(isset($user->visible))
			$data[DB::USER_VISIBLE] = $user->getVisible() ? 1 : 0;
		$data[DB::USER_CREATION_DATE] = date("Y-m-d G:i:s", $_SERVER["REQUEST_TIME"]);
			
		$this->db->execute($s = Query::generateInsertStm($this->table, $data), $this->table->getName(), $user);
			
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		
		$u = $this->load(intval($db->last_inserted_id()));
		
		//TODO salvo lo stato
		return $u;
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