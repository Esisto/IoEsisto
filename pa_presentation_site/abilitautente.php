<?php
error_reporting(E_ALL);

$path = "http://" . $_SERVER["SERVER_NAME"] . "/ioesisto/login.php";	
$nonabilitato = addslashes("Utente non abilitato. Ricontrolla il codice, altrimenti esegui di nuovo la registrazione.");

if(isset($_GET['user']))
	$user = $_GET['user'];
else
	header ("location: " . $path . "?mes=" . $nonabilitato);
if(isset($_GET['code']))
	$code = $_GET['code'];
else
	header ("location: " . $path . "?mes=" . $nonabilitato);

if(file_exists("login/" . $user . ".txt")) {
	require_once("classes/users.php");
	
	$err = "";
	$u = unserialize(file_get_contents("login/" . $user . ".txt"));

	if($u->abilita($code,$err))
		header ("location: " . $path . "?mes=" . addslashes("L'utente Kegrave stato abilitato. Effettua il login.") . "&ok=y");
	else
		header ("location: " . $path . "?mes=" . $nonabilitato);	
} else {
	header ("location: " . $path . "?mes=" . $nonabilitato);
}	
	


?>