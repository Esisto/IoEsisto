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

	$mysqli = @new MySQLi(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, "", DB_PORT);
	if(!$mysqli->select_db(DB_NAME)) {
		$mysqli->query("CREATE DATABASE " . DB_NAME);
		echo "<p>DATABASE CREATED</p>";
	} else
		echo "<p>DATABASE ALREADY EXISTED</p>";
	
	$mysqli->close();
	$db = new DBManager();
	require_once("db.php");
	$queries = explode("\n\n", $s);
	
	for($i=0; $i<count($queries); $i++) {
		$db->execute($queries[$i], null, LOGMANAGER);
		if($db->result) {
			$ss = explode("`", $queries[$i]);
			echo "<p>TABLE " . $ss[1] . " INSTALLED</p>";
		} else {
			//DEBUG
			$s = str_replace(",", ",<br />", $queries[$i]);
			$s = str_replace(") ENGINE", ")<br />ENGINE", $s);
			echo $s; //DEBUG
			echo $db->display_error("Install.php");
		}
	}
	
	$db->execute("INSERT INTO `Role` VALUES('admin')", "Role", null);
	$ra = $db->affected_rows();
	$db->execute("INSERT INTO `Role` VALUES('user')", "Role", null);
	$ra+= $db->affected_rows();
	
	if($ra == 2) echo "<p>INSERTED ROLES</p>";
	else {
		$db->display_error("Install.php");
	}
	
	// DEBUG
	$db->execute("INSERT INTO `User` VALUES(1, 'ioesisto', 'no-reply@ioesisto.com', 'ciccia', 'Io', 'Esisto', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2010-08-27 11:49:28', 1, 1)", "User", null);
	if($db->affected_rows() == 1) echo "<p>INSERTED FAKE USER</p>";
	
	$db->execute("INSERT INTO `MailDirectory` VALUES(1, '" . TRASH . "', 1)", "MailDirectory", null);
	$ra = $db->affected_rows();
	$db->execute("INSERT INTO `MailDirectory` VALUES(2, '" . MAILBOX . "', 1)", "MailDirectory", null);
	$ra+= $db->affected_rows();
	$db->execute("INSERT INTO `MailDirectory` VALUES(3, '" . SPAM . "', 1)", "MailDirectory", null);
	$ra+= $db->affected_rows();
	if($ra == 3) echo "<p>INSERTED FAKE MAIL DIRECTORIES</p>";
	// END DEBUG
?>

</body>
</html>
