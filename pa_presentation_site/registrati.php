<?php
require_once("classes/users.php");

if(isset($PHPSESSID)) {
	$err = "SESSION";
	$accesso = accesso();
} else {
	$err = "NIENTE";
	$accesso = 0;
}
$path = "http://" . $_SERVER["SERVER_NAME"] . "/ioesisto/login.php";	
if($accesso>0 && session_is_registered("nick")) {
	header("location: " . str_replace("|","&",$path));	
} else if($accesso>0)
	header("location: " . str_replace("|","&",$path));	
else
	$user = "";


	$noncreatomex = "?mex=". addslashes("L'utente non Kegrave; stato creato. Riprova.");
	if(isset($_POST["nick"]))
		$nick = $_POST["nick"];
	else
		header("location: " . str_replace("|","&",$path . $noncreatomex));		
	if(file_exists("../login/" . $nick . ".txt") || file_exists("../admin/login/" . $nick . ".txt")) {
		$path .= "?mex=". addslashes("Il nickname da te scelto non Kegrave; disponibile. Provane un altro.");
		header("location: " . str_replace("|","&",$path));	
	}
	if(isset($_POST["pwd"]))
		$pwd = $_POST["pwd"];
	else
		header("location: " . str_replace("|","&",$path . $noncreatomex));		
	if(isset($_POST["pwd2"]))
		$pwd2 = $_POST["pwd2"];
	else
		header("location: " . str_replace("|","&",$path . $noncreatomex));
	if($pwd != $pwd2) header("location: " . str_replace("|","&",$path . $noncreatomex));
	if(isset($_POST["mail"]))
		$mail = $_POST["mail"];
	else
		header("location: " . str_replace("|","&",$path . $noncreatomex));
	if(isset($_POST["mail2"]))
		$mail2 = $_POST["mail2"];
	else
		header("location: " . str_replace("|","&",$path . $noncreatomex));
	if($mail != $mail2) header("location: " . str_replace("|","&",$path . $noncreatomex));
	
	if(isset($_POST["news"]))
		$news = $_POST["news"];

	if(isset($_FILES['img']['tmp_name'])) {
		if(filesize($_FILES['img']['tmp_name']) > 200000) {
			unset($_FILES['img']['tmp_name']);
			$path .= "?mex=". addslashes("L'immagine Kegrave; troppo grande! Prova un'immagine piKugrave; piccola.");
			header("location: " . str_replace("|","&",$path));
		}
	}
	if($img!=""&&$img!=null) {
		$uploadimg = "../img/login/$nick.gif";
		if(file_exists($uploadimg) || !copy($_FILES['img']['tmp_name'], $uploadimg)) {
			$err = "NON CARICATA";
			//$path .= "?mex=". addslashes("L'immagine non Kegrave; stata caricata per un errore del server.");
			//header("location: " . str_replace("|","&",$path));
		}
	}
	$user = new utente();
	$n = ($news=="on" && isset($news));
	
	$saved = $user->salvaUtente($nick,$pwd,$mail,$n,false,$uploadimg);

	if($saved===$user) {
		echo $err . filesize($_FILES['img']['tmp_name']) . " - ";
		echo filesize($uploadimg) . " - " . serialize(getimagesize($uploadimg)) . "<br />";
		echo serialize($user);
?>
	<p>Registrazione eseguita con successo!</p>
	<p>Esegui l'accesso per continuare.</p>
	
<?php
		$path .= "?mex=". addslashes("Registrazione avvenuta, controlla la mail per confermare la registrazione.") ."|ok=y";
		header("location: " . str_replace("|","&",$path));
	} else {
		echo serialize($user);
		echo "	<p>Non salvato!</p>";
	}
	
?>


