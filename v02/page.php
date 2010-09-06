<?php

function elaborateRequest($request) {
	require_once("file_manager.php");
	echo "<br />" . $request;
	$s = substr($request, strlen(dirname($_SERVER["PHP_SELF"])) + 1);
	echo "<br />" . $s;
	$parts = explode("/", $s);
	echo "<br />" . serialize($parts);
	$return = array();
	if(is_numeric($parts[0]))
		$return["authorid"] = $parts[0];
	else
		$return["authornickname"] = $parts[0];
	$script = $parts[1];
	if($script == "Post") {
		//recupera il titolo (escaped) dalla richiesta fatta al server
		$title = explode("(", $parts[3]);
		$rand = substr($title[count($title)-1], 0, -1);
		if(is_numeric($rand)) $return["posttitle"] = substr($parts[3],0,-(2 + strlen($rand)));
		else $return["posttitle"] = $parts[3];
		
		$return["postday"] = date_timestamp_get(date_create_from_format("Y-m-d", $parts[2]));
	}
	
	$return["permalink"] = $s;
	$return["script"] = $script;
	echo "<br />" . serialize($return);
	return $return;
}

?>