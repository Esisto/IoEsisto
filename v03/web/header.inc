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
	<link rel="stylesheet" type="text/css" media="screen,print" href="<?php echo FileManager::appendToRootPath("files/" . $css . ".css") ?>" />
<?php 
}
if(count($stylesheets) == 0) {
?>
	<link rel="stylesheet" type="text/css" media="screen,print" href="<?php echo FileManager::appendToRootPath("files/default/default.css") ?>" />
<?php 
}

if(count($js) <= 1) {
?>
    <script src="http://www.google.com/jsapi?key=ABQIAAAAjg_c7D-9UshGFUSLngI0kRTb-vLQlFZmc2N8bgWI8YDPp5FEVBRY2O1KZH9g1Dq5uxwllWjXB-Lu5Q" type="text/javascript"></script>
    <script src="<?php echo FileManager::appendToRootPath("plugins/ckeditor/ckeditor.js"); ?>" type="text/javascript"></script>
<?php 
}
foreach($js as $src) {
?>
    <script src="<?php 
    if(strpos($src, "http://") === 0)
    	$str = $src;
    else if(strpos($src, "plugins") === 0)
    	$str = FileManager::appendToRootPath($src);
    else
    	$str = FileManager::appendToRootPath("files/" . $src . (strpos($src, ".js") ? "" : ".js"));
    echo $str; ?>" type="text/javascript"></script>
<?php
}
?>
</head>

<body>
<?php
}
?>