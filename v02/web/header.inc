<?php 

function writeHeader($title, $stylesheets = array(), $js = array()) {
	require_once 'file_manager.php';
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
	<title><?php echo $title; ?></title>
<?php 
foreach($stylesheets as $css) {
?>
	<link rel="stylesheet" type="text/css" media="screen,print" href="<?php echo FileManager::appendToRootPath("styles/" . $css . ".css") ?>" />
<?php 
}
if(count($stylesheets) > 0) {
?>
	<link rel="stylesheet" type="text/css" media="screen,print" href="<?php echo FileManager::appendToRootPath("default.css") ?>" />
<?php 
}
?>
</head>

<body>
<?php
}
?>