<?php
require_once("common.php");
require_once("settings.php");
require_once(USER_DIR . "/User.php");

class UserManager{
    static function createUser($data) {
        $data = Filter::filterArray($data);
        $user = new User($data);
        $u = $user->save();
        //echo "<p>" . $user . "</p>"; //DEBUG
        if($u !== false) {
            //TODO send verification mail.
            //TODO crea cartelle mail per spam, inbox e trash
        }
        return $user;
    }
    
    static function verifyUser($user, $code) {
        $mail = $user->getMail();
        $password = $user->getPassword();
        
        $user->setVerified($code == sha1($mail . $password)); //TODO pensare ed estrarre una funzione che crei questo codice.
        return $user->update();
    }
    
    static function login($data, $pwd) {
        if($nick) {
            
            //TODO check nick e password,
            //TODO comincia la sessione
            //TODO registra le variabili
            //TODO salva cookie
        }
    }
    
    static function logout($session) {
        //TODO elimina la sessione.
    }
    
    static function editUser($user, $data) {
        require_once("common.php");
        $data = Filter::filterArray($data);
        return $user->edit($data);
    }
    
    /**
     * Aggiunge $follower ai followers di $subject e ritorna $follower.
     *
     */
    static function followUser($follower, $subject) {
        return $follower->follow($subject);
    }
    
    /**
     * Aggiunge un feedback di $creator a $subject con valore $value e ritorna $creator.
     */
    static function feedbackUser($creator, $subject, $value) {
        if($subject->addFeedbackFrom($creator, $value) !== false) {
            $subject->loadFeedback(); //forzo il caricamento del feedback perché sembra non vada…
            return $creator;
        }
        return false;
    }
    
    static function stopFollowingUser($follower, $subject) {
        return $follower->stopFollowing($subject);
    }
    
    static function deleteFeedbackFromUser($creator, $subject) {
        //TODO da implementare in User
    }
    
    static function addContactToUser($data, $user) {
        $data[USER] = $user->getID();
        $c = new Contact($data);
        $c->save();
        return $user->addContact($c);
    }
    
    static function editContact($data, $contact) {
        return $contact->edit($data);
    }
    
    static function deleteContact($contact, $user) {
        return $user->removeContact($contact);
    }
    
    static function deleteUser($user) {
        //echo $user;
        return $user->delete();
    }
    
    static function loadUser($id) {
        return User::loadFromDatabase($id);
    }
    
    static function loadUserByMail($email) {
        return User::loadByMail($email);
    }

    static function loadUserByNickname($nickname) {
        return User::loadByNickname($nickname);
    }
}





?>