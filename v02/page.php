<?php
require_once("user/UserManager.php");

class Page {
	private static $requestedObject;
	private static $requestedAction;
	private static $currentPermalink;
	private static $currentID;
	private static $currentObject;
	private static $user;
	
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
		
		self::$requestedObject = $parts[0];
		self::$requestedAction = $parts[$count-1];
		
		//selezione dell'oggetto su cui lavorare
		switch (self::$requestedObject) {
			case "error.php":
			case "error":
				self::$requestedObject = "index";
				self::$requestedAction = "error";
				break;
			case "Login":
			case "Logout":
			case "Signin":
				if(self::$requestedAction != self::$requestedObject)
					self::$requestedAction = self::$requestedObject;
				break;
			case "Profile":
				if(self::$user === false) {
					self::$requestedAction = "";
					break;
				}
				if(self::$requestedAction == self::$requestedObject) self::$requestedAction = "Read";
				self::$requestedObject = "User";
				self::$currentID = self::$user->getNickname();
				self::$currentObject = self::$user;
				break;
			case "Favourites":
				if(self::$user === false) {
					self::$requestedAction = "";
					break;
				}
				if(self::$requestedAction == self::$requestedObject) self::$requestedAction = "Read";
				self::$requestedObject = "Post";
				require_once 'search/SearchManager.php';
				$p = SearchManager::searchBy("Post", array("type" => "collection", "title" => "Favourites", "author" => self::$user->getID()), array());
				if(is_array($p) && count($p) == 1) {
					self::$currentObject = $p[0];
					self::$currentID = $p->getPermalink();
				} else {
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				}
				break;
			case "Edit":
				if(self::$user === false) {
					self::$requestedAction = "";
					break;
				}
				if(self::$requestedAction == self::$requestedObject) self::$requestedAction = "Posts";
				self::$requestedObject = "User";
				self::$currentID = self::$user->getNickname();
				self::$currentObject = self::$user;
				break;
			case "Contest":
				//modifica o leggi tutti i post di un contest //EDIT E DELETE SOLO ADMIN!!!
				if(self::$requestedAction == "Edit" || self::$requestedAction == "Posts" || self::$requestedAction == "Delete") {	//esempio: /Contest/%contest_id%/Edit
					if($count != 3) self::$requestedAction = "";
					else self::$currentID = $parts[1];
				}
				//crea nuovo contest //SOLO ADMIN!!!
				if($action == "New") {	//esempio: /Contest/New
					if($count != 2)
						self::$requestedAction = "";
				}
				//leggi la scheda del contest
				if($count == 2) {	//esempio: /Contest/%contest_id%
					self::$requestedAction = "Read";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca dei contest
				if($count == 1) {
					self::$requestedAction == "Search";
				}
				break;
			case "Category":
				//modifica o leggi tutti i post di una categoria //EDIT, SETPARENT E DELETE SOLO ADMIN!!!
				if(self::$requestedAction == "Edit" || self::$requestedAction == "Posts" ||
				   self::$requestedAction == "Delete" || self::$requestedAction == "SetParent") {	//esempio: /Category/%category_name%/Posts
					if($count != 3) self::$requestedAction = "";
					else self::$currentID = $parts[1];
				}
				//crea nuova categoria //SOLO ADMIN!!!
				if(self::$requestedAction == "New") {	//esempio: /Category/New
					if($count != 2)
						self::$requestedAction = "";
				}
				//leggi tutti i post di una categoria //E' UNA COPIA DI QUELLA SOPRA...
				if($count == 2) {	//esempio: /Category/%category_name%
					self::$requestedAction = "Posts";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca delle categorie
				if($count == 1) {
					self::$requestedAction == "Search";
				}
				break;
			case "Tag":
				//leggi tutti i post di un tag
				if(self::$requestedAction == "Posts") {	//esempio: /Tag/%tag_name%/Posts
					if($count != 3) $action = "";
					else self::$currentID = $parts[1];
				}
				//leggi tutti i post di un tag //E' UNA COPIA DI QUELLA SOPRA...
				if($count == 2) {	//esempio: /Tag/%tag_name%
					self::$requestedAction = "Posts";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca dei tag
				if($count == 1) {
					self::$requestedAction == "Search";
				}
				break;
			case "Post":
				//modifica, vota, commenta, elimina, subscribe o aggiungi a una collezione il post
				if(self::$requestedAction == "Edit" || self::$requestedAction == "Vote" ||
				   self::$requestedAction == "Comment" || self::$requestedAction == "Delete" ||
				   self::$requestedAction == "Subscribe" || self::$requestedAction == "AddToCollection") {	//esempio: /Post/%author%/%post_date%/%post_title%/Edit
					if($count != 5) self::$requestedAction = "";
				}
				//leggi il post
				if($count == 4) {	//esempio: /Post/%author%/%post_date%/%post_title%/
					self::$requestedAction = "Read";
				}
				//crea nuovo post
				if(self::$requestedAction == "New") {	//esempio: /Post/New
					if($count != 2)
						self::$requestedAction = "";
					break; //non deve fare altro
				}
				//pagina di ricerca dei post
				if($count == 1) {
					self::$requestedAction == "Search";
				} else if(self::$requestedAction != "") { //recupera altre informazioni sul post
					self::$currentID = $parts[0] . "/" . $parts[1] . "/" . $parts[2] . "/" . $parts[3];
				}
				break;
			case "Comment":
				//rimuovi un commento
				if(self::$requestedAction == "Delete") {	//esempio: /Comment/%comment_id%/Remove
					if($count != 3) self::$requestedAction = "";
					else self::$currentID = $parts[1];
				}
				//leggi un commento e relativo post
				if($count == 2) {	//esempio: /Comment/%comment_id%
					self::$requestedAction = "Read";
					self::$currentID = $parts[1];
				} else if($count != 3) self::$requestedAction = "";
				break;
			case "Vote":
				//rimuovi, modifica un voto
				if(self::$requestedAction == "Delete" || self::$requestedAction == "Edit") {	//esempio: /Vote/%post_id%/Remove
					if($count != 3) self::$requestedAction = "";
					else self::$currentID = $parts[1];
				}
				break;
			case "User":
				//modifica, segui, non seguire, commenta, elimina, verifica, leggi tutti i post di un utente
				if(self::$requestedAction == "Edit" || self::$requestedAction == "Follow" ||
				   self::$requestedAction == "Feedback" || self::$requestedAction == "Delete" ||
				   self::$requestedAction == "StopFollow" || self::$requestedAction == "Verify" ||
				   self::$requestedAction == "Posts" || self::$requestedAction == "AddContact" ||
				   self::$requestedAction == "Mails" ) {	//esempio: /User/%user_nickname%/Verify
					if($count != 3) self::$requestedAction = "";
					self::$currentID = $parts[1];
				}
				//registra nuovo utente
				if(self::$requestedAction == "New") {	//esempio: /User/New
					if($count != 2)
						self::$requestedAction = "";
				}
				//leggi il profilo
				if($count == 2) {	//esempio: /User/%user_nickname%
					self::$requestedAction = "Read";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca degli utenti
				if($count == 1) {
					self::$requestedAction == "Search";
				}
				break;
			case "Feedback":
				//rimuovi, modifica un voto
				if(self::$requestedAction == "Delete") {	//esempio: /Feedback/%subject_id%/Remove
					if($count != 3) self::$requestedAction = "";
					else self::$currentID = $parts[1];
				}
				break;
			case "Contact":
				//modifica o elimina un contatto
				if(self::$requestedAction == "Edit" || self::$requestedAction == "Delete") {	//esempio: /Contact/%contact_id%/Edit
					if($count != 3) self::$requestedAction = "";
					else self::$currentID = $parts[1];
				}
				//pagina di ricerca dei contatti
				if($count == 1) {
					self::$requestedAction == "Search";
				}
				break;
			case "Mail":
				//modifica, rispondi, sposta nel cestino o in un'altra cartella o segnala come spam una mail
				if(self::$requestedAction == "Edit" || self::$requestedAction == "Delete" ||
				   self::$requestedAction == "Move" || self::$requestedAction == "Spam" ||
				   self::$requestedAction == "Answer") {	//esempio: /Mail/%mail_id%/Edit
					if($count != 3) self::$requestedAction = "";
					else self::$currentID = $parts[1];
				}
				//crea nuova mail o svuota il cestino
				if(self::$requestedAction == "New" || self::$requestedAction == "EmptyTrash") {	//esempio: /Mail/EmptyTrash
					if($count != 2)
						self::$requestedAction = "";
				}
				//leggi la mail
				if($count == 2) {	//esempio: /Mail/%mail_id%
					self::$requestedAction = "Read";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca delle mail
				if($count == 1) {
					self::$requestedAction == "Search";
				}
				break;
			case "Directory":
				//modifica o elimina o leggi le mail di una directory
				if(self::$requestedAction == "Edit" || self::$requestedAction == "Delete" || self::$requestedAction == "Mails") {	//esempio: /Directory/%dir_id%/Edit
					if($count != 3) self::$requestedAction = "";
					else self::$currentID = $parts[1];
				}
				//crea nuova dir o leggi inviate o non lette
				if(self::$requestedAction == "New" || self::$requestedAction == "Sent" || self::$requestedAction == "Unread") {	//esempio: /Directory/Sent
					if($count != 2)
						self::$requestedAction = "";
				}
				//guarda il contenuto 
				if($count == 2) {	//esempio: /Directory/%dir_id%
					self::$requestedAction = "Mails";
					self::$currentID = $parts[1];
				}
				//pagina di ricerca delle mail nella cartella
				if($count == 1) {
					self::$requestedAction == "Search";
				}
				break;
			case "Partner":
				//TODO
				break;
			default:
				self::$requestedAction = "";
		}
		
		if(self::$requestedAction == "")
			self::$requestedObject = "index";
		//echo "<br />" . serialize($return["object"]); //DEBUG
		return $return;
	}
	
	private static function doAction($request) {
		switch (self::$requestedObject) {
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
				if(self::$requestedAction == "error") {
					require_once 'errors/errors.php';
					showError($_GET["e"]);
				}
				require_once 'search/SearchManager.php';
				$posts = SearchManager::searchBy(array("Post"), array(), array("limit" => 1, "order" => "DESC", "by" => array("ps_creationDate")));
				require_once("post/PostPage.php");
				PostPage::showPost(self::$currentObject = $posts[0], self::$post_options);
				self::$currentID = self::$currentObject->getPermalink();
				break;
		}
	}
	
	private static function doUserAction($request) {
		//echo "<p>" . serialize($request) . "</p>"; //DEBUG
		$user = null;
		if(is_numeric(self::$currentID))
			$user = UserManager::loadUser(self::$currentID);
		else if(isset(self::$currentID))
			$user = UserManager::loadUserByNickname(self::$currentID);
		
		switch (self::$requestedAction) {
			case "Edit":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				require_once 'user/UserPage.php';
				UserPage::showEditProfileForm($user);
				break;
			case "Follow":
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("error.php?e=Oops la pagina non è stata trovata."));
				
				UserManager::followUser(self::$user, $user);
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
				
				UserManager::stopFollowingUser(self::$user, $user);
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
				$mails = MailManager::loadDirectoryFromName(MAILBOX, self::$user);
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
		switch (self::$requestedAction) {
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
		switch (self::$requestedAction) {
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
		switch (self::$requestedAction) {
			case "Edit":
				require_once 'admin/common.php';
				CategoryPage::showEditCategoryForm(self::$currentID);
				break;
			case "Posts":
				//echo "<p><font color='green'>REQUEST TO LOAD post which category is " . $request["categoryname"] . ".</font></p>"; //DEBUG
				require_once 'search/SearchManager.php';
				$posts = SearchManager::searchBy(array("Post"), array("category" => self::$currentID), array("limit" => 4, "order" => "DESC", "by" => array("ps_creationDate")), true);
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
		require_once 'post/PostCommon.php';
		self::$currentObject = Comment::loadFromDatabase(self::$currentID);
		switch (self::$requestedAction) {
			case "Delete":
				$c->delete();
				require_once 'post/PostManager.php';
				$p = PostManager::loadPostByPermalink(self::$currentObject->getPost());
				header("location: " . $p->getFullPermalink());
				break;
			case "Read":
			default:
				require_once 'post/PostManager.php';
				$p = PostManager::loadPostByPermalink(self::$currentObject->getPost());
				header("location: " . $p->getFullPermalink() . "#comment" . self::$currentObject->getID());
				break;
		}
	}
	
	private static function doFeedbackAction($request) {
		switch (self::$requestedAction) {
			case "Delete":
				if(is_numeric(self::$currentID))
					$subject = UserManager::loadUser(self::$currentID);
				else
					$subject = UserManager::loadUserByNickname(self::$currentID);
				if(is_null($user) || $user === false)
					header("location: " . FileManager::appendToRootPath("/error.php?e=Oops la pagina non è stata trovata."));
				UserManager::deleteFeedbackFromUser(self::$user, $subject);
			default:
				header("location: " . FileManager::appendToRootPath("User/" . $user->getID()));
		}
	}
	
	private static function doMailAction($request) {
		require_once 'mail/MailManager.php';
		if(isset(self::$currentID) && self::$currentID != null)
			self::$currentObject = MailManager::loadMail(self::$currentID);
		switch (self::$requestedAction) {
			case "Edit": //una mail non si può modificare...
				break;
			case "Move":
				require_once 'mail/MailPage.php';
				MailPage::showMoveToForm(self::$currentObject);
				break;
			case "Delete":
				$dir = MailManager::directoryForMail(self::$currentObject, self::$user);
				MailManager::moveToTrash(self::$currentObject, $dir);
				header("location: " . FileManager::appendToRootPath("Directory/" . $dir->getID()));
				break;
			case "Spam":
				$dir = MailManager::directoryForMail(self::$currentObject, self::$user);
				MailManager::moveToSpam(self::$currentObject, $dir);
				header("location: " . FileManager::appendToRootPath("Directory/" . $dir->getID()));
				break;
			case "Answer":
				self::$currentObject = MailManager::loadMail(self::$currentID);
			case "New":
				if(!isset(self::$currentObject)) self::$currentObject = null;
				require_once 'mail/MailPage.php';
				MailPage::showNewForm(self::$currentObject);
				break;
			case "EmptyTrash":
				MailManager::emptyTrash(self::$user);
				header("location: " . FileManager::appendToRootPath("User/" . self::$user->getID() . "/Mails"));
				break;
			case "Read":
				require_once 'mail/MailPage.php';
				MailPage::showMail(self::$currentObject);
				break;
			case "Search":
			default:
				require_once 'search/SearchPage.php';
				SearchPage::showMailSearchForm();
				break;
		}
	}
	
	private static function doDirectoryAction($request) {
		require_once 'mail/MailManager.php';
		if(isset(self::$currentID) && self::$currentID != null)
			self::$currentObject = MailManager::loadDirectory(self::$currentID);
		switch (self::$requestedAction) {
			case "Edit":
				require_once 'mail/MailPage.php';
				MailPage::showEditDirectoryForm(self::$currentObject);
				break;
			case "Mails":
				require_once 'mail/MailPage.php';
				foreach(self::$currentObject->getMails() as $mail)
					MailPage::showShortMail($mail);
				break;
			case "Delete":
				MailManager::deleteDirectory(self::$currentObject);
				$inbox = MailManager::loadDirectoryFromName(MAILBOX, self::$currentObject->getUser());
				header("location: " . FileManager::appendToRootPath("Directory/" . $inbox->getID()));
				break;
			case "Sent":
				$mails = MailManager::getMailSent(self::$user);
				foreach($mails->getMails() as $mail)
					MailPage::showShortMail($mail);
				break;
			case "Unread":
				//@deprecated non ce n'è bisogno...
				$inbox = MailManager::loadDirectoryFromName(MAILBOX, self::$user);
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
		switch (self::$requestedAction) {
			case "Delete":
				require_once 'post/PostManager.php';
				$vote = PostManager::loadVote(self::$user, self::$currentID);
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
		switch (self::$requestedAction) {
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
		if(isset(self::$currentID) && self::$currentID != null)
			self::$currentObject = PostManager::loadPostByPermalink(self::$currentID);
		//echo "<p>" . $request["action"] . "</p>"; //DEBUG
		switch(self::$requestedAction) {
			//modifica, vota, commenta, elimina, subscribe o aggiungi a una collezione il post
			case "Read":
				//echo "<p><font color='green'>" . $request["permalink"] . "</font></p>"; //DEBUG
				require_once("post/PostPage.php");
				PostPage::showPost(self::$currentObject, self::$post_options);
				break;
			case "Edit":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				require_once("post/PostPage.php");
				PostPage::showEditPostForm(self::$currentObject);
				break;
			case "Vote":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				require_once("post/PostPage.php");
				//controllo su vote.
				if(isset($_GET["vote"])) {
					if($_GET["vote"] == "y" || $_GET["vote"] == "yes")
						$vote = true;
					if($_GET["vote"] == "n" || $_GET["vote"] == "no")
						$vote = false;
					if(!isset($_GET["vote"])) header("location: " . FileManager::appendToRootPath("error.php?error=Oops, il voto da te inserito non è valido."));
					PostManager::votePost(self::$user->getID(), self::$currentObject, $vote);
				}
				PostPage::showPost(self::$currentObject, self::$post_options);
				break;
			case "Comment":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				require_once("post/PostPage.php");
				PostPage::showComments(self::$currentObject);
				break;
			case "Delete":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				require_once("post/PostPage.php");
				PostManager::deletePost(self::$currentObject);
				header("location: " . FileManager::getServerPath());
				break;
			case "Subscribe":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				require_once("post/PostPage.php");
				PostPage::showContestForm(self::$currentObject);
				break;
			case "AddToCollection":
				//echo "<p><font color='green'>REQUEST TO LOAD " . $request["script"] . " by: " . $author->getNickname() . ", with the title of: " . $request["posttitle"] . ", created the day: " . date("d/m/Y", $request["postday"]) . "</font></p>"; //DEBUG
				require_once("post/PostPage.php");
				PostPage::showCollectionForm(self::$currentObject);
				break;
			case "New":
				require_once("post/PostPage.php");
				PostPage::showNewPostForm();
				break;
			case "Search":
			default:
				require_once("search/SearchPage.php");
				SearchPage::showPostSearchForm();
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
		
		self::$user = Session::getUser();
		$data = self::elaborateRequest($request);
		if(self::$requestedObject == "Login")
			self::redirect("");
		else if(self::$requestedObject == "Logout") {
			Session::destroy();
			self::redirect("");
		}
		
		//return; //DEBUG
		$default = TemplateManager::getDefaultTemplate();
		$parser = null; $tentativi = 0;
		while(is_numeric($parser) || is_null($parser)) {
			$template = TemplateManager::getTemplateForRequest(self::$requestedObject, self::$currentID, self::$requestedAction);
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
		if(self::$requestedObject == "index") {
			self::$post_options[PostPage::NO_DATE] = true;
			self::$post_options[PostPage::NO_COMMENTS] = true;
		}
		
		self::doAction($data);
		
		self::$post_options[PostPage::NO_DATE] = false;
		self::$post_options[PostPage::NO_COMMENTS] = false;
	}
	
	private static function PCComments($data) {
		require_once 'post/PostPage.php';
		PostPage::showComments(self::$currentObject, 2, false);
	}
	
	private static function PCRelated($data) {
		echo "Vedi anche";
		require_once 'search/SearchManager.php';
		$posts = SearchManager::searchBy(array("Post"),
										array("author" => self::$currentObject->getAuthor(), "no_id" => self::$WHO_POST, "loadComments" => false),
										array("limit" => 3, "order" => "DESC", "by" => array("ps_creationDate")));
		$posts2 = SearchManager::searchBy(array("Post"),
										array("tag" => self::$currentObject->getTags(), "category" => self::$currentObject->getCategories(), "no_id" => self::$WHO_POST, "loadComments" => false),
										array("limit" => 3, "order" => "DESC", "by" => array("ps_creationDate")));
		if(is_array($posts2))
			array_merge($posts, $posts2);
		foreach($posts as $post) {
			if($post->getID() != self::$currentObject->getID()) {
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
		$p = PostManager::loadPost(self::$WHO_POST, false);
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
		echo "<p class='title'>Altri articoli</p>";
	}
	
	private static function PCCat($data, $num) { ?>
		<div class="category_splash category_splash_<?php echo $num; ?>">
			<p class="category_splash_name">Category <?php echo $num; ?></p>
			<?php //TODO echo writePosts; ?>
		</div>
<?php
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
		if(isset(self::$currentObject) && !is_null(self::$currentObject) && self::$currentObject !== false) {
			require_once 'user/UserManager.php';
			$user = UserManager::loadUser(self::$currentObject->getAuthor());
			if(true) { //TODO se l'autore vuole
				echo "<p>L'autore</p>";
				require_once 'user/UserPage.php';
				UserPage::showProfile($user);
			}
		}
	}
	
	private static function PCSameAuthor($data) {
		if(isset(self::$currentObject) && !is_null(self::$currentObject) && self::$currentObject !== false) {
			require_once 'user/UserManager.php';
			$user = UserManager::loadUser(self::$currentObject->getAuthor());
			if(true) { //TODO se l'autore vuole
				echo "<p>Dello stesso autore</p>";
				require_once 'search/SearchManager.php';
				$posts = SearchManager::searchBy("Post", array("author" => $user->getID(), "no_id" => self::$currentObject->getID(), "loadComments" => false), array("order" => -1, "by" => "ps_creationDate"));
				
				self::$post_options[PostPage::SHORT] = true;
				self::$post_options[PostPage::NO_COMMENTS] = true;
				self::$post_options[PostPage::NO_MODIF_DATE] = true;
				
				require_once 'post/PostPage.php';
				foreach($posts as $p)
					PostPage::showPost($p, self::$post_options);
					
				self::$post_options[PostPage::SHORT] = false;
				self::$post_options[PostPage::NO_COMMENTS] = false;
				self::$post_options[PostPage::NO_MODIF_DATE] = false;
			}
		}
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