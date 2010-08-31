<?php
require_once("strings/strings.php");
require_once("settings.php");
require_once(USER_DIR . "/User.php");
require_once(USER_DIR . "/UserManager.php");

class UserTest {
	var $user_data;
	var $user_data2;
	var $user_data_all;
	var $contact_data;
	var $contact_data2;
	
	function __construct() {
		$this->user_data = array(/*AVATAR => null,*/
								  BIRTHDAY => date_timestamp_get(date_create("1985-03-20")),
								  BIRTHPLACE => "QUI!",
								  EMAIL => "no-reply2@ioesisto.com",
								  GENDER => "m",
								  HOBBIES => "porno, siti, soldi",
								  JOB => "nessuno",
								  LIVING_PLACE => "Lì!",
								  NAME => "IO",
								  NICKNAME => "iotransisto",
								  PASSWORD => sha1("cazzi miei!" . sha1("cazzi miei!")),
								  ROLE => "admin",
								  SURNAME => "ESISTO",
								  VISIBLE => true);
		$this->user_data2 = array(/*AVATAR => null,*/
								  BIRTHDAY => date_timestamp_get(date_create("1985-03-20")),
								  BIRTHPLACE => "Lì!",
								  EMAIL => "ma_sti_cazzi@ioesisto.com",
								  GENDER => "f",
								  HOBBIES => "porno, porno, porno",
								  JOB => "prostituto",
								  LIVING_PLACE => "Là!",
								  NAME => "TU",
								  NICKNAME => "iocoesisto",
								  PASSWORD => sha1("ma fatti i cazzi tuoi" . sha1("ma fatti i cazzi tuoi")),
								  ROLE => "admin",
								  SURNAME => "COESISTI?",
								  VISIBLE => false);
		$this->contact_data = array(NAME => "cellulare",
									CONTACT => "340xxxxxxx",
									TYPE => PHONE);
		$this->contact_data2 = array(NAME => "email",
									CONTACT => "iosussisto@ioesisto.com",
									TYPE => EMAIL);
	}
	
