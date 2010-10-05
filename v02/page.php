<?php

class Page {
	/**
	 * Elabora le richieste fatte al server dall'utente per scegliere lo script da eseguire e su quali dati.
	 * Non esegue lo script ma fornisce i dati per sceglierlo.
	 * 
	 * @param $reqest: un URL relativo. Deve essere in una di queste forme:
	 * %oggetto%/
	 * %oggetto%/%identificativo oggetto%/
	 * %oggetto%/%identificativo oggetto%/%azione%/
	 * %oggetto%/%identificativo oggetto%/%azione%/?%nome parametro%=%valore parametro%
	 * 
	 * @return un array associativo contenente i seguenti parametri:
	 * object => l'oggetto o se c'è un errore: index.
	 * identificativo (o identificativi) dell'oggetto.
	 * permalink oggetto => il permalink (che è anche la richiesta).
	 * azione => l'azione da eseguire sull'oggetto.
	 * il parametro potrà ancora essere recuperato attraverso $_GET[%nome parametro%].
	 */
	private static function elaborateRequest($request) {
		require_once("file_manager.php");
		//echo "<br />" . $request; //DEBUG
		$param_index = strpos($request,"?"); //TODO: TEST ME
		$bookmark_index = strpos($request,"#"); //TODO: TEST ME
		$get = $param_index || $bookmark_index;
		$index = strlen($request);
		if($get) {
			if(!$param_index || $bookmark_index < $param_index) $index = $bookmark_index;
			else if(!$bookmark_index || $param_index < $bookmark_index) $index = $param_index-1;
		}
		$start = strlen(dirname($_SERVER["PHP_SELF"])) + 1;
		$return = array();
		$return["permalink"] = substr($request, $start, $index - $start); //TODO: TEST ME
		//echo "<br />" . $s; //DEBUG
		$parts = explode("/", $return["permalink"]);
		$count = count($parts);
		//se parts è vuoto eseguo l'index
		if($count == 0) return array("object" => "index");
		
		$object = $parts[0];
		$action = $parts[$count-1];
		
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
				//modifica o leggi tutti i post di una categoria //EDIT, SETPARENT E DELETE SOLO ADMIN!!!
				if($action == "Edit" || $action == "Posts" ||
				   $action == "Delete" || $action == "SetParent") {	//esempio: /Category/%category_name%/Posts
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
				//modifica, vota, commenta, elimina, subscribe o aggiungi a una collezione il post
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
				   $action == "Posts" || $action == "AddContact") {	//esempio: /User/%user_nickname%/Verify
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
		else $return["object"] = "index";
		//echo "<br />" . serialize($return); //DEBUG
		return $return;
	}
	
	private static function getResponse($request) {
		//recupera i dati dal db
		switch ($request["object"]) {
			case "Post":
				self::doPostAction($request);
				break;
			case "User":
				self::doUserAction($request);
				break;
			case "Contest":
				self::doContestAction($request);
				break;
			case "Category":
				self::doCategoryAction($request);
				break;
			case "Comment":
				self::doCommentAction($request);
				break;
			case "Feedback":
				self::doFeedbackAction($request);
				break;
			case "Contact":
				self::doContactAction($request);
				break;
			case "Mail":
				self::doMailAction($request);
				break;
			case "Directory":
				self::doDirectoryAction($request);
				break;
			case "Vote":
				self::doVoteAction($request);
				break;
			case "Partner":
				self::doPartnerAction($request);
				break;
			case "Tag":
				self::doTagAction($request);
				break;
			case "index":
			default:
				//TODO: creare pagina index
				$posts = SearchManager::searchBy(array("Post"), array(), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
				foreach($posts as $p) {
					require_once("post/PostPage.php");
					PostPage::showPost($p);
				}
				break;
		}
	}
	
	static function make($request) {
		//riceve la richiesta
		//la fa elaborare
		$req = self::elaborateRequest($request);
		
		//sceglie le parti da inserire nella pagina
		//header, menù, ecc...
		
		//recupera la risposta
		$r = self::getResponse($request);
		//dà la risposta in pasto alla Page giusta
		if($req["object"] == "Post") { //è un esempio...
			PostPage::showPost();
		}
		return $req;
	}
	
	private static function doUserAction($request) {
		switch ($request["action"]) {
			case "Edit":
				break;
			case "Follow":
			case "Feedback":
			case "AddContact":
			case "StopFollow":
			case "Verify":
			case "Posts":
			case "Delete":
			case "New":
			case "Read":
			case "Search":
			default:
				break;
		}
	}
	
	private static function doContactAction($request) {
		switch ($request["action"]) {
			case "Edit":
				break;
			case "Delete":
			case "Search":
			default:
				break;
		}
	}
	
	private static function doContestAction($request) {
		switch ($request["action"]) {
			case "Edit":
				break;
			case "Posts":
			case "Delete":
			case "New":
			case "Read":
			case "Search":
			default:
				break;
		}
	}
	
	private static function doCategoryAction($request) {
		switch ($request["action"]) {
			case "Edit":
				require_once 'admin/common.php';
				CategoryPage::showEditCategoryForm($request["categoryid"]);
				break;
			case "Posts":
				//echo "<p><font color='green'>REQUEST TO LOAD post which category is " . $request["categoryname"] . ".</font></p>"; //DEBUG
				$posts = SearchManager::searchBy(array("Post"), array("category" => $request["categoryname"]), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
				foreach($posts as $p) {
					require_once("post/PostPage.php");
					PostPage::showPost($p);
				}
				break;
			case "Delete":
				require_once 'admin/common.php';
				$cat = AdminCategoryManager::deleteCategory($request["categoryid"]);
				header("location: ");
				break;
			case "New":
				require_once 'admin/common.php';
				$cat = CategoryPage::showNewCategoryForm();
				header("location: " . FileManager::appendToRootPath("Category/" . $cat));
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php'; //TODO
				//SearchPage::showCategorySearchForm();
				break;
		}
	}
	
	private static function doCommentAction($request) {
		switch ($request["action"]) {
			case "Delete":
				require_once 'post/PostCommon.php';
				$c = Comment::loadFromDatabase($request["commentid"]);
				$c->delete();
				$p = PostManager::loadPostByPermalink($c->getPost());
				header("location: " . $p->getFullPermalink());
				break;
			case "Read":
			default:
				require_once 'post/PostCommon.php';
				$c = Comment::loadFromDatabase($request["commentid"]);
				require_once 'post/PostManager.php';
				$p = PostManager::loadPostByPermalink($c->getPost());
				header("location: " . $p->getFullPermalink() . "#" . $c->getID());
				break;
		}
	}
	
	private static function doFeedbackAction($request) {
		switch ($request["action"]) {
			case "Delete":
		}
	}
	
	private static function doMailAction($request) {
		switch ($request["action"]) {
			case "Edit":
				break;
			case "Move":
			case "Delete":
			case "Spam":
			case "Answer":
			case "New":
			case "EmptyTrash":
			case "Read":
			case "Search":
			default:
				break;
		}
	}
	
	private static function doDirectoryAction($request) {
		switch ($request["action"]) {
			case "Edit":
				break;
			case "Mails":
			case "Delete":
			case "Sent":
			case "Unread":
			case "New":
			case "Search":
			default:
				break;
		}
	}
	
	private static function doVoteAction($request) {
		switch ($request["action"]) {
			case "Delete":
			case "Edit":
			default:
				break;
		}
	}
	
	private static function doPartnerAction($request) {}
	
	private static function doTagAction($request) {
		switch ($request["action"]) {
			case "Posts":
				//echo "<p><font color='green'>REQUEST TO LOAD post which tag is " . $request["tagname"] . ".</font></p>"; //DEBUG
				$posts = SearchManager::searchBy(array("Post"), array("tag" => $request["tagname"]), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
				foreach($posts as $p) {
					require_once("post/PostPage.php");
					PostPage::showPost($p);
				}
				break;
			case "Search":
			default:
				break;
		}
	}
	
	private static function doPostAction($request) {
		switch ($request["action"]) {
			//modifica, vota, commenta, elimina, subscribe o aggiungi a una collezione il post
			case "Read":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink($request["permalink"]);
				require_once("post/PostPage.php");
				PostPage::showPost($p);
				break;
			case "Edit":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink($request["permalink"]);
				require_once("post/PostPage.php");
				PostPage::showEditForm($p);
				break;
			case "Vote":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink($request["permalink"]);
				require_once("post/PostPage.php");
				//TODO: controllo su vote.
				PostManager::votePost(Session::getUser(), $p, $_GET["vote"]);
				PostPage::showPost($p);
				break;
			case "Comment":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink($request["permalink"]);
				require_once("post/PostPage.php");
				PostPage::showCommentForm($p);
				break;
			case "Delete":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink($request["permalink"]);
				require_once("post/PostPage.php");
				PostManager::deletePost($p);
				header("location: " . FileManager::getServerPath());
				break;
			case "Subscribe":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink($request["permalink"]);
				require_once("post/PostPage.php");
				PostPage::showContestForm();
				break;
			case "AddToCollection":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink($request["permalink"]);
				require_once("post/PostPage.php");
				PostPage::showCollectionForm($p);
				break;
			case "New":
				require_once("post/PostPage.php");
				PostPage::showPostForm($p);
				break;
			case "Search":
			default:
				require_once("post/SearchPage.php");
				SearchPage::showPostSearchForm($p);
			break;
		}
	}
}
?>