<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<meta content="text/html; charset=UTF-8" http-equiv="Content-Type" />
	<title>IOESISTO prova</title>
	<link rel="stylesheet" type="text/css" media="screen,print" href="<?php echo "http://localhost:8888/ioesisto/v02/"; //TODO ?>example.css" />
</head>

<body>
<?php

require_once("post/PostManager.php");
require_once("user/UserManager.php");
require_once("search/SearchManager.php");

require_once("page.php");
$request = Page::make($_SERVER["REQUEST_URI"]);

if($request["script"] == "Post") {
	//DEBUG
	if(isset($request["authornickname"])) $author = UserManager::loadUserByNickname($request["authornickname"]);
	else $author = UserManager::loadUser($request["authorid"]);
	
	//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>";
	//END DEBUG
	$posts = SearchManager::searchBy(array("Post"), array("permalink" => $request["permalink"]), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
	
	foreach($posts as $p) {
		require_once("post/PostPage.php");
		PostPage::showPost($p);
	}
} else if($request["script"] == "Tag") {
	//echo "<p><font color='green'>REQUEST TO LOAD post which tag is " . $request["tagname"] . ".</font></p>"; //DEBUG
	$posts = SearchManager::searchBy(array("Post"), array("tag" => $request["tagname"]), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
	foreach($posts as $p) {
		require_once("post/PostPage.php");
		PostPage::showShortPost($p);
	}
} else if($request["script"] == "Category") {
	//echo "<p><font color='green'>REQUEST TO LOAD post which category is " . $request["categoryname"] . ".</font></p>"; //DEBUG
	$posts = SearchManager::searchBy(array("Post"), array("category" => $request["categoryname"]), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
	foreach($posts as $p) {
		require_once("post/PostPage.php");
		PostPage::showShortPost($p);
	}
}

?>
</body>
</html>