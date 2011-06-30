<?php
require_once("dataobject/User.php");
require_once("dao/UserDao.php");
require_once("filter.php");
require_once("session.php");

class UserManager {
	
    static function createUser($nickname, $email, $password, $role) {
 	$pwd = Filter::encodePassword($password);
        $user = new User($nickname, $email, $pwd);
	$user->setRole($role);
        
        $userdao = new UserDao();
        $u = $userdao->save($user);
        
        //invia una mail per permettere all'utente di convalidare la sua casella.
        $code = self::generateValidationCode($u);
        mail($u->getEMail(), "Iscrizione a IoEsisto", self::generateValidationMailMessage($code));
     	
        //genera una collection di preferiti
        require_once 'manager/CollectionManager.php';
        $data = array("title" => "Preferiti",
     			  "author" => $u->getID(),
     			  "categories" => "favourites",
     			  "visible" => false,
			  "type" => "collection");
	//TODO la collection viene creata nel db ma da fatal error, forse il problema è nell'override di setContent dove controlla che si stia inserendo un post ma il contenuto è false
        //CollectionManager::createCollection($data);
	
	//TODO creare MailManager
        //genera tre directory email: mailbox, cestino e spam
		//require_once("manager/MailManager.php");
		//MailManager::createDirectory(MAILBOX, $u->getID());
		//MailManager::createDirectory(TRASH, $u->getID());
		//MailManager::createDirectory(SPAM, $u->getID());
		
        return $u;
    }
    
    function checkMail($email){
	$email = trim($email);
	if(!$email) {
		return false;
	}
	$num_at = count(explode( '@', $email )) - 1;
	if($num_at != 1) {
		return false;
	}
 	if(strpos($email,';') || strpos($email,',') || strpos($email,' ')) {
		return false;
	}
 	if(!preg_match( '/^[\w\.\-]+@\w+[\w\.\-]*?\.\w{1,4}$/', $email)) {
		return false;
	}
	return true;
    }
    
    private static function generateValidationMailMessage($code) {
	 	return '
		 	<h3>Benvenuto in IoEsisto.</h3>
		 	<p>Per verificare la tua email clicca il link qui sotto. Se il tuo client di posta elettronica non supporta i link copia l\'indirizzo qui sotto e incollalo nella barra dell\'indirizzo del tuo browser.</p>
		 	<p><a href="' . $code . '">' . $code . '</a></p> 
		 	';
    }
    
    private static function generateValidationCode($user) {
 		return Filter::hash($user->getEMail() . $user->getPassword());
    }
    
    static function verifyUser($user, $code) {
        $user->setVerified($code == self::generateValidationCode($user));
        $userdao = new UserDao();
        return $userdao->update($user);
    }
    
    static function login($data) {
        if(!isset($data["username"]))
     		throw new Exception("Non hai inserito il nickname");

    	$u = false; $logged = false;
        try {
        	//carico l'utente come se username fosse un nickname
	    	$u = self::loadUserByNickname($data["username"]);
        } catch (Exception $e) {
	     	//carico l'utente come se username fosse una mail
	     	$u = self::loadUserByMail($data["username"]);
        }
	    // assumo che la password mi sia arrivata in chiaro attraverso una connessione sicura
        if($u->getPassword() == Filter::encodePassword($data["password"]))
	        $logged = true;
        
	    if($logged) {
	    	if(Session::start($u)) {
	    		return true;
	    	} else {
     			throw new Exception("Non sono abilitate le sessioni.");
	    	}
        }
     	throw new Exception("Username o password sono errate.");
    }
    
    static function logout($session, $error = null) {
        // elimina la sessione.
       Session::destroy();
    }
    
    static function editUser($user, $data) {
        if(isset($data[User::PASSWORD]) && $data[User::PASSWORD] != "")
	   $data[User::PASSWORD] = Filter::encodePassword($data[User::PASSWORD]);
        $data = Filter::filterArray($data);
        $user->edit($data);
        $userdao = new UserDao();
        return $userdao->update($user, $editor);
    }
    
    /**
     * Aggiunge $follower ai followers di $subject e ritorna $follower.
     *
     */
    static function followUser($follower, $subject, $error = null) {
        return $follower->follow($subject); //TODO da implementare in feedbackdao
    }
    
    /**
     * Aggiunge un feedback di $creator a $subject con valore $value e ritorna $creator.
     */
    static function feedbackUser($creator, $subject, $value, $error = null) { //TODO da implementare in feedbackdao
        if($subject->addFeedbackFrom($creator, $value) !== false) {
            $subject->loadFeedback(); //forzo il caricamento del feedback perché sembra non vada…
            return $creator;
        }
        return false;
    }
    
    static function stopFollowingUser($follower, $subject, $error = null) {
    	return $follower->stopFollowing($subject); //TODO da implementare in feedbackdao
    }
    
    static function deleteFeedbackFromUser($creator, $subject, $error = null) { //
        //TODO da implementare in feedbackdao
    }
    
    static function addContactToUser($data, $user) {
    	require_once 'dataobject/Contact.php';
    	require_once 'dao/ContactDao.php';
        $data[Contact::USER] = $user->getID();
        $c = new Contact($data);
        $contactdao = new ContactDao();
        $contact = $contactdao->save($c);
        return $user->addContact($contact);
    }
    
    static function editContact($data, $contact) {
    	$contact->edit($data);
    	require_once 'dao/ContactDao.php';
        $contactdao = new ContactDao();
        return $contactdao->update($contact, Session::getUser()); 
    }
    
    static function deleteContact($contact) {
    	require_once 'dao/ContactDao.php';
        $contactdao = new ContactDao();
        return $contactdao->delete($contact); 
    }
    
    static function deleteUser($user) {
        $userdao = new UserDao();
        return $userdao->delete($user);
    }
    
    static function loadUser($id, $loadDependencies = true) {
        $userdao = new UserDao();
        $userdao->setLoadDependences($loadDependencies);
        return $userdao->load($id);
    }
    
    static function loadUserByMail($email, $loadDependencies = true) {
        $userdao = new UserDao();
        $userdao->setLoadDependences($loadDependencies);
        return $userdao->loadByMail($email);
    }

    static function loadUserByNickname($nickname, $loadDependencies = true) {
        $userdao = new UserDao();
        $userdao->setLoadDependences($loadDependencies);
        return $userdao->loadByNickname($nickname);
    }
    
    static function userExists($user) {
        $userdao = new UserDao();
		return $userdao->exists($user);
    }
    
    static function emailExist($email){
	try {
	    self::loadUserByMail($email);
	    return true;
	} catch(Exception $e) {
	    return false;
	}
    }
    
    static function nicknameExist($nickname){
	try {
	    self::loadUserByNickname($nickname);
	    return true;
	} catch(Exception $e) {
	    return false;
	}
    }
}
?>
