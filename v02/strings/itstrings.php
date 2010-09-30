<?php
// FILE DI LOCALIZZAZIONE IN ITALIANO

// tutte le stringhe definite
define("DB_NOT_CONNECTED", "Non connesso.");
define("DB_CONNECTED", "Connesso a ");
define("NO_USERNAME", "username non presente");
define("NO_PASSWORD","password non presente");
define("NO_NICKNAME", "nickname non presente");
define("DIFFERENT_PASSWORD","le password non corrispondono");
define("NO_EMAIL","email non presente");
define("INVALID_DATE","inserisci una data completa di giorno, mese e anno");
define("CURRENT_PASSWORD", "password attuale");
define("NEW_PASSWORD","nuova password");
define("CHECK_PASSWORD","verifica password");

// date
function format_datetime($date) {
	$months = array("Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno", "Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre");
	$sh_months = array("Gen", "Feb", "Mar", "Apr", "Mag", "Giu", "Lug", "Ago", "Set", "Ott", "Nov", "Dic");
	$days = array("Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato");
	$sh_days = array("Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab");

	$today = date_timestamp_get(date_create_from_format("Y-m-d G:i", date("Y-m-d", time()) . " 00:00"));
	$tomorrow = date_timestamp_get(date_create_from_format("Y-m-d G:i", date("Y-m-d", time()+24*60*60) . " 00:00"));
	$yesterday = date_timestamp_get(date_create_from_format("Y-m-d G:i", date("Y-m-d", time()-24*60*60) . " 00:00"));
	//echo "now = " . time() . "|today = " . $today . "|yest = " . $yesterday . "|tom = " . $tomorrow; //DEBUG
	
	if($date >= $today && $date < $tomorrow) {
		$d = "Oggi " . date("@ G:i", $date);
	} else if($date < $today && $date >= $yesterday) {
		$d = "Ieri " . date("@ G:i", $date);
	} else {
		$d = $sh_days[intval(date("w", $date))] . date(" j ", $date) . $sh_months[intval(date("n", $date))-1] . date(" Y @ g:i a", $date);
	}
	$d = str_replace("@", "alle", $d);
	return $d;
}
?>