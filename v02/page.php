<?php

/**
 * Elabora le richieste fatte al server dall'utente per scegliere lo script da eseguire e su quali dati.
 * Non esegue lo script ma fornisce i dati per sceglierlo.
 * 
 * @param $reqest: un URL relativo. Deve essere in una di queste forme:
 * /%nome utente&/Posts					carica tutti i post di un utente.
 * /%nome utente/Post/%data%/%titolo	carica il post il cui permalink  %nome utente/Post/%data%/%titolo
 * /%nome utente/Resource/%id risorsa%	carica la risorsa con id %id risorsa%
 * /Contests							carica tutti i contest
 * /Contest/%id contest%				carica il contest con id %id contest%
 * /Contest/%id contest%/Posts			carica i post del contest
 *
 * eccÉ TODO: decidere tutti i comandiÉ
 */
function elaborateRequest($request) {
	require_once("file_manager.php");
	//echo "<br />" . $request; //DEBUG
	$s = substr($request, strlen(dirname($_SERVER["PHP_SELF"])) + 1);
	//echo "<br />" . $s; //DEBUG
	$parts = explode("/", $s);
	//echo "<br />" . serialize($parts); //DEBUG
	$return = array();
	if(isset($parts[1])) {
		if($parts[0] == "Contest") {
			$script = "Contest";
			$return["idcontest"] = $parts[1];
		} else if($parts[0] == "Category") {
			$script = "Category";
			$return["categoryname"] = $parts[1];
		} else if($parts[0] == "Tag") {
			$script = "Tag";
			$return["tagname"] = $parts[1];
		} else if($parts[1] == "Post") {
			$script = "Post";
			if(is_numeric($parts[0]))
				$return["authorid"] = $parts[0];
			else
				$return["authornickname"] = $parts[0];
		}
	} else if($parts[0] == "Contests") {
		$script = "Contests";
	} else {
		$script = "User";
	}
	
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
	//echo "<br />" . serialize($return); //DEBUG
	return $return;
}

?>