	/**
	 * Tests creating, saving, editing User objects.
	 */
	function testUser() {
		// elimino l'utente già creato in precedenza con questo nome.
		$u = UserManager::loadUserByNickname($this->user_data[NICKNAME]);
		//echo "<p>" . $u . "</p>"; //DEBUG
		if($u !== false) {
			$u = UserManager::deleteUser($u);
			//echo "<p>" . $u . "</p>"; //DEBUG
		}
		$u = UserManager::loadUserByNickname($this->user_data2[NICKNAME]);
		if($u !== false)
			$u = UserManager::deleteUser($u);
		$u = UserManager::createUser($this->user_data);
		//echo "<p>" . $u . "</p>"; //DEBUG
		$data = Filter::filterArray($this->user_data);
		
		if(isset($data[AVATAR]))
			if($u->getAvatar() != $data[AVATAR])
				return "User test NOT PASSED: avatar";
		if(isset($data[BIRTHDAY]))
			if($u->getBirthday() != $data[BIRTHDAY])
				return "User test NOT PASSED: birthday";
		if(isset($data[BIRTHPLACE]))
			if($u->getBirthplace() != $data[BIRTHPLACE])
				return "User test NOT PASSED: birthplace";
		if(isset($data[EMAIL]))
			if($u->getEMail() != $data[EMAIL])
				return "User test NOT PASSED: email";
		if(isset($data[GENDER]))
			if($u->getGender() != $data[GENDER])
				return "User test NOT PASSED: gender";
		if(isset($data[HOBBIES]))
			if($u->getHobbies() != $data[HOBBIES])
				return "User test NOT PASSED: hobbies";
		if(isset($data[JOB]))
			if($u->getJob() != $data[JOB])
				return "User test NOT PASSED: job";
		if(isset($data[LIVING_PLACE]))
			if($u->getLivingPlace() != $data[LIVING_PLACE])
				return "User test NOT PASSED: living place";
		if(isset($data[NAME]))
			if($u->getName() != $data[NAME])
				return "User test NOT PASSED: name";
		if(isset($data[NICKNAME]))
			if($u->getNickname() != $data[NICKNAME])
				return "User test NOT PASSED: nickname";
		if(isset($data[PASSWORD]))
			if($u->getPassword() != $data[PASSWORD])
				return "User test NOT PASSED: password";
		if(isset($data[ROLE]))
			if($u->getRole() != $data[ROLE])
				return "User test NOT PASSED: role";
		if(isset($data[SURNAME]))
			if($u->getSurname() != $data[SURNAME])
				return "User test NOT PASSED: surname";
		if(isset($data[VISIBLE]))
			if($u->getVisible() != $data[VISIBLE])
				return "User test NOT PASSED: visible";
		
		// TEST LOAD
		$u2 = UserManager::loadUser($u->getID());
		//echo "<p>" . $u . "<br />" . $u2 . "</p>"; //DEBUG
		if($u->getAvatar() != $u2->getAvatar())
			return "User test NOT PASSED: avatar not loaded";
		//echo "<p>" . $u->getBirthday() . "<br />" . $u2->getBirthday() . " - " . time() . "</p>"; //DEBUG
		if($u->getBirthday() != $u2->getBirthday())
			return "User test NOT PASSED: birthday not loaded";
		if($u->getBirthplace() != $u2->getBirthplace())
			return "User test NOT PASSED: birthplace not loaded";
		if($u->getEMail() != $u2->getEMail())
			return "User test NOT PASSED: email not loaded";
		if($u->getGender() != $u2->getGender())
			return "User test NOT PASSED: gender not loaded";
		if($u->getHobbies() != $u2->getHobbies())
			return "User test NOT PASSED: hobbies not loaded";
		if($u->getJob() != $u2->getJob())
			return "User test NOT PASSED: job not loaded";
		if($u->getLivingPlace() != $u2->getLivingPlace())
			return "User test NOT PASSED: living place not loaded";
		if($u->getName() != $u2->getName())
			return "User test NOT PASSED: name not loaded";
		if($u->getNickname() != $u2->getNickname())
			return "User test NOT PASSED: nickname not loaded";
		if($u->getPassword() != $u2->getPassword())
			return "User test NOT PASSED: password not loaded";
		if($u->getRole() != $u2->getRole())
			return "User test NOT PASSED: role not loaded";
		if($u->getSurname() != $u2->getSurname())
			return "User test NOT PASSED: surname not loaded";
		if($u->getVisible() != $u2->getVisible())
			return "User test NOT PASSED: visible not loaded";
		if($u->getCreationDate() != $u2->getCreationDate())
			return "User test NOT PASSED: creation date not loaded";
		if($u->getID() != $u2->getID())
			return "User test NOT PASSED: ID not loaded";
		
		// TEST EDIT
		//echo $u;
		$u2 = UserManager::editUser($u, $this->user_data2);
		$u = UserManager::loadUser($u->getID());
		//echo "<p>" . $u . "<br />" . $u2 . "</p>"; //DEBUG
		if(!isset($u) || $u === false)
			return "non VAAA!!";
		if(!isset($u2) || $u2 === false)
			return "AAARGH";
		if($u->getAvatar() != $u2->getAvatar())
			return "User test NOT PASSED: avatar not updated";
		//echo "<p>" . $u->getBirthday() . "<br />" . $u2->getBirthday() . " - " . time() . "</p>"; //DEBUG
		if($u->getBirthday() != $u2->getBirthday())
			return "User test NOT PASSED: birthday not updated";
		if($u->getBirthplace() != $u2->getBirthplace())
			return "User test NOT PASSED: birthplace not updated";
		if($u->getEMail() != $u2->getEMail())
			return "User test NOT PASSED: email not updated";
		if($u->getGender() != $u2->getGender())
			return "User test NOT PASSED: gender not updated";
		if($u->getHobbies() != $u2->getHobbies())
			return "User test NOT PASSED: hobbies not updated";
		if($u->getJob() != $u2->getJob())
			return "User test NOT PASSED: job not updated";
		if($u->getLivingPlace() != $u2->getLivingPlace())
			return "User test NOT PASSED: living place not updated";
		if($u->getName() != $u2->getName())
			return "User test NOT PASSED: name not updated";
		if($u->getNickname() != $u2->getNickname())
			return "User test NOT PASSED: nickname not updated";
		if($u->getPassword() != $u2->getPassword())
			return "User test NOT PASSED: password not updated";
		if($u->getRole() != $u2->getRole())
			return "User test NOT PASSED: role not updated";
		if($u->getSurname() != $u2->getSurname())
			return "User test NOT PASSED: surname not updated";
		if($u->getVisible() != $u2->getVisible())
			return "User test NOT PASSED: visible not updated";
		if($u->getCreationDate() != $u2->getCreationDate())
			return "User test NOT PASSED: creation date not updated";
		if($u->getID() != $u2->getID())
			return "User test NOT PASSED: ID not updated";
		
		return "User test passed";
	}
	
