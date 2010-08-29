<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Install IoEsisto</title>
</head>

<body>

<?php
	ini_set("display_errors", "On");
	error_reporting(E_ALL);
	require_once("settings.php");
	require_once("strings/" . LANG . "strings.php");
	require_once("strings/strings.php");
	require_once("query.php");

	$db = connect();
	if(false !== $db) { // CREA DATABASE SE NON ESISTE
		if($GLOBALS["db_status"] == DB_NOT_CONNECTED) {
			mysql_query("CREATE DATABASE ioesisto", $db);
			echo "<p>DATABASE CREATED</p>";
			if(mysql_select_db("ioesisto", $db))
				$GLOBALS["db_status"] == DB_CONNECTED . DB_HOSTNAME . "/" . DB_NAME;
		} else
			echo "<p>DATABASE ALREADY CREATED</p>";
	}
	
	mysql_close($db);
	$q = new Query();
	
	require_once("db.php");
	$queries = explode("\n\n", $s);
	
	for($i=0; $i<count($queries); $i++) {
		$q->execute($queries[$i], null, LOGMANAGER);
		//DEBUG
		$s = str_replace(",", ",<br />", $queries[$i]);
		$s = str_replace(") ENGINE", ")<br />ENGINE", $s);
		echo $s; //DEBUG
			$ss = explode("`", $queries[$i]);
			echo "<p>TABLE " . $ss[1] . " INSTALLED</p>";
	}
	
	$q->execute("INSERT INTO `Role` VALUES('admin')", "Role", null);
	$ra = $q->affected_rows();
	$q->execute("INSERT INTO `Role` VALUES('user')", "Role", null);
	$ra+= $q->affected_rows();
	
	if($ra == 2) echo "<p>INSERTED ROLES</p>";
	
	// DEBUG
	$q->execute("INSERT INTO `User` VALUES(1, 'ioesisto', 'no-reply@ioesisto.com', 'ciccia', 'Io', 'Esisto', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2010-08-27 11:49:28', 1, 1)", "User", null);
	if($q->affected_rows() == 1) echo "<p>INSERTED FAKE USER</p>";
	$q->execute("INSERT INTO `MailDirectory` VALUES(1, '" . TRASH . "', 1)", "MailDirectory", null);
	$ra = $q->affected_rows();
	$q->execute("INSERT INTO `MailDirectory` VALUES(2, '" . MAILBOX . "', 1)", "MailDirectory", null);
	$ra+= $q->affected_rows();
	$q->execute("INSERT INTO `MailDirectory` VALUES(3, '" . SPAM . "', 1)", "MailDirectory", null);
	$ra+= $q->affected_rows();
	if($ra == 3) echo "<p>INSERTED FAKE MAIL DIRECTORIES</p>";
	// END DEBUG
?>

</body>
</html>
