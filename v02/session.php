<?php
	require_once(USER_DIR ."/User.php");
	/**
	 * Classe Session 
	 * è una classe che contiene solo metodi statici e
	 * gestisce le sessioni php	
	 */

	session_start();

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
 	
					$user = User::loadFromDatabase( $u->getID() );	// Mi assicura che user sia presente nel database
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
		static function getUser() {
			/*if( !session_start() )
				return false;*/
			//require_once 'user/UserManager.php';
			//return UserManager::loadUser(1);
			
			if ( isset($_SESSION["iduser"]) ) {
				$user = User::loadFromDatabase($_SESSION["iduser"]);
				
				if ( $user != false )
					return $user;
				else
					return false;
			}
			else
				return false;
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
