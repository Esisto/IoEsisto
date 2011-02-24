<?php
require_once("dataobject/User.php");
require_once 'manager/UserManager.php';
require_once 'dao/UserDao.php';

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
 				try {
					$userdao = new UserDao();
					$user = $userdao->quickLoad($u->getID());	// Mi assicura che user sia presente nel database
									   	// prima di avviare una sessione	
					$_SESSION["iduser"] = $user->getID();
					return true;
 				} catch (Exception $e) {
					return false;
 				}
			}
			return true;
		}	
	}

	/**
	 * Metodo Session::getUser()
	 * restituisce un oggetto user che ha avviato la sessione		
	 */
	static function getUser($who_asks = "Anonimous") {
		if ( isset($_SESSION["iduser"]) ) {
			$userdao = new UserDao();
			$user = $userdao->quickLoad($_SESSION["iduser"]);
			
			if(!isset($_SESSION["getUser"]))
				$_SESSION["getUser"] = 1;
			else
				$_SESSION["getUser"]++;
				
			if ( $user != false )
				return $user;
			else
				return false;
		} else {
			return false;
		}
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
	 * Salva un VideoReportage e una Risorsa (per la fase 2 del salvataggio di un video).
	 * ATTENZIONE!!! Salva oggetti solo di tipo VideoReportage e Resource!!!
	 * @param VideoReportage $post il video reportage da salvare temporaneamente
	 * @param Resource $video la risorsa da salvare temporaneamente (opzionale)
	 * @return true se è stato salvato, false altrimenti
	 */
	static function setTempVideoRep($post, $video = null) {
		if(is_a($post, "VideoReportage") && ($video == null || is_a($video, "Resource"))) {
			$_SESSION["temp_post"] = $post;
			$_SESSION["temp_resource"] = $video;
			return true;
		}
		return false;
	}
	
	/**
	 * Restituisce il VideoReportage e il Video salvati in sessione (per il ritorno da youtube)
	 * ATTENZIONE!!! Distrugge le cose dalla sessione!!!
	 * @return un array associativo con i parametri "post" e "video". O un array vuoto se non è stato salvato nulla.
	 */
	static function getTempVideoRep() {
		$res = array();
		if(isset($_SESSION["temp_resource"])) {
			$res["video"] = $_SESSION["temp_resource"];
			unset($_SESSION["temp_resource"]);
			$res["post"] = $_SESSION["temp_post"];
			unset($_SESSION["temp_post"]);
		}
		return $res;
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
