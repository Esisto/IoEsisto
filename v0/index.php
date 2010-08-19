<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>IOESISTO prova</title>
</head>

<body>

<?php
	global $q;
	ini_set("display_errors", "On");
	error_reporting(E_ALL ^ E_NOTICE);
	require_once("post/PostTest.php");
	require_once("post/collection/CollectionTest.php");
	require_once("settings.php");
	require_once("strings/" . LANG . "strings.php");
	
	echo testEditPost();
	//echo testAddPostToCollection();
	//echo testSavePost();
	//echo testSaveComment();
	//echo testDeletePost();
	//echo testDeleteComment();
	//echo testSaveVote();
	//echo testDeleteVote();
	//echo testSaveCollection();
	//echo testSaveVoteOnCollection();
?>

</body>
</html>
