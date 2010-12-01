<?php
require_once("common.php");
require_once("settings.php");
require_once(USER_DIR . "/User.php");
require_once("session.php");

class UserManager {
	const UM_NoUserError = "UM_NoUser";
	const UM_NoPasswordError = "UM_NoPassword";
	const UM_NoSessionError = "UM_NoSession";
	
    static function createUser($data, $error = null) {
    	$data["password"] = Filter::encodePassword($data["passowrd"]);
        $data = Filter::filterArray($data);
        $user = new User($data);
        $u = $user->save();
        //echo "<p>" . $user . "</p>"; //DEBUG
        if($u !== false) {
        	//invia una mail per permettere all'utente di convalidare la sua casella.
        	$code = self::generateValidationCode($u);
        	mail($u->getMail(), "Iscrizione a IoEsisto", self::generateValidationMailMessage($code));
        	
        	//genera una collection di preferiti
        	require_once 'post/collection/CollectionManager.php';
        	$data = array("title" => "Preferiti",
        				  "author" => $u->getID(),
        				  "categories" => "favourites",
        				  "visible" => false);
        	CollectionManager::createCollection($data);
        	
        	//genera tre directory email: mailbox, cestino e spam
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
    	return Filter::hash($user->getMail() . $user->getPassword());
    }
    
    static function verifyUser($user, $code, $error = null) {
        $user->setVerified($code == self::generateValidationCode($user));
        return $user->update();
    }
    
    static function login($data) {
        if(!isset($data["username"])) {
            return self::UM_NoUserError;
        } else {
		
        	$u = false; $logged = false;
            //check nick and password
           	$u = self::loadUserByNickname($data["username"]);
           	// assumo che la password mi sia arrivata in chiaro attraverso una connessione sicura
                if($u !== false && $u->getPassword() == Filter::encodePassword($data["password"]))
                
            	$logged = true;
            if($u === false) {
	       		//check mail and password
	            $u = self::loadUserByMail($data["username"]);
	            // assumo che la password mi sia arrivata in chiaro attraverso una connessione sicura
            	if($u !== false && $u->getPassword() == Filter::encodePassword($data["password"]))
	            	header("location: " . FileManager::appendToRootPath());
            }
            if($u !== false) {
	            if($logged) {
		            if(Session::start($u))
		            	return true;
			    	else
						return self::UM_NoSessionError; 
	            }
	            return self::UM_NoPasswordError;
            }
            return self::UM_NoUserError;
        }
    }
    
    static function logout($session, $error = null) {
        // elimina la sessione.
       Session::destroy();
    }
    
    static function editUser($user, $data, $error = null) {
        require_once("common.php");
        $data["password"] = Filter::encodePassword($data["password"]);
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
    
    static function loadUser($id, $loadDependencies = true, $error = null) {
        return User::loadFromDatabase($id, $loadDependencies);
    }
    
    static function loadUserByMail($email, $loadDependencies = true, $error = null) {
        return User::loadByMail($email, $loadDependencies);
    }

    static function loadUserByNickname($nickname, $loadDependencies = true, $error = null) {
        return User::loadByNickname($nickname, $loadDependencies);
    }
    
    static function userExists($user) {
    	return User::exists($user);
    }
}
?>
