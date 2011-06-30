<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/User.php");

class UserDao extends Dao {
	const OBJECT_CLASS = "User";
	private $loadDependences = true;
	private $loadReports = false;
	private $loadAccessCount = true;
	
	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_USER);
	}

	function setLoadReports($load) {
		settype($load, "boolean");
		$this->loadReports = $load;
		return $this;
	}
	function setLoadAccessCount($load) {
		settype($load, "boolean");
		$this->loadAccessCount = $load;
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
			
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $this->db->fetch_result();
		$user = $this->createFromDBRow($row);
		return $user;
	}

	function loadByMail($mail) {
		parent::load($mail);
		$this->db->execute($s = Query::generateSelectStm(array($this->table), array(),
												   array(new WhereConstraint($this->table->getColumn(DB::USER_E_MAIL), Operator::EQUAL, $mail)),
												   array()));
			
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");

		$row = $this->db->fetch_result();
		$user = $this->createFromDBRow($row);
		return $user;
	}

	function loadByNickname($nickname) {
		parent::load($nickname);
		$this->db->execute($s = Query::generateSelectStm(array($this->table), array(),
												   array(new WhereConstraint($this->table->getColumn(DB::USER_NICKNAME), Operator::EQUAL, $nickname)),
												   array()));
			
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");

		$row = $this->db->fetch_result();
		$user = $this->createFromDBRow($row);
		return $user;
	}
	
	function quickLoad($id) {
		$loadD = $this->loadDependences; $this->setLoadDependences(false);
		$loadR = $this->loadReports; $this->setLoadReports(false);
		$this->loadAccessCount = false;
		try {
			$u = $this->load($id);
			$this->setLoadDependences($loadD);
			$this->setLoadReports($loadR);
			$this->loadAccessCount = true;
			return $u;
		} catch (Exception $e) {
			$this->setLoadDependences($loadD);
			$this->setLoadReports($loadR);
			$this->loadAccessCount = true;
			throw $e;
		}
	}

	private function createFromDBRow($row) {
		$user = new User($row[DB::USER_NICKNAME], $row[DB::USER_E_MAIL], $row[DB::USER_PASSWORD]);
		
		$user->setName($row[DB::USER_NAME])
			 ->setSurname($row[DB::USER_SURNAME])
			 ->setGender($row[DB::USER_GENDER]);
		if(!is_null($row[DB::USER_BIRTHDAY]))
			$user->setBirthday(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::USER_BIRTHDAY])));
		$user->setBirthplace($row[DB::USER_BIRTHPLACE])
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
//			try {
//				$user->setAvatar($resourceDao->quickLoad(EMPTY_AVATAR));
//			} catch (Exception $e1) {
//				//DEBUG da togliere più avanti, quando ci saranno le immagini.
//			}
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
		if($this->loadReports && AuthorizationManager::canUserDo(AuthorizationManager::READ_REPORTS, $user)) {
			require_once 'dao/ReportDao.php';
			$reportDao = new ReportDao();
			$reportDao->loadAll($user);
		}
		
		//setto lo stato
		$user->setEditable($row[DB::EDITABLE])
			 ->setRemovable($row[DB::REMOVABLE]);
		$user->setBlackContent($row[DB::BLACK_CONTENT])
			 ->setRedContent($row[DB::RED_CONTENT])
			 ->setYellowContent($row[DB::YELLOW_CONTENT])
			 ->setAutoBlackContent($row[DB::AUTO_BLACK_CONTENT]);
			 
		if($this->loadAccessCount)
			$user->setAccessCount($this->getAccessCount($user));
		return $user;
	}

	function save($user) {
		parent::save($user, self::OBJECT_CLASS);
		
		$data = array();	
		if(!is_null($user->getAvatar()))
			$data[DB::USER_AVATAR] = $user->getAvatar();
		if(!is_null($user->getBirthday()))
			$data[DB::USER_BIRTHDAY] = date("Y/m/d", $user->getBirthday());
		if(!is_null($user->getBirthplace()))
			$data[DB::USER_BIRTHPLACE] = $user->getBirthplace();
		if(!is_null($user->getEMail()))
			$data[DB::USER_E_MAIL] = $user->getEMail();
		if(!is_null($user->getGender()))
			$data[DB::USER_GENDER] = $user->getGender();
		if(!is_null($user->getHobbies()))
			$data[DB::USER_HOBBIES] = $user->getHobbies();
		if(!is_null($user->getJob()))
			$data[DB::USER_JOB] = $user->getJob();
		if(!is_null($user->getLivingPlace()))
			$data[DB::USER_LIVINGPLACE] = $user->getLivingPlace();
		if(!is_null($user->getName()))
			$data[DB::USER_NAME] = $user->getName();
		if(!is_null($user->getNickname()))
			$data[DB::USER_NICKNAME] = $user->getNickname();
		if(!is_null($user->getPassword()))
			$data[DB::USER_PASSWORD] = $user->getPassword();
		if(!is_null($user->getRole()))
			$data[DB::USER_ROLE] = $user->getRole();
		if(!is_null($user->getSurname()))
			$data[DB::USER_SURNAME] = $user->getSurname();
		if(!is_null($user->getVisible()))
			$data[DB::USER_VISIBLE] = $user->getVisible() ? 1 : 0;
		$data[DB::USER_CREATION_DATE] = date("Y-m-d G:i:s", $_SERVER["REQUEST_TIME"]);
			
		$this->db->execute($s = Query::generateInsertStm($this->table, $data), $this->table->getName(), $user);
			
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		
		$u = $this->load(intval($this->db->last_inserted_id()));
		
		$this->updateState($u);
		return $u;
	}
	
	function update($user, $editor) {
		parent::update($user, $editor, self::OBJECT_CLASS);
		
		$old = $this->quickLoad($user->getID());
		if(is_null($old))
			throw new Exception("L'oggetto da modificare non esiste.");
		
		$data = array();
		if($user->getAvatar() != $old->getAvatar())
			$data[DB::USER_AVATAR] = $user->getAvatar();
		if($user->getBirthday() != $old->getBirthday())
			$data[DB::USER_BIRTHDAY] = $user->getBirthday();
		if($user->getBirthplace() != $old->getBirthplace())
			$data[DB::USER_BIRTHPLACE] = $user->getBirthplace();
		if($user->getEMail() != $old->getEMail())
			$data[DB::USER_E_MAIL] = $user->getEMail();
		if($user->getGender() != $old->getGender())
			$data[DB::USER_GENDER] = $user->getGender();
		if($user->getHobbies() != $old->getHobbies())
			$data[DB::USER_HOBBIES] = $user->getHobbies();
		if($user->getJob() != $old->getJob())
			$data[DB::USER_JOB] = $user->getJob();
		if($user->getLivingPlace() != $old->getLivingPlace())
			$data[DB::USER_LIVINGPLACE] = $user->getLivingPlace();
		if($user->getName() != $old->getName())
			$data[DB::USER_NAME] = $user->getName();
		if($user->getNickname() != $old->getNickname())
			$data[DB::USER_NICKNAME] = $user->getNickname();
		if($user->getPassword() != $old->getPassword())
			$data[DB::USER_PASSWORD] = $user->getPassword();
		if($user->getRole() != $old->getRole())
			$data[DB::USER_ROLE] = $user->getRole();
		if($user->getSurname() != $old->getSurname())
			$data[DB::USER_SURNAME] = $user->getSurname();
		if($user->getVisible() != $old->getVisible())
			$data[DB::USER_VISIBLE] = $user->getVisible() ? 1 : 0;
			
		$this->db->execute($s = Query::generateUpdateStm($this->table, $data,
														 array(new WhereConstraint($this->table->getColumn(DB::USER_ID), Operator::EQUAL, $user->getID()))),
							$this->table->getName(), $user);
		
		//TODO aggiungere authenitcationManager
		//aggiorno lo stato della risorsa (se chi l'ha modificata è un redattore).
		//if(AuthenticationManager::isUserManager($editor)) {
		//	$resource->setEditable(false);
		//	$resource->setRemovable(false);
		//	$this->updateState($resource);
		//}
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
		return $user;
	}
	
	function delete($user) {
		parent::delete($user, self::OBJECT_CLASS);
		
		//carico la risorsa, completa dei suoi derivati (che andrebbero persi).
		$loadR = $this->loadReports; $this->loadReports = true;
		$loadD = $this->loadDependences; $this->loadDependences = true;
		$u_complete = null;
		try {
			$u_complete = $this->load($user->getID());
			$this->loadReports = $loadR;
			$this->loadDependences = $loadD;
		} catch(Exception $e) {
			$this->loadReports = $loadR;
			$this->loadDependences = $loadD;
			throw $e;
		}
		$this->db->execute(Query::generateDeleteStm($this->table,
													array(new WhereConstraint($this->table->getColumn(DB::USER_ID),Operator::EQUAL,$user->getID()))),
						  $this->table->getName(), $user);
		
		//salvo la risorsa nella storia.
		$this->saveHistory($u_complete, "DELETED");
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore eliminando il dato. Riprovare.");
		return $user;
	}
	
	function exists($user) {
		try {
			$u = $this->quickLoad($user->getID());
			return is_subclass_of($u, self::OBJECT_CLASS);
		} catch(Exception $e) {
			return false;
		}
	}

	function updateState($user) {
		parent::updateState($user, $this->table, DB::USER_ID);
	}
	
	protected function getAccessCount($user) {
		parent::getAccessCount($user, $this->table, DB::USER_ID);
	}
}
?>