<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>IOESISTO prova</title>
</head>

<body>

<?php
	session_start();
	session_register("q");
	ini_set("display_errors", "On");
	error_reporting(E_ALL ^ E_WARNING);
	require_once("post/PostTest.php");
	require_once("mail/MailTest.php");
	require_once("settings.php");
	require_once("strings/" . LANG . "strings.php");
	require_once("strings/strings.php");
	require_once(USER_DIR . "/UserTest.php");
	
	$t = new Test();
	echo $t->testEditPost();
	echo $t->testAddPostToCollection();
	echo $t->testSavePost();
	echo $t->testSaveComment();
	//echo $t->testDeletePost();
	//echo $t->testDeleteComment();
	echo $t->testSaveVote();
	//echo $t->testDeleteVote();
	echo $t->testSaveCollection();
	echo $t->testSaveVoteOnCollection();
	echo $t->testSaveContest();
	echo $t->testSubscribeToContest();
	echo $t->testUnsubscribeToContest();
	//echo $t->testDeleteContest();
	echo $t->testPermalink();
	
	//$t = new MailTest();
	//echo $t->testMail();
	//echo $t->testDirectory();
	//echo $t->testDeleteMailFromDirectory();
	//echo $t->testDeleteDirectory();
	//echo $t->testSetReadStatus();
	//echo $t->testAnswerMail();
	//echo $t->testSendMail();
	//
	//$t = new UserTest();
	//echo $t->testUser();
	//echo $t->testContacts();
	//echo $t->testDeleteContact();
	//echo $t->testFollow();
	//echo $t->testDeleteFollow();
	//echo $t->testAddFeedback();
?>

</body>
</html>
