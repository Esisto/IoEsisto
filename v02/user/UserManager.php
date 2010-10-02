<?php
require_once("common.php");
require_once("settings.php");
require_once(USER_DIR . "/User.php");

class UserManager{
	static $UM_NoUserError = "UM_NoUser";
	static $UM_NoPasswordError = "UM_NoPassword";
	static $UM_NoSessionError = "UM_NoSession";
	
    static function createUser($data, $error = null) {
        $data = Filter::filterArray($data);
        $user = new User($data);
        $u = $user->save();
        //echo "<p>" . $user . "</p>"; //DEBUG
        if($u !== false) {
        	$code = self::generateValidationCode($u);
        	mail($u->getMail(), "Iscrizione a IoEsisto", self::generateValidationMailMessage($code));
        	
			require_once("mail/MailManager.php");
			MailManager::createDirectory(MAILBOX, $u->getID());
			MailManager::createDirectory(TRASH, $u->getID());
			MailManager::createDirectory(SPAM, $u->getID());
        }
        return $user;
    }
    
    private static function generateValidationMailMessage($code) {
    	return '
    	<h3>Benvenuto in IoEsisto.</h3>
    	<p>Per verificare la tua email clicca il link qui sotto. Se il tuo client di posta elettronica non supporta i link copia l\'indirizzo qui sotto e incollalo nella barra dell\'indirizzo del tuo browser.</p>
    	<p><a href="' . $code . '">' . $code . '</a></p> 
    	';
    }
    
    private static function generateValidationCode($user) {
    	return sha1($user->getMail() . $user->getPassword());
    }
    
    static function verifyUser($user, $code, $error = null) {
        $user->setVerified($code == self::generateValidationCode($user));
        return $user->update();
    }
    
    static function login($error = null) {
        if(!isset($_POST["nickname"])) {
            return self::$UM_NoUserError;
        } else {
        	$u = false;
            //check nick and password
           	$u = self::loadUserByNickname($_POST["nickname"]);
           	// assumo che la password mi sia arrivata in chiaro attraverso una connessione sicura
            if($u !== false && $u->getPassword() == sha1($_POST["password"]))
            	$logged = true;
            if($u === false) {
	       		//check mail and password
	            $u = self::loadUserByMail($_POST["nickname"]);
	            // assumo che la password mi sia arrivata in chiaro attraverso una connessione sicura
            	if($u !== false && $u->getPassword() == sha1($_POST["password"]))
	            	$logged = true;
            }
            if($u === false) {
	            if($logged) {
		            //comincia la sessione
		            if(!session_start())
		            	return self::$UM_NoSessionError;
		            //registra le variabili
		            session_register("user", $u->getNickname());
		            //TODO salva cookie
		            return true;
	            }
	            return self::$UM_NoPasswordError;
            }
            return self::$UM_NoUserError;
        }
    }
    
    static function logout($session, $error = null) {
        // elimina la sessione.
        session_unset();
        session_write_close();
    }
    
    static function editUser($user, $data, $error = null) {
        require_once("common.php");
        $data = Filter::filterArray($data);
        return $user->edit($data);
    }
    
    /**
     * Aggiunge $follower ai followers di $subject e ritorna $follower.
     *
     */
    static function followUser($follower, $subject, $error = null) {
        return $follower->follow($subject);
    }
    
    /**
     * Aggiunge un feedback di $creator a $subject con valore $value e ritorna $creator.
     */
    static function feedbackUser($creator, $subject, $value, $error = null) {
        if($subject->addFeedbackFrom($creator, $value) !== false) {
            $subject->loadFeedback(); //forzo il caricamento del feedback perché sembra non vada…
            return $creator;
        }
        return false;
    }
    
    static function stopFollowingUser($follower, $subject, $error = null) {
        return $follower->stopFollowing($subject);
    }
    
    static function deleteFeedbackFromUser($creator, $subject, $error = null) {
        //TODO da implementare in User
    }
    
    static function addContactToUser($data, $user, $error = null) {
        $data[USER] = $user->getID();
        $c = new Contact($data);
        $c->save();
        return $user->addContact($c);
    }
    
    static function editContact($data, $contact, $error = null) {
        return $contact->edit($data);
    }
    
    static function deleteContact($contact, $user, $error = null) {
        return $user->removeContact($contact);
    }
    
    static function deleteUser($user, $error = null) {
        //echo $user;
        return $user->delete();
    }
    
    static function loadUser($id, $error = null) {
        return User::loadFromDatabase($id);
    }
    
    static function loadUserByMail($email, $error = null) {
        return User::loadByMail($email);
    }

    static function loadUserByNickname($nickname, $error = null) {
        return User::loadByNickname($nickname);
    }
}





?>