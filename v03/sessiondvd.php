<?php
	require_once(USER_DIR . "/User.php");
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

			if ( !isset($_SESSION['iduser']) ) {
				
				if ( isset($u->getID()) ) {
 	
					$user = User::loadFromDatabase( $u->getID() );	// Mi assicura che user sia presente nel database
										   	// prima di avviare una sessione	
					if ( $user != false ) {
						session_start();
						// $_SESSION = array(); NON necessaria
						$_SESSION['iduser'] = $user->getID();
					}
					else
						//TODO header("location: http://ioesisto/?err=errore1…");
						echo "<p>Session Error: utente non presente nel database</p>";
				}
				else
					//TODO header("location: http://ioesisto/?err=errore2…");
					echo "<p>Session Error: id non presente</p>";
			}	
		} 
		
		/**
		 * Metodo Session::getUser()
		 * restituisce un oggetto user che ha avviato la sessione		
		 */
		static function getUser() {
			
			if ( isset($_SESSION['iduser']) ) {

				$user = User::loadFromDatabase($_SESSION['iduser']);
			
				if ( $user != false )
					return $user;
				else
					//TODO header("location: http://ioesisto/?err=errore3…");
					echo "<p>Session Error: utente non presente nel database</p>";
			} 	
		}

		/**
		 * Metodo Session::destroy()
		 * distrugge la sessione dell'user che ha avviato la sessione		
		 */
		static function destroy() {
			if ( isset($_SESSION['iduser']) ) {
				// unset($_SESSION['iduser']); NON necessaria			
				session_destroy();
			}
		}
	}
?>
