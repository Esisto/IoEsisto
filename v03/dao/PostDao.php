<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Post.php");

class PostDao extends Dao {
	const OBJECT_CLASS = "Post";
	private $loadReports = false;
	private $loadComments = true;
	
	const DEFAULT_CATEGORY = "News";
	
	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_POST);
	}
	
	function setLoadReports($load) {
		settype($load, "boolean");
		$this->loadReports = $load;
		return $this;
	}
	function setLoadComments($load) {
		settype($load, "boolean");
		$this->loadComments = $load;
		return $this;
	}
	
	function load($id) {
		parent::load($id);
		$rs = $db->execute($s = Query::generateSelectStm(array($this->table),
														 array(),
														 array(new WhereConstraint($this->table->getColumn(DB::POST_ID),Operator::EQUAL,intval($id))),
														 array()));
		
		if($db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $db->fetch_result();
		$p = $this->createFromDBRow($row);
		return $p;
	}
	
	function quickLoad($id) {
		$loadC = $this->loadComments; $this->loadComments = false;
		$loadR = $this->loadReports; $this->loadReports = false;
		$p = null;
		try {
			$p = $this->load($id);
			$this->loadComments = $loadC;
			$this->loadReports = $loadR;
		} catch(Exception $e) {
			$this->loadComments = $loadC;
			$this->loadReports = $loadR;
			throw $e;
		}
		return $p;
	}
	
	function loadByPermalink($permalink) {
		parent::load($permalink);
		$rs = $db->execute($s = Query::generateSelectStm(array($this->table),
														 array(),
														 array(new WhereConstraint($this->table->getColumn(DB::POST_PERMALINK),Operator::EQUAL,$permalink)),
														 array()),
							$this->table->getName(), null);
		
		if($db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $db->fetch_result();
		$p = $this->createFromDBRow($row);
		return $p;
	}
	
	function createFromDBRow($row) {
		$type = $row[DB::POST_TYPE];
		if($type == Post::NEWS || $type == Post::VIDEOREP)
			$content = $row[DB::POST_CONTENT];
		else
			$content = unserialize($row[DB::POST_CONTENT]);
		$data = array("title" => $row[DB::POST_TITLE],
					  "subtitle" => $row[DB::POST_SUBTITLE],
					  "headline" => $row[DB::POST_HEADLINE],
					  "author"=> intval($row[DB::POST_AUTHOR]),
					  "tags" => $row[DB::POST_TAGS],
					  "categories" => $row[DB::POST_CATEGORIES],
					  "content" => $content,
					  "visible" => $row[DB::POST_VISIBLE] > 0,
					  "type" => $type,
					  "place" => $row[DB::POST_PLACE]);
		if($type == PostType::NEWS) {
			require_once("dataobject/News");
			$p = new News($data);
		} else if($type == Post::VIDEOREP) {
			require_once("dataobject/VideoReportage.php");
			$p = new VideoReportage($data);
		} else if($type == Post::ALBUM) {
			require_once("dataobject/Album.php");
			$p = new Album($data);
		} else if($type == Post::MAGAZINE) {
			require_once("dataobject/Magazine.php");
			$p = new Magazine($data);
		} else if($type == Post::PHOTOREP) {
			require_once("dataobject/PhotoReportage.php");
			$p = new PhotoReportage($data);
		} else if($type == Post::PLAYLIST) {
			require_once("dataobject/Playlist.php");
			$p = new Playlist($data);
		} else if($type == Post::COLLECTION) {
			require_once("dataobject/Collection.php");
			$p = new Collection($data);
		} else
			throw new Exception("Errore!!! Il tipo inserito non esiste!");
		
		$p->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::POST_CREATION_DATE])));
		$p->setID(intval($row[DB::POST_ID]));
		if(!is_null($row[DB::POST_MODIFICATION_DATE]))
			$p->setModificationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::POST_MODIFICATION_DATE])));
		else $p->setModificationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::POST_CREATION_DATE])));
		if($this->loadComments) {
			require_once 'dao/CommentDao.php';
			$commentDao = new CommentDao();
			$commentDao->loadAll($p);
		}
		$p->setPermalink($row[DB::POST_PERMALINK]);
		$voteDao = new VoteDao();
		$p->setVote($voteDao->getVote($p));
		//setto lo stato
		$p->setEditable($row[DB::EDITABLE])->setRemovable($row[DB::REMOVABLE]);
		$p->setBlackContent($row[DB::BLACK_CONTENT])->setRedContent($row[DB::RED_CONTENT])
				->setYellowContent($row[DB::YELLOW_CONTENT])->setAutoBlackContent($row[DB::AUTO_BLACK_CONTENT]);
		
		$user = Session::getUser();
		if($this->loadReports && $user->isEditor()) { //FIXME usa authorizationManager o roleManager
			require_once 'dao/ReportDao.php';
			$reportDao = new ReportDao();
			$reportDao->loadAll($p);
		}
		$p->setAccessCount($this->getAccessCount($post));
		return $p;
	}

	function permalinkExists($permalink) {
		parent::load($permalink);
		$rs = $db->execute($s = Query::generateSelectStm(array($this->table), array(),
														 array(new WhereConstraint($this->table->getColumn(DB::POST_PERMALINK),Operator::EQUAL,$permalink)),
														 array("count" => 2)));
		if($db->num_rows() != 1)
			throw new Exception("Si è verificato un errore. Riprovare.");
		
		$row = $db->fetch_row();
		return $row[0] > 0;
	}
	
	function exists($post) {
		try {
			$p = $this->load($post->getID());
			return is_subclass_of($p, self::OBJECT_CLASS);
		} catch(Exception $e) {
			return false;
		}
	}
	
	function save($post) {
		parent::save($post, self::OBJECT_CLASS);
		
		$data = array(DB::POST_TYPE => $post->getType());
		if(!is_null($post->getTitle()))
			$data[DB::POST_TITLE] = $post->getTitle();
		if(!is_null($post->getSubtitle()))
			$data[DB::POST_SUBTITLE] = $post->getSubtitle();
		if(!is_null($post->getHeadline()))
			$data[DB::POST_HEADLINE] = $post->getHeadline();
		if(!is_null($post->getTags()))
			$data[DB::POST_TAGS] = $post->getTags();
		if(!is_null($post->getCategories())) {
			// check sulle categorie, eliminazione di quelle che non esistono nel sistema, se vuoto inserimento di quella di default
			$new_cat = CategoryManager::filterWrongCategories(explode(",", $post->getCategories()));
			if(is_null($post->getCategories()) || count($new_cat) == 0)
				$new_cat[] = self::DEFAULT_CATEGORY;
			$post->setCategories(Filter::arrayToText($new_cat));
			$data[DB::POST_CATEGORIES] = $post->getCategories();
		}
		if(isset($post->content) && !is_null($post->getContent())) {
			if($post->type == Post::NEWS || $post->type == Post::VIDEOREP)
				$data[DB::POST_CONTENT] = $post->getContent();
			else
				$data[DB::POST_CONTENT] = serialize($post->getContent());
		}
		if(!is_null($post->isVisible()))
			$data[DB::POST_VISIBLE] = $post->isVisible() ? 1 : 0;
		if(!is_null($post->getAuthor()))
			$data[DB::POST_AUTHOR] = $post->getAuthor();
		if(!is_null($post->getPlace()))
			$data[DB::POST_PLACE] = $post->getPlace();
		if(is_null($post->getPermalink()));
			$post->setPermalink($this->generatePermalink($post));
		$data[DB::POST_PERMALINK] = $post->getPermalink();
		$rand = ""; $count = 0;
		while(!$this->permalinkExists($post->getPermalink() . $rand)) {
			if($count >= 1000) throw new Exception("Attenzione! Hai troppi atricol che si chiamano in questo modo. Prova a cambiare titolo.");
			//finché esiste già un permalink del genere, ne genero uno random.
			$rand = "(" . rand(65535, $_SERVER["REQUEST_TIME"]) . ")";
			$data[DB::POST_PERMALINK] = $post->getPermalink() . $rand;
			$count++;
		}
		$data[DB::POST_CREATION_DATE] = date("Y-m-d G:i:s", $_SERVER["REQUEST_TIME"]);
		
		$rs = $this->db->execute($s = Query::generateInsertStm($this->table,$data), $this->table->getName(), $this);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		//carico il post inserito.
		$p = $this->load(intval($this->db->last_inserted_id()));
		//salvo i tag che non esistono
		if(isset($data[DB::POST_TAGS]) && !is_null($data[DB::POST_TAGS]) && trim($data[DB::POST_TAGS]) != "")
			TagManager::createTags(explode(",", $data[DB::POST_TAGS]));
			
		//TODO salvo lo stato
		return $p;
	}
	
	function update($post, $editor) {
		parent::update($post, $editor, self::OBJECT_CLASS);
		if(!is_a($editor, "User"))
			throw new Exception("Non hai settato chi ha fatto la modifica.");
		
		$p_old = $this->quickLoad($post->getID());
	
		$data = array();
		if(is_null($p_old))
			throw new Exception("L'oggetto da modificare non esiste.");
		//cerco le differenze e le salvo.
		if($p_old->getTitle() != $post->getTitle())
			$data[DB::POST_TITLE] = $post->getTitle();
		if($p_old->getSubtitle() != $post->getSubtitle())
			$data[DB::POST_SUBTITLE] = $post->getSubtitle();
		if($p_old->getHeadline() != $post->getHeadline())
			$data[DB::POST_HEADLINE] = $post->getHeadline();
		if($p_old->getContent() != $post->getContent()) {
			if($post->type == Post::NEWS || $post->type == Post::VIDEOREP)
				$data[DB::POST_CONTENT] = $post->getContent();
			else
				$data[DB::POST_CONTENT] = serialize($post->getContent());
		}
		if($p_old->getPlace() != $post->getPlace())
			$data[DB::POST_PLACE] = $post->getPlace();
		if($p_old->getPlaceName() != $post->getPlaceName())
			$data[DB::POST_PLACE_NAME] = $post->getPlaceName();
		if($p_old->getTags() != $post->getTags())
			$data[DB::POST_TAGS] = $post->getTags();
		if($p_old->getCategories() != $post->getCategories()) {
			// check sulle categorie, eliminazione di quelle che non esistono nel sistema, se vuoto inserimento di quella di default
			$new_cat = CategoryManager::filterWrongCategories(explode(",", $post->getCategories())); //TODO
			if(count($new_cat) == 0)
				$new_cat[] = self::DEFAULT_CATEGORY;
			$post->setCategories(Filter::arrayToText($new_cat));
			$data[DB::POST_CATEGORIES] = $post->getCategories();
		}
		if($p_old->isVisible() !== $post->isVisible())
			$data[DB::POST_VISIBLE] = $post->isVisible() ? 1 : 0;
		if($p_old->getPermalink() != $post->getPermalink()) {
			if($this->permalinkExists($post->getPermalink())) throw new Exception("Il permalink inserito esiste già. Riprova.");
			$data[DB::POST_PERMALINK] = $post->getPermalink();
		}
			
		if(count($data) == 0) throw new Exception("Nessuna modifica da effettuare.");
		$modDate = $_SERVER["REQUEST_TIME"];
		$data[DB::POST_MODIFICATION_DATE] = date("Y/m/d G:i:s", $modDate); // se mi dicono di fare l'update, cambio modificationDate
		
		//salvo la versione precedente e ne tengo traccia.
		$history_id = $this->saveHistory($p_old, "UPDATED");
		$post->setPreviousVersion($history_id);
		$data[DB::POST_PREVIOUS_VERSION] = $post->getPreviousVersion();
		
		$rs = $this->db->execute($s = Query::generateUpdateStm($this->table, $data,
									array(new WhereConstraint($this->table->getColumn(DB::POST_ID),Operator::EQUAL,$post->getID()))),
									$this->table->getName(), $post);
		//aggiorno lo stato del post (se chi l'ha modificato è un redattore).
		if($editor->isEditor()) { //TODO usa authorization manager
			//FIXME controlla che ci sia tutto
			$post->setEditable(false);
			$post->setRemovable(false);
			$this->updateState($post);
		}
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
		//salvo i tag che non esistono
		if(isset($data[DB::POST_TAGS]) && !is_null($data[DB::POST_TAGS]) && trim($data[DB::POST_TAGS]) != "")
			TagManager::createTags(explode(",", $data[DB::POST_TAGS])); //TODO
		
		return $post->setModificationDate($modDate);
	}
	
	function delete($post) {
		parent::delete($post, self::OBJECT_CLASS);
		
		//carico il post, completo dei suoi derivati (che andrebbero persi) esclusi i voti.
		$loadC = $this->loadComments; $this->loadComments = true;
		$loadR = $this->loadReports; $this->loadReports = true;
		$p_complete = null;
		try {
			$p_complete = $this->load($post->getID());
			$this->loadComments = $loadC;
			$this->loadReports = $loadR;
		} catch(Exception $e) {
			$this->loadComments = $loadC;
			$this->loadReports = $loadR;
			throw $e;
		}
		
		$this->db->execute($s = Query::generateDeleteStm($this->table,
								array(new WhereConstraint($this->table->getColumn(DB::POST_ID),Operator::EQUAL,$post->getID()))),
						  		$this->table->getName(), $post);
		
		//salvo il post nella storia.
		$this->saveHistory($p_complete, "DELETED");
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore eliminando il dato. Riprovare.");
		return $post;
	}
	
	private function generatePermalink($post) {
		require_once("common.php");
		$s = "Post/";
		$s.= Filter::textToPermalink($this->getAuthorName($post));
		$s.= "/";
		if(isset($post->creationDate)) {
			$s.= date("Y-m-d", $post->getCreationDate());
			$s.= "/";
		}
		$s.= Filter::textToPermalink($post->getTitle());
		return $s;
	}
	
	function getAuthorName($post) {
		require_once("user/UserManager.php");
		if(is_null($post->getAuthor()))
			return "Anonimous";
		$u = UserManager::loadUser($post->getAuthor(), false); //TODO
		if(!is_null($u->getNickname()))
			return $u->getNickname();
		return $post->getAuthor();
	}
	
	function updateState($post) {
		//TODO
		//check se lo stato è uguale:
		// - isRemovable
		// - contentColor
		
		//se lo stato è diverso si aggiornano sul db solo i campi di stato.
	}
	
	private function getAccessCount($post) {
		//TODO
		//aggiunge 1 all'accesscount e aggiorna il db.
		//restituisce il conto.
		//questo fino all'arrivo di googleanalitics
	}
}

?>