	/**
	 * Tests creating, saving, editing, Contact objects.
	 */
	function testContacts() {
		$u = UserManager::loadUserByNickname($this->user_data[NICKNAME]);
		if($u === false)
			$u = UserManager::loadUserByNickname($this->user_data2[NICKNAME]);
		if($u === false)
			$u = UserManager::createUser($this->user_data);
		
		$data = $this->contact_data;
		$data[USER] = $u->getID();
		$utente = UserManager::addContactToUser($data, $u);
		//echo "<p>" . $u . "</p>"; //DEBUG
		$data = Filter::filterArray($this->contact_data);
		
		
		if(count($utente->getContacts()) == 0)
			return "Test Contact NOT PASSED: not added.";
		$cs = $utente->getContacts();
		$c = $cs[0];
		if(isset($this->contact_data[NAME]))
			if($this->contact_data[NAME] != $c->getName())
				return "Test Contact NOT PASSED: contact name.";
		if(isset($this->contact_data[CONTACT]))
			if($this->contact_data[CONTACT] != $c->getContact())
				return "Test Contact NOT PASSED: contact.";
		if(isset($this->contact_data[TYPE]))
			if($this->contact_data[TYPE] != $c->getType())
				return "Test Contact NOT PASSED: contact type.";
		if($utente->getID() != $c->getUser())
			return "Test Contact NOT PASSED: user.";
		
		return "Test Contact passed.";
	}
	
	/**
	 * Tests deleting Contact objects
	 */
	function testDeleteContact() {
		$u = UserManager::loadUserByNickname($this->user_data[NICKNAME]);
		if($u === false)
			$u = UserManager::loadUserByNickname($this->user_data2[NICKNAME]);
		if($u === false)
			$u = UserManager::createUser($this->user_data);
		
		$data = $this->contact_data;
		$data[USER] = $u->getID();
		$utente = UserManager::addContactToUser($data, $u);
		$cs = $u->getContacts();
		$c = $cs[count($cs)-1];
		//echo "<p>" . $u . "</p>"; //DEBUG
		$oldcontactscount = count($utente->getContacts());
		
		$utente->removeContact($c);
		
		if(count($utente->getContacts()) >= $oldcontactscount)
			return "Test Contact deleting NOT PASSED: not removed.";
		
		return "Test Contact deleting passed.";
	}
	
	/**
	 * Tests adding feedback.
	 */
	function testAddFeedback() {
		$u1 = UserManager::loadUserByNickname($this->user_data[NICKNAME]);
		if($u1 === false)
			$u1 = UserManager::createUser($this->user_data);
		$oldfeedbackvalue = $u1->getFeedback();
		
		$u2 = UserManager::loadUserByNickname($this->user_data2[NICKNAME]);
		if($u2 === false)
			$u2 = UserManager::createUser($this->user_data2);
		echo "<hr>";
		
		UserManager::feedbackUser($u2, $u1, false);
		echo "<p>" . $oldfeedbackvalue . " - " . $u1->getFeedback() . "</p>"; //DEBUG
		if($u1->getFeedback() == $oldfeedbackvalue)
			return "Test feedback NOT PASSED: not updated.";
		
		return "Test feedback passed.";
	}
	
	/**
	 * Tests removing feedback.
	 */
	function testRemoveFeedback() {
		return "NOT implemented.";
	}
	
	/**
	 * Tests following users.
	 */
	function testFollow() {
		$u1 = UserManager::loadUserByNickname($this->user_data[NICKNAME]);
		if($u1 === false)
			$u1 = UserManager::createUser($this->user_data);
		
		$u2 = UserManager::loadUserByNickname($this->user_data2[NICKNAME]);
		if($u2 === false)
			$u2 = UserManager::createUser($this->user_data2);

		foreach($u1->getFollowers() as $follower) {
			if($follower->getID() == $u2->getID())
				UserManager::stopFollowingUser($u2, $u1);
		}
		
		$oldfollowerscount = count($u1->getFollowers());
		$oldfollowscount = count($u2->getFollows());
		
		UserManager::followUser($u2, $u1);
		//echo "<p>" . $u1 . "</p>"; //DEBUG
		
		if(count($u1->getFollowers()) <= $oldfollowerscount)
			return "Test follow NOT PASSED: not updated subject.";
		if(count($u2->getFollows()) <= $oldfollowscount)
			return "Test follow NOT PASSED: not updated follower.";
		
		return "Test follow passed.";
	}
	
	/**
	 * Tests removing follow.
	 */
	function testDeleteFollow() {
		$u1 = UserManager::loadUserByNickname($this->user_data[NICKNAME]);
		if($u1 === false)
			$u1 = UserManager::createUser($this->user_data);
		
		$u2 = UserManager::loadUserByNickname($this->user_data2[NICKNAME]);
		if($u2 === false)
			$u2 = UserManager::createUser($this->user_data2);
				
		UserManager::followUser($u2, $u1);
		//echo "<p>" . $u1 . "</p>"; //DEBUG
		$oldfollowerscount = count($u1->getFollowers());
		$oldfollowscount = count($u2->getFollows());
		
		UserManager::stopFollowingUser($u2, $u1);
		
		if(count($u1->getFollowers()) >= $oldfollowerscount)
			return "Test follow deleting NOT PASSED: not updated subject.";
		if(count($u2->getFollows()) >= $oldfollowscount)
			return "Test follow deleting NOT PASSED: not updated follower.";
		
		return "Test follow deleting passed.";	
	}
}

?>