<?php
require_once("user/User.php");

session_start();

/**
 * Classe Session 
 * è una classe che contiene solo metodi statici e
 * gestisce le sessioni php	
 */
class Session {
	
	/**
	 * Metodo Session::start( $user )
	 * riceve come argomento un oggetto di tipo user che deve
	 * avere un id presente nel database		
	 */
	static function start( $u ) {
		ini_set( 'session.use_cookies', 1 );		// Forza l'utilizzo dei cookies
		ini_set( 'session.use_only_cookies', 1 );	
		/*if( !session_start() )
			return false;	*/		
		
		if ( !isset($_SESSION["iduser"]) ) {
			if(is_a($u, "User"))
				$_SESSION["iduser"] = $u->getID();
			else {
 
				$user = User::loadFromDatabase($u->getID(),false);	// Mi assicura che user sia presente nel database
									   	// prima di avviare una sessione	
				if ( $user != false ) {
					//$_SESSION = array(); //NON necessaria
					$_SESSION["iduser"] = $user->getID();
					return true;
				}
				else
					return false;
			}
			return true;
		}	
	}

	/**
	 * Metodo Session::getUser()
	 * restituisce un oggetto user che ha avviato la sessione		
	 */
	static function getUser($who_asks = "Anonimous") {
		/*if( !session_start() )
			return false;*/
		//require_once 'user/UserManager.php';
		//return UserManager::loadUser(1);
		
		if ( isset($_SESSION["iduser"]) ) {
			$user = User::loadFromDatabase($_SESSION["iduser"], false);
			
			if(!isset($_SESSION["getUser"]))
				$_SESSION["getUser"] = 1;
			else
				$_SESSION["getUser"]++;
				
			if ( $user != false )
				return $user;
			else
				return false;
		}
		else
			return false;
	}

	static function is_set() {
		return isset($_SESSION["iduser"]);
	}
	
	static function initializeQueryCounter() {
		$_SESSION["query"] = array("INSERT" => 0, "SELECT" => 0, "UPDATE" => 0, "DELETE" => 0);
	}
	
	static function incrementQueryCounter($type) {
		$_SESSION["query"][$type]++;
	}
	
	static function getQueryCounter() {
		return $_SESSION["query"];
	}
	
	static function getUserCount() {
		return count($_SESSION["getUser"]);
	}
	
	/**
	 * Metodo Session::destroy()
	 * distrugge la sessione dell'user che ha avviato la sessione		
	 */
	static function destroy() {
		session_unset();
		session_destroy();
		session_write_close();
	}
}
?>
