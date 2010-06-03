<?
setcookie("PHPSESSID","",time()-5000);
session_start();
session_unset();
session_destroy();
unset($PHPSESSID);

$path = "http://" . $_SERVER["SERVER_NAME"] . "/ioesisto/" .$_GET["red"];	
header("location: " . str_replace("|","&",$path));	
?>