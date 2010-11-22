<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);


require_once("page.php");
$request = Page::getResponse($_SERVER["REQUEST_URI"]);


?>