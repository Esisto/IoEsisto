<?php
require_once("user/UserManager.php");

class Page {
	
	private static $currentObject;
	private static $currentAction;
	private static $currentPermalink;
	private static $currentID;
	
	
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
		$param_index = strpos($request,"?");
		//echo "<br />" . $param_index; //DEBUG
		$bookmark_index = strpos($request,"#");
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
		self::$currentPermalink = substr($request, $start, $index - $start);
		//echo "<br />" . $return["permalink"]; //DEBUG
		//echo "<br />" . $s; //DEBUG
		$parts = explode("/", self::$currentPermalink);
		$count = count($parts);
		//echo "<p style='color:red'>" . $count . "</p>"; //DEBUG
		//se parts è vuoto eseguo l'index
		if($count == 0) return array("object" => "index");
		
		self::$currentObject = $parts[0];
		self::$currentAction = $parts[$count-1];
		
		//selezione dell'oggetto su cui lavorare
		switch (self::$currentObject) {
			case "Login":
			case "Logout":
			case "Signin":
				if(self::$currentAction != self::$currentObject)
					self::$currentAction = self::$currentObject;
				break;
			case "Contest":
				//modifica o leggi tutti i post di un contest //EDIT E DELETE SOLO ADMIN!!!
				if(self::$currentAction == "Edit" || self::$currentAction == "Posts" || self::$currentAction == "Delete") {	//esempio: /Contest/%contest_id%/Edit
					if($count != 3) self::$currentAction = "";
					else self::$currentID = $parts[1];
				}
				//crea nuovo contest //SOLO ADMIN!!!
				if($action == "New") {	//esempio: /Contest/New
					if($count != 2)
						self::$currentAction = "";
				}
				//leggi la scheda del contest
				if($count == 2) {	//esempio: /Contest/%contest_id%
					self::$currentAction = "Read";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca dei contest
				if($count == 1) {
					self::$currentAction == "Search";
				}
				break;
			case "Category":
				//modifica o leggi tutti i post di una categoria //EDIT, SETPARENT E DELETE SOLO ADMIN!!!
				if(self::$currentAction == "Edit" || self::$currentAction == "Posts" ||
				   self::$currentAction == "Delete" || self::$currentAction == "SetParent") {	//esempio: /Category/%category_name%/Posts
					if($count != 3) self::$currentAction = "";
					else self::$currentID = $parts[1];
				}
				//crea nuova categoria //SOLO ADMIN!!!
				if(self::$currentAction == "New") {	//esempio: /Category/New
					if($count != 2)
						self::$currentAction = "";
				}
				//leggi tutti i post di una categoria //E' UNA COPIA DI QUELLA SOPRA...
				if($count == 2) {	//esempio: /Category/%category_name%
					self::$currentAction = "Posts";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca delle categorie
				if($count == 1) {
					self::$currentAction == "Search";
				}
				break;
			case "Tag":
				//leggi tutti i post di un tag
				if(self::$currentAction == "Posts") {	//esempio: /Tag/%tag_name%/Posts
					if($count != 3) $action = "";
					else self::$currentID = $parts[1];
				}
				//leggi tutti i post di un tag //E' UNA COPIA DI QUELLA SOPRA...
				if($count == 2) {	//esempio: /Tag/%tag_name%
					self::$currentAction = "Posts";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca dei tag
				if($count == 1) {
					self::$currentAction == "Search";
				}
				break;
			case "Post":
				//modifica, vota, commenta, elimina, subscribe o aggiungi a una collezione il post
				if(self::$currentAction == "Edit" || self::$currentAction == "Vote" ||
				   self::$currentAction == "Comment" || self::$currentAction == "Delete" ||
				   self::$currentAction == "Subscribe" || self::$currentAction == "AddToCollection") {	//esempio: /Post/%author%/%post_date%/%post_title%/Edit
					if($count != 5) self::$currentAction = "";
				}
				//leggi il post
				if($count == 4) {	//esempio: /Post/%author%/%post_date%/%post_title%/
					self::$currentAction = "Read";
				}
				//crea nuovo post
				if(self::$currentAction == "New") {	//esempio: /Post/New
					if($count != 2)
						self::$currentAction = "";
					break; //non deve fare altro
				}
				//pagina di ricerca dei post
				if($count == 1) {
					self::$currentAction == "Search";
				} else if(self::$currentAction != "") { //recupera altre informazioni sul post
					self::$currentID = $parts[0] . "/" . $parts[1] . "/" . $parts[2] . "/" . $parts[3];
				}
				break;
			case "Comment":
				//rimuovi un commento
				if(self::$currentAction == "Delete") {	//esempio: /Comment/%comment_id%/Remove
					if($count != 3) self::$currentAction = "";
					else self::$currentID = $parts[1];
				}
				//leggi un commento e relativo post
				if($count == 2) {	//esempio: /Comment/%comment_id%
					self::$currentAction = "Read";
					self::$currentID = $parts[1];
				} else if($count != 3) self::$currentAction = "";
				break;
			case "Vote":
				//rimuovi, modifica un voto
				if(self::$currentAction == "Delete" || self::$currentAction == "Edit") {	//esempio: /Vote/%post_id%/Remove
					if($count != 3) self::$currentAction = "";
					else self::$currentID = $parts[1];
				}
				break;
			case "User":
				//modifica, segui, non seguire, commenta, elimina, verifica, leggi tutti i post di un utente
				if(self::$currentAction == "Edit" || self::$currentAction == "Follow" ||
				   self::$currentAction == "Feedback" || self::$currentAction == "Delete" ||
				   self::$currentAction == "StopFollow" || self::$currentAction == "Verify" ||
				   self::$currentAction == "Posts" || self::$currentAction == "AddContact" ||
				   self::$currentAction == "Mails" ) {	//esempio: /User/%user_nickname%/Verify
					if($count != 3) self::$currentAction = "";
					self::$currentID = $parts[1];
				}
				//registra nuovo utente
				if(self::$currentAction == "New") {	//esempio: /User/New
					if($count != 2)
						self::$currentAction = "";
				}
				//leggi il profilo
				if($count == 2) {	//esempio: /User/%user_nickname%
					self::$currentAction = "Read";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca degli utenti
				if($count == 1) {
					self::$currentAction == "Search";
				}
				break;
			case "Feedback":
				//rimuovi, modifica un voto
				if(self::$currentAction == "Delete") {	//esempio: /Feedback/%subject_id%/Remove
					if($count != 3) self::$currentAction = "";
					else self::$currentID = $parts[1];
				}
				break;
			case "Contact":
				//modifica o elimina un contatto
				if(self::$currentAction == "Edit" || self::$currentAction == "Delete") {	//esempio: /Contact/%contact_id%/Edit
					if($count != 3) self::$currentAction = "";
					else self::$currentID = $parts[1];
				}
				//pagina di ricerca dei contatti
				if($count == 1) {
					self::$currentAction == "Search";
				}
				break;
			case "Mail":
				//modifica, rispondi, sposta nel cestino o in un'altra cartella o segnala come spam una mail
				if(self::$currentAction == "Edit" || self::$currentAction == "Delete" ||
				   self::$currentAction == "Move" || self::$currentAction == "Spam" ||
				   self::$currentAction == "Answer") {	//esempio: /Mail/%mail_id%/Edit
					if($count != 3) self::$currentAction = "";
					else self::$currentID = $parts[1];
				}
				//crea nuova mail o svuota il cestino
				if(self::$currentAction == "New" || self::$currentAction == "EmptyTrash") {	//esempio: /Mail/EmptyTrash
					if($count != 2)
						self::$currentAction = "";
				}
				//leggi la mail
				if($count == 2) {	//esempio: /Mail/%mail_id%
					self::$currentAction = "Read";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca delle mail
				if($count == 1) {
					self::$currentAction == "Search";
				}
				break;
			case "Directory":
				//modifica o elimina o leggi le mail di una directory
				if(self::$currentAction == "Edit" || self::$currentAction == "Delete" || self::$currentAction == "Mails") {	//esempio: /Directory/%dir_id%/Edit
					if($count != 3) self::$currentAction = "";
					else self::$currentID = $parts[1];
				}
				//crea nuova dir o leggi inviate o non lette
				if(self::$currentAction == "New" || self::$currentAction == "Sent" || self::$currentAction == "Unread") {	//esempio: /Directory/Sent
					if($count != 2)
						self::$currentAction = "";
				}
				//guarda il contenuto 
				if($count == 2) {	//esempio: /Directory/%dir_id%
					self::$currentAction = "Mails";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca delle mail nella cartella
				if($count == 1) {
					self::$currentAction == "Search";
				}
				break;
			case "Partner":
				//TODO
				break;
			default:
				self::$currentAction = "";
		}
		
		if(self::$currentAction == "")
			self::$currentObject = "index";
		//echo "<br />" . serialize($return["object"]); //DEBUG
		return $return;
	}
	
	private static function doAction($request) {
		//recupera i dati dal db
		switch (self::$currentObject) {
			case "Signin":
				require_once 'user/UserPage.php';
				UserPage::showSignInForm();
				break;
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
				$posts = SearchManager::searchBy(array("Post"), array(), array("limit" => 1, "order" => "DESC", "by" => array("ps_creationDate")));
				require_once("post/PostPage.php");
				PostPage::showPost($p = $posts[0], self::$post_options);
				self::$currentID = $p->getID();
				break;
		}
	}
	
	private static function doUserAction($request) {
		//echo "<p>" . serialize($request) . "</p>"; //DEBUG
		$user = null;
		if(is_numeric(self::$currentID))
			$user = UserManager::loadUser(self::$currentID);
		else if(isset($request["usernickname"]))
			$user = UserManager::loadUserByNickname(self::$currentID);
		
		switch (self::$currentAction) {
			case "Edit":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'user/UserPage.php';
				UserPage::showEditProfileForm($user);
				break;
			case "Follow":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				$me = UserManager::loadUser(Session::getUser());
				UserManager::followUser($me, $user);
				header("location:" . FileManager::appendToRootPath("User/" . $user->getID()));
				break;
			case "Feedback":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'user/UserPage.php';
				UserPage::showFeedbackForm($user);
				break;
			case "AddContact":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'user/UserPage.php';
				UserPage::showNewContactForm($user);
				break;
			case "StopFollow":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				$me = UserManager::loadUser(Session::getUser());
				UserManager::stopFollowingUser($me, $user);
				header("location:" . FileManager::appendToRootPath("User/" . $user->getID()));
				break;
			case "Verify":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				UserManager::verifyUser($user, $_GET["code"]);
				header("location:" . FileManager::appendToRootPath("User/" . $user->getID()));
				break;
			case "Posts":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error?e=Oops la pagina non è stata trovata."));
				
				require_once 'search/SearchManager.php';
				$posts = SearchManager::searchBy("Post", array("ps_author" => $user->getID()), array("order" => -1, "by" => "ps_creationDate"));
				require_once 'post/PostPage.php';
				foreach($posts as $p)
					PostPage::showPost($p, self::$post_options);
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
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				UserManager::deleteUser($user);
				header("location: " . FileManager::appendToRootPath(""));
				break;
			case "New":
				require_once 'user/UserPage.php';
				UserPage::showSignInForm();
				break;
			case "Read":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
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
		switch (self::$currentAction) {
			case "Edit":
				require_once 'user/User.php';
				$contact = Contact::loadFromDatabase(self::$currentID);
				require_once 'user/UserPage.php';
				UserPage::showEditContactForm($contact);
				break;
			case "Delete":
				require_once 'user/User.php';
				$contact = Contact::loadFromDatabase(self::$currentID);
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
		switch (self::$currentAction) {
			case "Edit":
				require_once 'admin/common.php';
				$contest = AdminContestManager::loadFormDatabase(self::$currentID);
				ContestPage::showEditContestForm($contest);
				break;
			case "Posts":
				require_once 'post/contest/ContestManager.php';
				$contest = ContestManager::loadFormDatabase(self::$currentID);
				require_once("post/PostPage.php");
				foreach($contest->getSubscribers() as $p)
					PostPage::showPost($p, self::$post_options);
				break;
			case "Delete":
				require_once 'admin/common.php';
				$contest = AdminContestManager::loadFormDatabase(self::$currentID);
				AdminContestManager::deleteContest($contest);
				header("location: ");
				break;
			case "New":
				require_once 'admin/common.php';
				ContestPage::showNewContestForm();
				break;
			case "Read":
				require_once 'post/contest/ContestManager.php';
				$contest = ContestManager::loadFormDatabase(self::$currentID);
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
		switch (self::$currentAction) {
			case "Edit":
				require_once 'admin/common.php';
				CategoryPage::showEditCategoryForm(self::$currentID);
				break;
			case "Posts":
				//echo "<p><font color='green'>REQUEST TO LOAD post which category is " . $request["categoryname"] . ".</font></p>"; //DEBUG
				require_once 'search/SearchManager.php';
				$posts = SearchManager::searchBy(array("Post"), array("category" => self::$currentID), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
				require_once("post/PostPage.php");
				foreach($posts as $p)
					PostPage::showPost($p, self::$post_options);
				break;
			case "Delete":
				require_once 'admin/common.php';
				$cat = AdminCategoryManager::deleteCategory(self::$currentID);
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
		switch (self::$currentAction) {
			case "Delete":
				require_once 'post/PostCommon.php';
				$c = Comment::loadFromDatabase(self::$currentID);
				$c->delete();
				require_once 'post/PostManager.php';
				$p = PostManager::loadPostByPermalink($c->getPost());
				header("location: " . $p->getFullPermalink());
				break;
			case "Read":
			default:
				require_once 'post/PostCommon.php';
				$c = Comment::loadFromDatabase(self::$currentID);
				require_once 'post/PostManager.php';
				$p = PostManager::loadPostByPermalink($c->getPost());
				header("location: " . $p->getFullPermalink() . "#" . $c->getID());
				break;
		}
	}
	
	private static function doFeedbackAction($request) {
		switch (self::$currentAction) {
			case "Delete":
				if(is_numeric(self::$currentID))
					$subject = UserManager::loadUser(self::$currentID);
				else
					$subject = UserManager::loadUserByNickname(self::$currentID);
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				UserManager::deleteFeedbackFromUser(Session::getUser(), $subject);
			default:
				header("location: " . FileManager::appendToRootPath("User/" . $user->getID()));
		}
	}
	
	private static function doMailAction($request) {
		switch (self::$currentAction) {
			case "Edit": //una mail non si può modificare...
				break;
			case "Move":
				require_once 'mail/MailManager.php';
				$mail = MailManager::loadMail(self::$currentID);
				require_once 'mail/MailPage.php';
				MailPage::showMoveToForm($mail);
				break;
			case "Delete":
				require_once 'mail/MailManager.php';
				$mail = MailManager::loadMail(self::$currentID);
				$dir = MailManager::directoryForMail($mail, Session::getUser());
				MailManager::moveToTrash($mail, $dir);
				header("location: " . FileManager::appendToRootPath("Directory/" . $dir->getID()));
				break;
			case "Spam":
				require_once 'mail/MailManager.php';
				$mail = MailManager::loadMail(self::$currentID);
				$dir = MailManager::directoryForMail($mail, Session::getUser());
				MailManager::moveToSpam($mail, $dir);
				header("location: " . FileManager::appendToRootPath("Directory/" . $dir->getID()));
				break;
			case "Answer":
				require_once 'mail/MailManager.php';
				$mail = MailManager::loadMail(self::$currentID);
			case "New":
				if(!isset($mail)) $mail = null;
				require_once 'mail/MailPage.php';
				MailPage::showNewForm($mail);
				break;
			case "EmptyTrash":
				require_once 'mail/MailManager.php';
				MailManager::emptyTrash($me);
				header("location: " . FileManager::appendToRootPath("User/" . Session::getUser()->getID() . "/Mails"));
				break;
			case "Read":
				require_once 'mail/MailManager.php';
				MailManager::loadMail(self::$currentID);
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
		switch (self::$currentAction) {
			case "Edit":
				require_once 'mail/MailManager.php';
				$directory = MailManager::loadDirectory(self::$currentID);
				require_once 'mail/MailPage.php';
				MailPage::showEditDirectoryForm($directory);
				break;
			case "Mails":
				require_once 'mail/MailManager.php';
				$directory = MailManager::loadDirectory(self::$currentID);
				require_once 'mail/MailPage.php';
				foreach($directory->getMails() as $mail)
					MailPage::showShortMail($mail);
				break;
			case "Delete":
				require_once 'mail/MailManager.php';
				$directory = MailManager::loadDirectory(self::$currentID);
				MailManager::deleteDirectory($directory);
				$inbox = MailManager::loadDirectoryFromName(MAILBOX, $directory->getUser());
				header("location: " . FileManager::appendToRootPath("Directory/" . $inbox->getID()));
				break;
			case "Sent":
				require_once 'mail/MailManager.php';
				$mails = MailManager::getMailSent(Session::getUser());
				foreach($directory->getMails() as $mail)
					MailPage::showShortMail($mail);
				break;
			case "Unread":
				//@deprecated non ce n'è bisogno...
				require_once 'mail/MailManager.php';
				$inbox = MailManager::loadDirectoryFromName(MAILBOX, Session::getUser());
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
		switch (self::$currentAction) {
			case "Delete":
				require_once 'post/PostManager.php';
				$vote = PostManager::loadVote(Session::getUser(), self::$currentID);
				PostManager::removeVote($vote);
				header("location: " . FileManager::appendToRootPath("Post/" . self::$currentID));
				break;
			case "Edit": //fare /Post/postid/Vote invece...
			default:
				header("location: " . FileManager::appendToRootPath("Post/" . self::$currentID));
				break;
		}
	}
	
	private static function doPartnerAction($request) {} //TODO da implementare
	
	private static function doResourceAction($request) {} //TODO da implementare
	
	private static function doPreferencesAction($request) {} //TODO da implementare
	
	private static function doTagAction($request) {
		switch (self::$currentAction) {
			case "Posts":
				//echo "<p><font color='green'>REQUEST TO LOAD post which tag is " . $request["tagname"] . ".</font></p>"; //DEBUG
				$posts = SearchManager::searchBy(array("Post"), array("tag" => self::$currentID), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")));
				require_once("post/PostPage.php");
				foreach($posts as $p)
					PostPage::showPost($p, self::$post_options);
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php';
				SearchPage::showTagSearchForm();
				break;
		}
	}
	
	private static function doPostAction($request) {
		require_once 'post/PostManager.php';
		//echo "<p>" . $request["action"] . "</p>"; //DEBUG
		switch(self::$currentAction) {
			//modifica, vota, commenta, elimina, subscribe o aggiungi a una collezione il post
			case "Read":
				//echo "<p><font color='green'>" . $request["permalink"] . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink(self::$currentID);
				require_once("post/PostPage.php");
				PostPage::showPost($p, self::$post_options);
				break;
			case "Edit":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink(self::$currentID);
				require_once("post/PostPage.php");
				PostPage::showEditPostForm($p);
				break;
			case "Vote":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink(self::$currentID);
				require_once("post/PostPage.php");
				//controllo su vote.
				if(isset($_GET["vote"])) {
					if($_GET["vote"] == "y" || $_GET["vote"] == "yes")
						$vote = true;
					if($_GET["vote"] == "n" || $_GET["vote"] == "no")
						$vote = false;
					if(!isset($_GET["vote"])) header("location: " . FileManager::appendToRootPath("error.php?error=Oops, il voto da te inserito non è valido."));
					PostManager::votePost(Session::getUser()->getID(), $p, $vote);
				}
				PostPage::showPost($p, self::$post_options);
				break;
			case "Comment":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink(self::$currentID);
				require_once("post/PostPage.php");
				PostPage::showCommentForm($p);
				break;
			case "Delete":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink(self::$currentID);
				require_once("post/PostPage.php");
				PostManager::deletePost($p);
				header("location: " . FileManager::getServerPath());
				break;
			case "Subscribe":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink(self::$currentID);
				require_once("post/PostPage.php");
				PostPage::showContestForm();
				break;
			case "AddToCollection":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				$p = PostManager::loadPostByPermalink(self::$currentID);
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
		if(self::$currentObject == "Login")
			self::redirect("");
		else if(self::$currentObject == "Logout") {
			Session::destroy();
			self::redirect("");
		}
		//return; //DEBUG
		$default = TemplateManager::getDefaultTemplate();
		$parser = null; $tentativi = 0;
		while(is_numeric($parser) || is_null($parser)) {
			$template = TemplateManager::getTemplateForRequest(self::$currentObject, self::$currentID, self::$currentAction);
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
					//echo "<p>element: " . serialize($el) . "</p>";  //DEBUG
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
					}
					if(isset($el["value"]) && $el["value"] != "\n") {
						self::evaluateText($el["value"], $data);
					}
					if($el["type"] == "close" || $el["type"] == "complete") {
						if($el["type"] == "close")
							unset($cols_stack[count($cols_stack)-1]);
						closediv();
					}
					break;
			}
			//echo "<p style='color:red;'>element: " . $el["tag"] . "</p>";  //DEBUG
		}
	}
	
	private static function evaluateText($text, $data) {
		$text = trim($text, " \t\n");
		$funcs = explode("\n", $text);
		//echo serialize($funcs);
		foreach($funcs as $func) {
			if($func == "") continue;
			
			$func = trim($func, " \t\n");
			//echo "<p style='color:red;'>|" . $func . "|</p>";
			if(substr($func,0,5)=="PCCat" && is_numeric(substr($func,-1,1))) {
				call_user_func(array("Page", substr($func, 0,5)), $data, substr($func,-1,1));
			} else if($a = method_exists("Page", $func)) {
				call_user_func(array("Page", $func), $data);
			} else {
				writePlainText($func);
			}
			//echo serialize($a);
		}
	}
	
	private static $post_options = array();
	private static function PCMain($data) {
		require_once 'post/PostPage.php';
		if(self::$currentObject == "index") {
			self::$post_options[PostPage::NO_DATE] = true;
			self::$post_options[PostPage::NO_COMMENTS] = true;
		}
		
		self::doAction($data);
		
		self::$post_options[PostPage::NO_DATE] = false;
		self::$post_options[PostPage::NO_COMMENTS] = false;
	}
	
	private static function PCComments($data) {
		echo "Commenti";
	}
	
	private static function PCRelated($data) {
		echo "Vedi anche";
		
		$p = PostManager::loadPost(self::$currentID);
		
		require_once 'search/SearchManager.php';
		$posts = SearchManager::searchBy(array("Post"),
										array("author" => $p->getAuthor()),
										array("limit" => 3, "order" => "DESC", "by" => array("ps_creationDate")));
		$posts2 = SearchManager::searchBy(array("Post"),
										array("tag" => $p->getTags(), "category" => $p->getCategories()),
										array("limit" => 3, "order" => "DESC", "by" => array("ps_creationDate")));
		if(is_array($posts2))
			array_merge($posts, $posts2); 
		foreach($posts as $post) {
			if($post->getID() != $p->getID()) {
				require_once 'post/PostPage.php';
				self::$post_options[PostPage::SHORTEST] = true;
				PostPage::showPost($post, self::$post_options);
				self::$post_options[PostPage::SHORTEST] = false;
			}
		}
		
	}
	
	private static $WHO_POST = 1;
	private static function PCWho($data) {
//		echo "Chi Siamo";
		require_once 'post/PostManager.php';
		$p = PostManager::loadPost(self::$WHO_POST);
		if($p !== false) {
			require_once 'post/PostPage.php';
			self::$post_options[PostPage::SHORT] = true;
			self::$post_options[PostPage::NO_COMMENTS] = true;
			self::$post_options[PostPage::NO_CATEGORIES] = true;
			self::$post_options[PostPage::NO_TAGS] = true;
			self::$post_options[PostPage::NO_MODIF_DATE] = true;
			PostPage::showPost($p, self::$post_options);
			self::$post_options[PostPage::SHORT] = false;
			self::$post_options[PostPage::NO_COMMENTS] = false;
			self::$post_options[PostPage::NO_TAGS] = false;
			self::$post_options[PostPage::NO_MODIF_DATE] = false;
		}
	}
	
	private static function PCSearch($data) {
		require_once 'search/SearchPage.php';
		SearchPage::showDefaultSearchForm();
		//echo "Cerca";
	}
	
	private static function PCCategories($data) {
		echo "Categorie";
	}
	
	private static function PCCat($data, $num) {
		echo "<p>Categoria " . $num . "</p>";
	}
	
	private static function PCRandomPosts($data) {
		echo "<p>Random Post</p>";
	}
	
	private static function PCFollows($data) {
		echo "<p>Chi segui</p>";
	}
	
	private static function PCFollow($data) {
		echo "<p>Follow</p>";
	}
	
	private static function PCFollowers($data) {
		echo "<p>Chi ti segue</p>";
	}
	
	private static function PCFollower($data) {
		echo "<p>Follower</p>";
	}
	
	private static function PCAuthor($data) {
		echo "<p>L'autore</p>";
	}
	
	private static function PCSameAuthor($data) {
		echo "<p>Dello stesso autore</p>";
	}
	
	private static  function redirect($where = "") {
		if(!headers_sent()) {
			header("location: " . self::createLinkPath($where));
		} else {
			?>
			<script type="text/javascript">
				location.href = "<?php echo self::createLinkPath($where); ?>";
			</script>
			<?php
		}
	}
	
	private static function createLinkPath($where = "") {
		require_once 'file_manager.php';
		return FileManager::appendToRootPath($where);
	}
}
?>