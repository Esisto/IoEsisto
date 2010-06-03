<?php
class utente {
	var $nick;
	var $password;
	var $email;
	var $newsletter = true;
	var $notifica_risposta = true;
	var $immagine;
	
	function setPassword($pwd) {
		$this->password = $pwd;
	}

	function setEMail($mail) {
		$this->email = $mail;
	}
	
	function fillUser($nick,$pwd,$mail,$news,$risposta,$img) {
		$this->nick = $nick;
		$this->setPassword($pwd);
		$this->setEMail($mail);
		if((isset($news))&&($news!==null))
			$this->newsletter = $news;
		if((isset($risposta))&&($risposta!==null))
			$this->notifica_risposta = $risposta;
		if((isset($img))&&($img!==null))
			$this->immagine = $img;
	}
	
	function salvaUtente($nick,$pwd,$mail,$news,$risposta,$img) {
		$this->fillUser($nick,$pwd,$mail,$news,$risposta,$img);
		if($nick!=="") {
			if((file_exists("login/$nick.txt"))||(file_exists("admin/login/$nick.txt"))) {
				return false;
			}
			$file = fopen("login/$nick.txt", "w+");
			fwrite($file,serialize($this));
			
			$mail_to = $mail;
			$mail_from = "do_not_reply@ioesisto.com";
			$mail_subject = "Registrazione";
			$mail_body = "<p><font color=#000000>Registrazione effettuata con successo.<br />
Per attivare l'utente seguire il link qui sotto:<br />
<a href='http://" . $_SERVER["SERVER_NAME"] . "/ioesisto/abilitautente.php?user=" . $nick . "&code=" . sha1($nick . sha1($pwd)) . "'>http://" . $_SERVER["SERVER_NAME"] . "/ioesisto/abilitautente.php?user=" . $nick . "&code=" . md5($nick . md5($pwd)) . "</a></font></p>";
			// Intestazioni HTML
			$mail_in_html = "MIME-Version: 1.0\r\n";
			$mail_in_html .= "Content-type: text/html; charset=UTF-8";
			$mail_in_html .= "From: <$mail_from>";
			// Processo di invio
			mail($mail_to, $mail_subject, $mail_body, $mail_in_html);

			return $this;
		}
		return false;
	}
		
	function login($nick,$pwd) {
		$accesso = 0;
		if(file_exists("login/$nick.txt") && file_exists("login/abilitati/$nick.txt")) {
			//echo "USER";
			$u = unserialize(file_get_contents("login/$nick.txt"));
			//echo " - " . $u->nick . " - " . $u->password . " - " . $pwd;
			if($pwd==$u->password) {
				session_start();
				session_register("nick");
				session_register("pwd");
				session_write_close();
				$accesso = 1;
			}
		}
		if(file_exists("admin/login/$nick.txt")) {
			//echo "ADMIN";
			$u = unserialize(file_get_contents("admin/login/$nick.txt"));
			//echo " - " . $u->nick . " - " . $u->password . " - " . $pwd;
			if($pwd==$u->password) {
				session_start();
				session_register("nick");
				session_register("pwd");
				session_write_close();
				//echo " - OK";
				$accesso = 2;
			}
		}
		return $accesso;
	}
	
	function abilita($code,&$err) {
		$err = sha1($this->nick . sha1($this->password));
		if($code == sha1($this->nick . sha1($this->password))) {
			if(!file_exists("login/abilitati/$this->nick.txt")) {
				$fp = fopen("login/abilitati/$this->nick.txt","w+");
				fwrite($fp,"ok");
			}

			return true;
		} else
			return false;
	}
}

/************ ACCESSO *****************/
global $nick;
global $pwd;

function login($nick,$pwd) {
		$GLOBALS['nick'] = $nick;
		$GLOBALS['pwd'] = $pwd;
	$user = new utente();
	return $user->login($nick,$pwd);
}

function accesso(/*&$err*/) {
    session_start();
	if(!session_is_registered("nick")) {
		//$err .= " sessione non registrata";
		return 0;
	}
	
	$user = new utente();
	$accesso = 0;
	//$err .= " provo utenti";
	if(file_exists("login/" . $_SESSION["nick"] . ".txt") && file_exists("login/abilitati/" . $_SESSION["nick"] . ".txt")) {
		//$err .= " trovato utente";
		$user = unserialize(file_get_contents("login/" . $_SESSION["nick"] . ".txt"));
		if($_SESSION["pwd"]==$user->password)
			$accesso = 1;
	}
	//$err .= " provo admin";
	if(file_exists("admin/login/" . $_SESSION["nick"] . ".txt")) {
		//$err .= " trovato admin";
		$user = unserialize(file_get_contents("admin/login/" . $_SESSION["nick"] . ".txt"));
		//$err .= "\npassword sessione = " . $_SESSION["pwd"];
		//$err .= "\npassword admin = " . $user->password;		
		if($_SESSION["pwd"]==$user->password)
			$accesso = 2;
	}
	return $accesso;
}
/**************** FINE ACCESSO *****************/

?>