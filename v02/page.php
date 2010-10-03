<?php

class Page {
	/**
	 * Elabora le richieste fatte al server dall'utente per scegliere lo script da eseguire e su quali dati.
	 * Non esegue lo script ma fornisce i dati per sceglierlo.
	 * 
	 * @param $reqest: un URL relativo. Deve essere in una di queste forme:
	 * /%nome utente&/Posts					carica tutti i post di un utente.
	 * /%nome utente/Post/%data%/%titolo	carica il post il cui permalink � %nome utente/Post/%data%/%titolo
	 * /%nome utente/Resource/%id risorsa%	carica la risorsa con id %id risorsa%
	 * /Contests							carica tutti i contest
	 * /Contest/%id contest%				carica il contest con id %id contest%
	 * /Contest/%id contest%/Posts			carica i post del contest
	 *
	 * ecc... TODO: decidere tutti i comandi�
	 */
	private static function elaborateRequest($request) {
		require_once("file_manager.php");
		//echo "<br />" . $request; //DEBUG
		$s = substr($request, strlen(dirname($_SERVER["PHP_SELF"])) + 1);
		//echo "<br />" . $s; //DEBUG
		$parts = explode("/", $s);
		$count = count($parts);
		//se parts è vuoto eseguo l'index
		if($count == 0) return array("object" => "index");
		
		$object = $parts[0];
		$action = $parts[$count-1];
		$return = array();
		
		//selezione dell'oggetto su cui lavorare
		switch ($object) {
			case "Contest":
				//modifica o leggi tutti i post di un contest //EDIT E DELETE SOLO ADMIN!!!
				if($action == "Edit" || $action == "Posts" || $action == "Delete") {	//esempio: /Contest/%contest_id%/Edit
					if($count != 3) $action = "";
					else $return["contestid"] = $parts[1];
				}
				//crea nuovo contest //SOLO ADMIN!!!
				if($action == "New") {	//esempio: /Contest/New
					if($count != 2)
						$action = "";
				}
				//leggi la scheda del contest
				if($count == 2) {	//esempio: /Contest/%contest_id%
					$action = "Read";
					$return["contestid"] = $parts[1];
				}
				//pagina di ricerca dei contest
				if($count == 1) {
					$action == "Search";
				}
				break;
			case "Category":
				//modifica o leggi tutti i post di una categoria //EDIT E DELETE SOLO ADMIN!!!
				if($action == "Edit" || $action == "Posts" || $action == "Delete") {	//esempio: /Category/%category_name%/Posts
					if($count != 3) $action = "";
					else $return["categoryname"] = $parts[1];
				}
				//crea nuova categoria //SOLO ADMIN!!!
				if($action == "New") {	//esempio: /Category/New
					if($count != 2)
						$action = "";
				}
				//leggi tutti i post di una categoria //E' UNA COPIA DI QUELLA SOPRA...
				if($count == 2) {	//esempio: /Category/%category_name%
					$action = "Posts";
					$return["categoryname"] = $parts[1];
				}
				//pagina di ricerca delle categorie
				if($count == 1) {
					$action == "Search";
				}
				break;
			case "Tag":
				//leggi tutti i post di un tag
				if($action == "Posts") {	//esempio: /Tag/%tag_name%/Posts
					if($count != 3) $action = "";
					else $return["tagname"] = $parts[1];
				}
				//leggi tutti i post di un tag //E' UNA COPIA DI QUELLA SOPRA...
				if($count == 2) {	//esempio: /Tag/%tag_name%
					$action = "Posts";
					$return["tagname"] = $parts[1];
				}
				//pagina di ricerca dei tag
				if($count == 1) {
					$action == "Search";
				}
				break;
			case "Post":
				//modifica, vota, commenta, elimina, subscribe il post
				if($action == "Edit" || $action == "Vote" ||
				   $action == "Comment" || $action == "Delete" ||
				   $action == "Subscribe" || $action == "AddToCollection") {	//esempio: /Post/%author%/%post_date%/%post_title%/Edit
					if($count != 5) $action = "";
				}
				//leggi il post
				if($count == 4) {	//esempio: /Post/%author%/%post_date%/%post_title%/
					$action = "Read";
				}
				//crea nuovo post
				if($action == "New") {	//esempio: /Post/New
					if($count != 2)
						$action = "";
				}
				//pagina di ricerca dei post
				if($count == 1) {
					$action == "Search";
				} else if($action != "") { //recupera altre informazioni sul post
					if(is_numeric($parts[1]))
						$return["authorid"] = $parts[1];
					else
						$return["authornickname"] = $parts[1];
					//recupera il titolo (escaped) dalla richiesta fatta al server
					$title = explode("(", $parts[3]);
					$rand = substr($title[count($title)-1], 0, -1);
					if(is_numeric($rand)) $return["posttitle"] = substr($parts[3],0,-(2 + strlen($rand)));
					else $return["posttitle"] = $parts[3];
					
					$return["postday"] = date_timestamp_get(date_create_from_format("Y-m-d", $parts[2]));
				}
				break;
			case "Comment":
				//rimuovi un commento
				if($action == "Delete") {	//esempio: /Comment/%comment_id%/Remove
					if($count != 3) $action = "";
					else $return["commentid"] = $parts[1];
				}
				//leggi un commento e relativo post
				if($count == 2) {	//esempio: /Comment/%comment_id%
					$action = "Read";
					$return["commentid"] = $parts[1];
				} else if($count != 3) $action = "";
				break;
			case "Vote":
				//rimuovi, modifica un voto
				if($action == "Delete" || $action == "Edit") {	//esempio: /Vote/%post_id%/Remove
					if($count != 3) $action = "";
					else $return["postid"] = $parts[1];
				}
				break;
			case "User":
				//modifica, segui, non seguire, commenta, elimina, verifica, leggi tutti i post di un utente
				if($action == "Edit" || $action == "Follow" ||
				   $action == "Feedback" || $action == "Delete" ||
				   $action == "StopFollow" || $action == "Verify" ||
				   $action == "Posts") {	//esempio: /User/%user_nickname%/Verify
					if($count != 3) $action = "";
				}
				//registra nuovo utente
				if($action == "New") {	//esempio: /User/New
					if($count != 2)
						$action = "";
				}
				//leggi il profilo
				if($count == 2) {	//esempio: /User/%user_nickname%
					$action = "Read";
					if(is_numeric($parts[1]))
						$return["userid"] = $parts[1];
					else
						$return["usernickname"] = $parts[1];
				}
				//pagina di ricerca degli utenti
				if($count == 1) {
					$action == "Search";
				}
				break;
			case "Feedback":
				//rimuovi, modifica un voto
				if($action == "Delete") {	//esempio: /Feedback/%subject_id%/Remove
					if($count != 3) $action = "";
					else $return["subjectid"] = $parts[1];
				}
				break;
			case "Contact":
				//modifica o elimina un contatto
				if($action == "Edit" || $action == "Delete") {	//esempio: /Contact/%contact_id%/Edit
					if($count != 3) $action = "";
					else $return["contactid"] = $parts[1];
				}
				//crea nuovo contatto
				if($action == "New") {	//esempio: /Contact/New
					if($count != 2)
						$action = "";
				}
				//pagina di ricerca dei contatti
				if($count == 1) {
					$action == "Search";
				}
				break;
			case "Mail":
				//modifica, rispondi, sposta nel cestino o in un'altra cartella o segnala come spam una mail
				if($action == "Edit" || $action == "Delete" ||
				   $action == "Move" || $action == "Spam" ||
				   $action == "Answer") {	//esempio: /Mail/%mail_id%/Edit
					if($count != 3) $action = "";
					else $return["mailid"] = $parts[1];
				}
				//crea nuova mail o svuota il cestino
				if($action == "New" || $action == "EmptyTrash") {	//esempio: /Mail/EmptyTrash
					if($count != 2)
						$action = "";
				}
				//leggi la mail
				if($count == 2) {	//esempio: /Mail/%mail_id%
					$action = "Read";
					$return["mailid"] = $parts[1];
				}
				//pagina di ricerca delle mail
				if($count == 1) {
					$action == "Search";
				}
				break;
			case "Directory":
				//modifica o elimina o leggi le mail di una directory
				if($action == "Edit" || $action == "Delete" || $action == "Mails") {	//esempio: /Directory/%dir_id%/Edit
					if($count != 3) $action = "";
					else $return["directoryid"] = $parts[1];
				}
				//crea nuova dir o leggi inviate o non lette
				if($action == "New" || $action == "Sent" || $action == "Unread") {	//esempio: /Directory/Sent
					if($count != 2)
						$action = "";
				}
				//guarda il contenuto 
				if($count == 2) {	//esempio: /Directory/%dir_id%
					$action = "Mails";
					$return["directoryid"] = $parts[1];
				}
				//pagina di ricerca delle mail nella cartella
				if($count == 1) {
					$action == "Search";
				}
				break;
			case "Partner":
				//TODO
				break;
			default:
				$action = "";
		}
		
		$return["permalink"] = $s;
		$return["object"] = $object;
		if($action != "") $return["action"] = $action;
		else $return["object"] = index;
		//echo "<br />" . serialize($return); //DEBUG
		return $return;
	}
	
	private static function getResponse($request) {
		//recupera i dati dal db
	}
	
	static function make($request) {
		//riceve la richiesta
		//la fa elaborare
		//sceglie le parti da inserire nella pagina
		//recupera la risposta
		//dà la risposta in pasto alla Page giusta
	}
}
?>