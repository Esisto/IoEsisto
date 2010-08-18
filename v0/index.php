<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>IOESISTO prova</title>
</head>

<body>

<?php
	ini_set("display_errors", "On");
	error_reporting(E_ALL ^ E_NOTICE);
	require_once("post/PostTest.php");
	require_once("post/collection/CollectionTest.php");
	require_once("settings.php");
	require_once("strings/" . LANG . "strings.php");
	
	/*echo testPost();
	echo "<br />";
	echo testComment();
	echo "<br />";
	echo testVote();
	echo "<br />";
	echo testEditPost();
	echo "<br />";
	echo testCollection();
	echo "<br />";
	echo testAddPostToCollection();*/
	echo "<br />";
	echo testSavePost();
	
	//require_once("db_schema.php");
	//$dbs = new DBSchema(null);
	////$db = connect();
	//if($GLOBALS["db_state"] != DB_NOT_CONNECTED) {
	//	echo "<br /><br />";
	//	echo $GLOBALS["db_status"];
	//}
	//echo "<br />";
	//echo $GLOBALS["db_schema_status"];
?>

</body>
</html>
