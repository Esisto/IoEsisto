<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);
require_once("settings.php");
require_once("strings/" . LANG . "strings.php");
require_once("strings/strings.php");
require_once("query.php");
require_once("session.php");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Install IoEsisto</title>
</head>

<body>

<?php
	Session::initializeQueryCounter();
	$mysqli = @new MySQLi(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, "", DB_PORT);
	if(!$mysqli->select_db(DB_NAME)) {
		$mysqli->query("CREATE DATABASE " . DB_NAME);
		echo "<p>DATABASE CREATED</p>";
	} else
		echo "<p>DATABASE ALREADY EXISTED</p>";
	
	$mysqli->close();
	$db = new DBManager();
	require_once("db.php");
	$queries = explode("\n\n", DB::getCreateQueries());
	
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
	
		$s = "INSERT INTO `" . DB::TABLE_ROLE . "` 
		(`" . DB::ROLE_NAME . "`, `" . DB::READ . "`, 
		`" . DB::CREATE_NEWS . "`, `" . DB::EDIT_NEWS . "`, `" . DB::DELETE_NEWS . "`, 
		`" . DB::CREATE_PHOTOREP . "`, `" . DB::EDIT_PHOTOREP . "`, `" . DB::DELETE_PHOTOREP . "`, 
		`" . DB::CREATE_VIDEOREP . "`, `" . DB::EDIT_VIDEOREP . "`, `" . DB::DELETE_VIDEOREP . "`, 
		`" . DB::CHANGE_VISIBILITY . "`, `" . DB::CREATE_LIST . "`, `" . DB::EDIT_LIST . "`, `" . DB::DELETE_LIST . "`, 
		`" . DB::COMMENT . "`, `" . DB::DELETE_COMMENT . "`, `" . DB::VOTE . "`, `" . DB::FOLLOW . "`, 
		`" . DB::STOP_FOLLOW . "`, `" . DB::CREATE_FEEDBACK . "`, `" . DB::DELETE_FEEDBACK . "`, 
		`" . DB::SEND_MESSAGE . "`, `" . DB::CREATE_DIRECTORY . "`, `" . DB::EDIT_DIRECTORY . "`, `" . DB::DELETE_DIRECTORY . "`, 
		`" . DB::MARK_AS_READ . "`, `" . DB::MOVE_MESSAGE . "`, `" . DB::EMPTY_RECYCLE_BIN . "`, 
		`" . DB::CREATE_RESOURCE . "`, `" . DB::EDIT_RESOURCE . "`, `" . DB::DELETE_RESOURCE . "`, 
		`" . DB::EDIT_PROFILE . "`, `" . DB::CREATE_CONTEST . "`, `" . DB::EDIT_CONTEST . "`, `" . DB::DELETE_CONTEST . "`, 
		`" . DB::SUBSCRIBE . "`, `" . DB::UNSUBSCRIBE . "`, `" . DB::CREATE_USER . "`, `" . DB::DELETE_USER . "`, 
		`" . DB::BLOCK_USER . "`, `" . DB::SUSPEND_USER . "`, `" . DB::SIGNAL . "`, 
		`" . DB::CREATE_CATEGORY . "`, `" . DB::EDIT_CATEGORY . "`, `" . DB::DELETE_CATEGORY . "`, 
		`" . DB::CREATE_TEMPLATE . "`, `" . DB::EDIT_TEMPLATE . "`, `" . DB::DELETE_TEMPLATE . "`, 
		`" . DB::ADVANCED_TPL_MANAGER . "`, `" . DB::EDIT_OTHER_NEWS . "`, `" . DB::EDIT_OTHER_PHOTOREP . "`, 
		`" . DB::EDIT_OTHER_VIDEOREP . "`, `" . DB::EDIT_OTHER_LIST . "`, `" . DB::EDIT_OTHER_PROFILE . "`, 
		`" . DB::EDIT_OTHER_RESOURCE . "`, `" . DB::UNSUBSCRIBE_OTHER . "`, `" . DB::DELETE_OTHER_FEEDBACK . "`, 
		`" . DB::HIDE_OTHER . "`, `" . DB::CREATE_OTHER_TEMPLATE . "`, `" . DB::EDIT_OTHER_TEMPLATE . "`, `" . DB::DELETE_OTHER_TEMPLATE . "`, 
		`" . DB::REQUEST_SUSPEND . "`, `" . DB::REQUEST_BLOCK . "`, `" . DB::VIEW_MOD_DECISION . "`, 
		`" . DB::VIEW_EDIT_DECISION . "`, `" . DB::VIEW_HISTORY . "`, `" . DB::VIEW_BLOCK_REQUEST . "`, `" . DB::VIEW_SUSPEND_REQUEST . "`) VALUES
