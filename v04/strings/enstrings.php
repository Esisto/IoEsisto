<?php
// ENGLISH LOCALIZATION FILE

// all the defined strings
// DB STRINGS
define("DB_NOT_CONNECTED", "Not connected.");
define("DB_CONNECTED", "Connected to ");
define("NOT_FOUND", "Not found");

// MAIL STRINGS
define("NO_SUBJECT", "No subject");

//USER STRINGS
define("NO_USERNAME", "missing username");
define("NO_PASSWORD","missing password");
define("NO_NICKNAME", "missing nickname");
define("DIFFERENT_PASSWORD","passwords do not match");
define("NO_EMAIL","missing email");
define("INVALID_DATE","insert a date complete of day, month and year");
define("CURRENT_PASSWORD", "current password");
define("NEW_PASSWORD","new password");
define("CHECK_PASSWORD","check password");

//reCHAPTCHA
define("NO_RECAPTCHA","The reCAPTCHA wasn't entered correctly. Try it again");

function format_datetime($date) {
	$today = date_timestamp_get(date_create_from_format("Y-m-d G:i", date("Y-m-d", time()) . " 00:00"));
	$tomorrow = date_timestamp_get(date_create_from_format("Y-m-d G:i", date("Y-m-d", time()+24*60*60) . " 00:00"));
	$yesterday = date_timestamp_get(date_create_from_format("Y-m-d G:i", date("Y-m-d", time()-24*60*60) . " 00:00"));
	//echo "now = " . time() . "|today = " . $today . "|yest = " . $yesterday . "|tom = " . $tomorrow; //DEBUG
	if($date >= $today && $date < $tomorrow) {
		$d = "Today " . date("@ g:i a", $date);
	} else if($date < $today && $date >= $yesterday) {
		$d = "Yesterday " . date("@ g:i a", $date);
	} else {
		$d = date("D jS M Y @ g:i a", $date);
	}
	$d = str_replace("@", "at", $d);
	return $d;
}
?>