<?php
require_once("user/UserManager.php");

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
		//echo "<br />" . $param_index; //DEBUG
		$bookmark_index = strpos($request,"#"); //TODO: TEST ME
		$get = $param_index || $bookmark_index;
		$index = strlen($request);
		if($get) {
			if(!$param_index || ($bookmark_index && $bookmark_index < $param_index)) {
				//echo "<br />" . $param_index; //DEBUG
				$index = $bookmark_index;
			} else if(!$bookmark_index || ($param_index && $param_index < $bookmark_index)) {
				$index = $param_index;
				//echo "<br />" . $bookmark_index; //DEBUG
			}
		}
		$start = strlen(dirname($_SERVER["PHP_SELF"])) + 1; //il +1 è per lo /
		$return = array();
		//echo "<br />" . $start . " " . $index; //DEBUG
		$return["permalink"] = substr($request, $start, $index - $start); //TODO: TEST ME
		//echo "<br />" . $return["permalink"]; //DEBUG
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
				   $action == "Posts" || $action == "AddContact" ||
				   $action == "Mails" ) {	//esempio: /User/%user_nickname%/Verify
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
		
		$return["object"] = $object;
		if($action != "") $return["action"] = $action;
		else $return["object"] = "index";
		//echo "<br />" . serialize($return); //DEBUG
		return $return;
	}
	
	private static function doAction($request) {
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
				require_once 'search/SearchManager.php';
				$posts = SearchManager::searchBy(array("Post"), array(), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
				require_once("post/PostPage.php");
				foreach($posts as $p)
					PostPage::showPost($p);
				break;
		}
	}
	
	private static function doUserAction($request) {
		$user = null;
		if(isset($request["userid"]))
			$user = UserManager::loadUser($request["userid"]);
		else if(isset($request["usernickname"]))
			$user = UserManager::loadUserByNickname($request["usernickname"]);
		
		switch ($request["action"]) {
			case "Edit":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'user/UserPage.php';
				UserPage::showEditProfileForm($user);
				break;
			case "Follow":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				$me = UserManager::loadUser(Session::getUser());
				UserManager::followUser($me, $user);
				header("location:" . FileManager::appendToRootPath("User/" . $user->getID()));
				break;
			case "Feedback":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'user/UserPage.php';
				UserPage::showFeedbackForm($user);
				break;
			case "AddContact":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'user/UserPage.php';
				UserPage::showNewContactForm($user);
				break;
			case "StopFollow":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				$me = UserManager::loadUser(Session::getUser());
				UserManager::stopFollowingUser($me, $user);
				header("location:" . FileManager::appendToRootPath("User/" . $user->getID()));
				break;
			case "Verify":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				UserManager::verifyUser($user, $_GET["code"]);
				header("location:" . FileManager::appendToRootPath("User/" . $user->getID()));
				break;
			case "Posts":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'search/SearchManager.php';
				$posts = SearchManager::searchBy("Post", array("ps_author" => $user->getID(), array()));
				require_once 'post/PostPage.php';
				foreach($posts as $p)
					PostPage::showShortPost($p);
				break;
			case "Mails":
				require_once 'mail/MailManager.php';
				$me = UserManager::loadUser(Session::getUser());
				$mails = MailManager::loadDirectoryFromName(MAILBOX, $me);
				require_once 'mail/MailPage.php';
				foreach($mails as $mail)
					MailPage::showShortMail($mail);
				break;
			case "Delete":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				UserManager::deleteUser($user);
				header("location: " . FileManager::appendToRootPath(""));
				break;
			case "New":
				require_once 'user/UserPage.php';
				UserPage::showSignInForm();
				break;
			case "Read":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'user/UserPage.php';
				UserPage::showProfile($user);
				break;
			case "Search":
			default:
				require_once("search/SearchPage.php");
				SearchPage::showUserSearchForm($p);
				break;
		}
	}
	
	private static function doContactAction($request) {
		switch ($request["action"]) {
			case "Edit":
				require_once 'user/User.php';
				$contact = Contact::loadFromDatabase($request["contactid"]);
				require_once 'user/UserPage.php';
				UserPage::showEditContactForm($contact);
				break;
			case "Delete":
				require_once 'user/User.php';
				$contact = Contact::loadFromDatabase($request["contactid"]);
				$user = UserManager::loadUser($contact->getUser());
				UserManager::deleteContact($contact, $user);
				require_once 'user/UserPage.php';
				UserPage::showProfile($user);
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php';
				SearchPage::showContactSearchForm();
				break;
		}
	}
	
	private static function doContestAction($request) {
		switch ($request["action"]) {
			case "Edit":
				require_once 'admin/common.php';
				$contest = AdminContestManager::loadFormDatabase($request["contestid"]);
				ContestPage::showEditContestForm($contest);
				break;
			case "Posts":
				require_once 'post/contest/ContestManager.php';
				$contest = ContestManager::loadFormDatabase($request["contestid"]);
				require_once("post/PostPage.php");
				foreach($contest->getSubscribers() as $p)
					PostPage::showPost($p);
				break;
			case "Delete":
				require_once 'admin/common.php';
				$contest = AdminContestManager::loadFormDatabase($request["contestid"]);
				AdminContestManager::deleteContest($contest);
				header("location: ");
				break;
			case "New":
				require_once 'admin/common.php';
				ContestPage::showNewContestForm();
				break;
			case "Read":
				require_once 'post/contest/ContestManager.php';
				$contest = ContestManager::loadFormDatabase($request["contestid"]);
				require_once 'post/PostPage.php';
				PostPage::showContestDetails($contest);
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php';
				SearchPage::showContactSearchForm();
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
				require_once 'search/SearchManager.php';
				$posts = SearchManager::searchBy(array("Post"), array("category" => $request["categoryname"]), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
				require_once("post/PostPage.php");
				foreach($posts as $p)
					PostPage::showPost($p);
				break;
			case "Delete":
				require_once 'admin/common.php';
				$cat = AdminCategoryManager::deleteCategory($request["categoryid"]);
				header("location: ");
				break;
			case "New":
				require_once 'admin/common.php';
				CategoryPage::showNewCategoryForm();
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php';
				SearchPage::showCategorySearchForm();
				break;
		}
	}
	
	private static function doCommentAction($request) {
		switch ($request["action"]) {
			case "Delete":
				require_once 'post/PostCommon.php';
				$c = Comment::loadFromDatabase($request["commentid"]);
				$c->delete();
				require_once 'post/PostManager.php';
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
				if(isset($request["userid"]))
					$user = UserManager::loadUser($request["subjectid"]);
				else if(isset($request["usernickname"]))
					$user = UserManager::loadUserByNickname($request["subjectnickname"]);
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				$me = UserManager::loadUser(Session::getUser());
				UserManager::deleteFeedbackFromUser($me, $subject);
			default:
				header("location: " . FileManager::appendToRootPath("User/" . $user->getID()));
		}
	}
	
	private static function doMailAction($request) {
		switch ($request["action"]) {
			case "Edit": //una mail non si può modificare...
				break;
			case "Move":
				require_once 'mail/MailManager.php';
				$mail = MailManager::loadMail($request["mailid"]);
				require_once 'mail/MailPage.php';
				MailPage::showMoveToForm($mail);
				break;
			case "Delete":
				require_once 'mail/MailManager.php';
				$mail = MailManager::loadMail($request["mailid"]);
				$me = UserManager::loadUser(Session::getUser());
				$dir = MailManager::directoryForMail($mail, $me);
				MailManager::moveToTrash($mail, $dir);
				header("location: " . FileManager::appendToRootPath("Directory/" . $dir->getID()));
				break;
			case "Spam":
				require_once 'mail/MailManager.php';
				$mail = MailManager::loadMail($request["mailid"]);
				$me = UserManager::loadUser(Session::getUser());
				$dir = MailManager::directoryForMail($mail, $me);
				MailManager::moveToSpam($mail, $dir);
				header("location: " . FileManager::appendToRootPath("Directory/" . $dir->getID()));
				break;
			case "Answer":
				require_once 'mail/MailManager.php';
				$mail = MailManager::loadMail($request["mailid"]);
			case "New":
				if(!isset($mail)) $mail = null;
				require_once 'mail/MailPage.php';
				MailPage::showNewForm($mail);
				break;
			case "EmptyTrash":
				require_once 'mail/MailManager.php';
				$me = UserManager::loadUser(Session::getUser());
				MailManager::emptyTrash($me);
				header("location: " . FileManager::appendToRootPath("User/" . $me->getID() . "/Mails"));
				break;
			case "Read":
				require_once 'mail/MailManager.php';
				MailManager::loadMail($request["mailid"]);
				require_once 'mail/MailPage.php';
				MailPage::showMail($mail);
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php';
				SearchPage::showMailSearchForm();
				break;
		}
	}
	
	private static function doDirectoryAction($request) {
		switch ($request["action"]) {
			case "Edit":
				require_once 'mail/MailManager.php';
				$directory = MailManager::loadDirectory($request["directoryid"]);
				require_once 'mail/MailPage.php';
				MailPage::showEditDirectoryForm($directory);
				break;
			case "Mails":
				require_once 'mail/MailManager.php';
				$directory = MailManager::loadDirectory($request["directoryid"]);
				require_once 'mail/MailPage.php';
				foreach($directory->getMails() as $mail)
					MailPage::showShortMail($mail);
				break;
			case "Delete":
				require_once 'mail/MailManager.php';
				$directory = MailManager::loadDirectory($request["directoryid"]);
				MailManager::deleteDirectory($directory);
				$inbox = MailManager::loadDirectoryFromName(MAILBOX, $directory->getUser());
				header("location: " . FileManager::appendToRootPath("Directory/" . $inbox->getID()));
				break;
			case "Sent":
				require_once 'mail/MailManager.php';
				$me = UserManager::loadUser(Session::getUser());
				$mails = MailManager::getMailSent($me);
				foreach($directory->getMails() as $mail)
					MailPage::showShortMail($mail);
				break;
			case "Unread":
				//@deprecated non ce n'è bisogno...
				require_once 'mail/MailManager.php';
				$me = UserManager::loadUser(Session::getUser());
				$inbox = MailManager::loadDirectoryFromName(MAILBOX, $me);
				header("location: " . FileManager::appendToRootPath("Directory/" . $inbox->getID()));
				break;
			case "New":
				require_once 'mail/MailPage.php';
				MailPage::showNewDirectoryForm();
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php';
				SearchPage::showMailSearchForm();
				break;
		}
	}
	
	private static function doVoteAction($request) {
		switch ($request["action"]) {
			case "Delete":
				require_once 'post/PostManager.php';
				$me = UserManager::loadUser(Session::getUser());
				$vote = PostManager::loadVote($me, $require["postid"]);
				PostManager::removeVote($vote);
				header("location: " . FileManager::appendToRootPath("Post/" . $require["postid"]));
				break;
			case "Edit": //fare /Post/postid/Vote invece...
			default:
				header("location: " . FileManager::appendToRootPath("Post/" . $require["postid"]));
				break;
		}
	}
	
	private static function doPartnerAction($request) {} //TODO da implementare
	
	private static function doResourceAction($request) {} //TODO da implementare
	
	private static function doPreferencesAction($request) {} //TODO da implementare
	
	private static function doTagAction($request) {
		switch ($request["action"]) {
			case "Posts":
				//echo "<p><font color='green'>REQUEST TO LOAD post which tag is " . $request["tagname"] . ".</font></p>"; //DEBUG
				$posts = SearchManager::searchBy(array("Post"), array("tag" => $request["tagname"]), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
				require_once("post/PostPage.php");
				foreach($posts as $p)
					PostPage::showPost($p);
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php';
				SearchPage::showTagSearchForm();
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
				PostPage::showEditPostForm($p);
				break;
			case "Vote":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink($request["permalink"]);
				require_once("post/PostPage.php");
				//controllo su vote.
				if(isset($_GET["vote"])) {
					if($_GET["vote"] == "y")
						$vote = true;
					if($_GET["vote"] == "n")
						$vote = false;
					if(!isset($vote)) header("location: " . FileManager::appendToRootPath("error.php?error=Oops, il voto da te inserito non è valido."));
					$me = UserManager::loadUser(Session::getUser());
					PostManager::votePost($me, $p, $_GET["vote"]);
				}
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
				PostPage::showNewPostForm();
				break;
			case "Search":
			default:
				require_once("search/SearchPage.php");
				SearchPage::showPostSearchForm($p);
			break;
		}
	}
	
	private static function canUserDo($object, $objectType, $action) {
		return true; //TODO deve leggere le autorizzazioni per il tipo di utente.
	}
	
	public static function titleForRequest($request) {
		return "IoEsisto";
	} 
	
	public static function getResponse($request) {
		require_once 'web/header.inc';
		require_once 'web/page.inc';
		require_once 'web/footer.inc';
		require_once 'template/TemplateManager.php';
		
		$data = self::elaborateRequest($request);
		//return; //DEBUG
		$default = TemplateManager::getDefaultTemplate();
		$parser = null; $tentativi = 0;
		while(is_numeric($parser) || is_null($parser)) {
			$template = TemplateManager::getTemplateForRequest($data);
			if(is_numeric($parser) || is_null($template) || $template === false)
				$template = $default;
			if($tentativi == 1)
				echo "<h3>ERRORE IN " . $template . "</h3>";
			if($tentativi == 2) {
				echo "<h3>ERRORE NEL TEMPLATE DI DEFAULT</h3>";
				return;
			}
			$tentativi++;
			
			$parser = TemplateParser::parseTemplate($template);
			//echo "<p>" . serialize(is_numeric($parser)) . "</p>"; //DEBUG
		}
		//echo "parser creato: " . serialize($parser) . "<br />";  //DEBUG
		$css = "default"; $title = self::titleForRequest($request);
		$cols_stack = array();
		$write_h = false; $write_f = false; $ad = false;
//		$i=0; //DEBUG
		while($el = $parser->nextElement()) {
//			if($i==10) //DEBUG
//				return; //DEBUG
//			$i++; //DEBUG
			$id = null; $class = null;
			switch ($el["tag"]) {
				case "TEMPLATE":
					if($el["type"] != "open") continue;
					$css = $el["attributes"]["STYLE"];
					$c = array("default/default");
					if($css != "default/default")
						$c[] = $css;
					writeHeader($title, $c, $c);
					break;
				case "HEADER":
					$write_h = true;
					if($el["type"] == "close") {
						writePageHeader($ad);
						$write_h = false;
					}
					break;
				case "FOOTER":
					$write_f = true;
					if($el["type"] == "close" || $el["type"] == "complete") {
						writeFooter($ad);
						$write_f = false;
					}
					break;
				case "AD":
					if($write_h || $write_f)
						$ad = true;
					else {
						$style = "default";
						if(isset($el["attributes"]["STYLE"]))
							$style = $el["attributes"]["STYLE"];
						writeAD($style);
					}
					break;
				case "CONTENT":
					$id = "content";
				case "COL":
					//echo "<p>element: " . serialize($el) . "</p>";  //DEBUG
				case "DIV":
					if($el["type"] == "open" || $el["type"] == "complete") {
						if(isset($el["attributes"]["COLS"]))
							$cols_stack[] = $el["attributes"]["COLS"];
						else
							$cols_stack[] = 1;
						if(isset($el["attributes"]["ID"]))
							$id = $el["attributes"]["ID"];
						if(isset($el["attributes"]["CLASS"]))
							$class = $el["attributes"]["CLASS"];
						opendiv($class, $id);
						if(isset($el["value"]) && $el["value"] != "\n") {
							self::evaluateText($el["value"], $data);
						}
					}
					if($el["type"] == "close" || $el["type"] == "complete") {
						if($el["type"] == "close")
							unset($cols_stack[count($cols_stack)-1]);
						closediv();
					}
					break;
			}
			//echo "<p>element: " . serialize($el) . "</p>";  //DEBUG
		}
	}
	
	private static function evaluateText($text, $data) {
		$text = trim($text, " \t\n");
		$funcs = explode("\n", $text);
		//echo serialize($funcs);
		foreach($funcs as $func) {
			if($func == "") continue;
			
			$func = trim($func, " \t\n");
			//echo "<p style='color:red;'>|" . substr($func,-1,1) . "|</p>";
			if(substr($func,0,5)=="PCCat" && is_numeric(substr($func,-1,1))) {
				call_user_func(array("Page", substr($func, 0,5)), $data, substr($func,-1,1));
			} else if($a = method_exists("Page", $func)) {
				call_user_func_array(array("Page", $func), $data);
			} else {
				writePlainText($func);
			}
			//echo serialize($a);
		}
	}
	
	private static function PCMain($data) {
		echo "Main";
		self::doAction($data);
	}
	
	private static function PCComments($data) {
		echo "Commenti";
	}
	
	private static function PCRelated($data) {
		echo "Vedi anche";
	}
	
	private static $WHO_POST = 1000;
	private static function PCWho($data) {
		echo "Chi Siamo";
//		require_once 'post/PostManager.php';
//		$p = PostManager::loadPost(self::$WHO_POST);
//		if($p !== false) {
//			require_once 'post/PostPage.php';
//			PostPage::showPost($p, array("short"));
//		}
	}
	
	private static function PCSearch($data) {
//		require_once 'search/SearchPage.php';
//		SearchPage::showDefaultSearchForm();
		echo "Cerca";
	}
	
	private static function PCCategories($data) {
		echo "Categorie";
	}
	
	private static function PCCat($data, $num) {
		echo "<p>Categoria " . $num . "</p>";
	}
}

?>