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

//	$mysqli = @new MySQLi(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, "", DB_PORT);
//	if(!$mysqli->select_db(DB_NAME)) {
//		$mysqli->query("CREATE DATABASE " . DB_NAME);
//		echo "<p>DATABASE CREATED</p>";
//	} else
//		echo "<p>DATABASE ALREADY EXISTED</p>";
//	
//	$mysqli->close();
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
	$db->execute("INSERT INTO `User` VALUES(1, 'ioesisto', 'no-reply@ioesisto.com', sha1('ciccia'), 'Io', 'Esisto', 'm', NULL, NULL, NULL, NULL, NULL, NULL, 'admin', '2010-08-27 11:49:28', 1, 1)", "User", null);
	if($db->affected_rows() == 1) echo "<p>INSERTED FAKE USER</p>";
	
	$db->execute("INSERT INTO `MailDirectory` VALUES(1, '" . TRASH . "', 1)", "MailDirectory", null);
	$ra = $db->affected_rows();
	$db->execute("INSERT INTO `MailDirectory` VALUES(2, '" . MAILBOX . "', 1)", "MailDirectory", null);
	$ra+= $db->affected_rows();
	$db->execute("INSERT INTO `MailDirectory` VALUES(3, '" . SPAM . "', 1)", "MailDirectory", null);
	$ra+= $db->affected_rows();
	if($ra == 3) echo "<p>INSERTED FAKE MAIL DIRECTORIES</p>";
	// END DEBUG
	
	$cat = array( //non metto le regioni e le provincie perché verranno inserite in automatico.
				"Novità" => array(),
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
	
	require_once 'post/PostCommon.php';
	$count = CategoryManager::createCategoriesFromArray($cat);
	echo "<p>INSERTED CATEGORIES</p>";
	
	
	s = "INSERT INTO `role` (`rl_name`, `read`, `create_news`, `edit_news`, `delete_news`, `create_photorep`, `edit_photorep`, `delete_photorep`, `create_videorep`, `edit_videorep`, `delete_videorep`, `change_visibility`, `create_list`, `edit_list`, `delete_list`, `comment`, `delete_comment`, `vote`, `follow`, `stop_follow`, `create_feedback`, `delete_feedback`, `send_message`, `create_directory`, `edit_directory`, `delete_directory`, `mark_as_read`, `move_message`, `empty_recycle_bin`, `create_resource`, `edit_resource`, `delete_resource`, `edit_profile`, `create_contest`, `edit_contest`, `delete_contest`, `subscribe`, `unsubscribe`, `create_user`, `delete_user`, `block_user`, `suspend_user`, `signal`, `create_category`, `edit_category`, `delete_category`, `create_template`, `edit_template`, `delete_template`, `adv_template_manager`, `edit_other_news`, `edit_other_photorep`, `edit_other_videorep`, `edit_other_list`, `edit_other_profile`, `edit_other_resource`, `unsubscribe_other`, `delete_other_feedback`, `hide_other`, `create_other_template`, `edit_other_template`, `delete_other_template`, `request_suspend`, `request_block`, `view_mod_decision`, `view_edit_decision`, `view_history`, `view_block_request`, `view_suspend_request`) VALUES
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
	
?>

</body>
</html>
