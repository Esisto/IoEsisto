<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Install IoEsisto</title>
</head>

<body>

<?php
	ini_set("display_errors", "On");
	error_reporting(E_ALL ^ E_NOTICE);
	require_once("settings.php");
	require_once("strings/" . LANG . "strings.php");
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
	
	$s = file_get_contents("ioesistodb2.sql");
	$queries = explode("\n\n", $s);
	
	for($i=0; $i<count($queries); $i++) {
		$rs = $q->execute($queries[$i]);
		if($rs === false) echo "<p>ERROR CREATING TABLES</p>";
		else {
			$ss = explode("`", $queries[$i]);
			echo "<p>TABLE " . $ss[1] . " INSTALLED</p>";
		}
	}
	
	$q->execute("INSERT INTO `Role` VALUES('admin')");
	$ra = mysql_affected_rows();
	$q->execute("INSERT INTO `Role` VALUES('user')");
	$ra+= mysql_affected_rows();
	
	if($ra == 2) echo "<p>INSERTED ROLES</p>";
	
	// DEBUG
	$q->execute("INSERT INTO `User` VALUES(2, 'ioesisto', 'ciccia', 'Io', 'Esisto', NULL, 'no-reply@ioesisto.com', 'm', NULL, 1, 1, NULL, NULL, NULL, 'admin', NULL)");
	if(mysql_affected_rows() == 1) echo "<p>INSERTED FAKE USER</p>";
	// END DEBUG
?>

</body>
</html>
