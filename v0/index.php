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
	require_once("settings.php");
	require_once("strings/" . LANG . "strings.php");
	
	$t = new Test();
	//echo $t->testEditPost();
	//echo $t->testAddPostToCollection();
	//echo $t->testSavePost();
	//echo $t->testSaveComment();
	//echo $t->testDeletePost();
	//echo $t->testDeleteComment();
	//echo $t->testSaveVote();
	//echo $t->testDeleteVote();
	//echo $t->testSaveCollection();
	//echo $t->testSaveVoteOnCollection();
	//echo $t->testSaveContest();
	//echo $t->testSubscribeToContest();
	//echo $t->testUnsubscribeToContest();
	echo $t->testDeleteContest();
	
?>

</body>
</html>
