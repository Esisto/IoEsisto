<?php
ini_set("display_errors", "On");
error_reporting(E_ALL);

require_once("post/PostManager.php");
require_once("user/UserManager.php");
require_once("search/SearchManager.php");

require_once("page.php");
$request = elaborateRequest($_SERVER["REQUEST_URI"]);

if($request["script"] == "Post") {
	//DEBUG
	if(isset($request["authornickname"])) $author = UserManager::loadUserByNickname($request["authornickname"]);
	else $author = UserManager::loadUser($request["authorid"]);
	
	echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>";
	//END DEBUG
	$posts = SearchManager::searchBy(array("Post"), array("permalink" => $request["permalink"]), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
	
	foreach($posts as $p) {
		echo "<p><font color='red'>" . $p . "</font></p>"; //DEBUG
	}
}

?>