('admin', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1, 1),
('chief-editor', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 0, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1),
('editor', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 1, 0, 1, 0, 0, 1, 0, 1, 1, 0, 1, 1, 0, 1, 0, 1),
('guest', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('historian', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1),
('level1', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('level2', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('level3', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('level4', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('level5', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('moderator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 0, 0),
('sponsor', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 1, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('suspended', 1, 0, 1, 1, 0, 1, 1, 0, 1, 1, 1, 0, 1, 1, 0, 1, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
('user-manager', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1);";
	$db->execute($s, LOGMANAGER, null);
	$ra = $db->affected_rows();
	echo "<p>INSERTED " . $ra . " ROLES</p>";
	
	// DEBUG
	require_once 'filter.php';
	$db->execute("INSERT INTO `User` VALUES(1, 'ioesisto', 'no-reply@ioesisto.com', '" . Filter::encodePassword("ciccia") . "', 'Io', 'Esisto', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2010-08-27 11:49:28', 1, 1, 0, 0, 0, 0, 0, 0, 0, NULL)", "User", null);
	if($db->affected_rows() == 1) echo "<p>INSERTED FAKE USER</p>";
	
	$db->execute("INSERT INTO `" . DB::TABLE_MAIL_DIRECTORY . "` VALUES(1, '" . TRASH . "', 1)", "MailDirectory", null);
	$ra = $db->affected_rows();
	$db->execute("INSERT INTO `" . DB::TABLE_MAIL_DIRECTORY . "` VALUES(2, '" . MAILBOX . "', 1)", "MailDirectory", null);
	$ra+= $db->affected_rows();
	$db->execute("INSERT INTO `" . DB::TABLE_MAIL_DIRECTORY . "` VALUES(3, '" . SPAM . "', 1)", "MailDirectory", null);
	$ra+= $db->affected_rows();
	if($ra == 3) echo "<p>INSERTED FAKE MAIL DIRECTORIES</p>";
	// END DEBUG
	
	$cat = array( //non metto le regioni e le provincie perché verranno inserite in automatico.
				"Novit&agrave;" => array(),
				"Cronaca" => array(),
				"Politica" => array(),
				"Finanza" => array("Economia", "Borsa e finanza"),
				"Scienza" => array("Tecnologia", "Medicina"),
				"Sport" => array("Calcio" => array("Serie A", "Serie B", "Mercato"), "Basket", "Pallavolo", "Nuoto", "Tennis", "Golf", "Rugby", "Football americano", "Motociclismo", "Automobilismo", "Atletica", "Altri sport"),
				"Spettacoli" => array("Musica", "Cinema", "TV", "Teatro"),
				"Cultura e tendenza" => array("Libri", "Moda", "Arte", "Fotografia", "Religione", "Gossip", "Web"),
				"Motori" => array("Auto", "Moto", "Altro"),
				"Tempo libero" => array("Viaggi", "Cucina", "Casa", "Animali")
	);
	
	require_once 'manager/CategoryManager.php';
	CategoryManager::createCategoriesFromArray($cat, 1);
	echo "<p>INSERTED CATEGORIES</p>";
	
?>

</body>
</html>
