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
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table),
														 array(),
														 array(new WhereConstraint($this->table->getColumn(DB::POST_ID),Operator::EQUAL,intval($id))),
														 array()));
		
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $this->db->fetch_result();
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
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table),
														 array(),
														 array(new WhereConstraint($this->table->getColumn(DB::POST_PERMALINK),Operator::EQUAL,$permalink)),
														 array()),
							$this->table->getName(), null);
		
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $this->db->fetch_result();
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
		if($type == Post::NEWS) {
			require_once("dataobject/News.php");
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
		require_once 'dao/VoteDao.php';
		$voteDao = new VoteDao();
		$p->setVote($voteDao->getVote($p));
		//setto lo stato
		$p->setEditable($row[DB::EDITABLE])->setRemovable($row[DB::REMOVABLE]);
		$p->setBlackContent($row[DB::BLACK_CONTENT])->setRedContent($row[DB::RED_CONTENT])
				->setYellowContent($row[DB::YELLOW_CONTENT])->setAutoBlackContent($row[DB::AUTO_BLACK_CONTENT]);
		
		$user = Session::getUser();
		if($this->loadReports && AuthorizationManager::canUserDo(AuthorizationManager::READ_REPORTS, $r)) {
			require_once 'dao/ReportDao.php';
			$reportDao = new ReportDao();
			$reportDao->loadAll($p);
		}
		$p->setAccessCount($this->getAccessCount($p));
		return $p;
	}

	function permalinkExists($permalink) {
		parent::load($permalink);
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table), array(),
														 array(new WhereConstraint($this->table->getColumn(DB::POST_PERMALINK),Operator::EQUAL,$permalink)),
														 array("count" => 2)));
		if($this->db->num_rows() != 1)
			throw new Exception("Si è verificato un errore. Riprovare.");
		
		$row = $this->db->fetch_row();
		return $row[0] > 0;
	}
	
	function exists($post) {
		try {
			$p = $this->quickLoad($post->getID());
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
			require_once 'manager/CategoryManager.php';
			$new_cat = CategoryManager::filterWrongCategories(explode(",", $post->getCategories()));
			if(is_null($post->getCategories()) || count($new_cat) == 0)
				$new_cat[] = self::DEFAULT_CATEGORY;
			$post->setCategories(Filter::arrayToText($new_cat));
			$data[DB::POST_CATEGORIES] = $post->getCategories();
		}
		if(!is_null($post->getContent())) {
			if($post->getType() == Post::NEWS || $post->getType() == Post::VIDEOREP)
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
		$post->setCreationDate($_SERVER["REQUEST_TIME"]);
		if(is_null($post->getPermalink()));
			$post->setPermalink($this->generatePermalink($post));
		$data[DB::POST_PERMALINK] = $post->getPermalink();
		$rand = ""; $count = 0;
		while($this->permalinkExists($post->getPermalink() . $rand)) {
			if($count >= 1000) throw new Exception("Attenzione! Hai troppi atricol che si chiamano in questo modo. Prova a cambiare titolo.");
			//finché esiste già un permalink del genere, ne genero uno random.
			$rand = "(" . rand(65535, $_SERVER["REQUEST_TIME"]) . ")";
			$data[DB::POST_PERMALINK] = $post->getPermalink() . $rand;
			$count++;
		}
		$data[DB::POST_CREATION_DATE] = date("Y-m-d G:i:s", $post->getCreationDate());
		
		$rs = $this->db->execute($s = Query::generateInsertStm($this->table,$data), $this->table->getName(), $this);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		
		//carico il post inserito.
		$p = $this->load(intval($this->db->last_inserted_id()));
		
		//salvo i tag che non esistono
		if(isset($data[DB::POST_TAGS]) && !is_null($data[DB::POST_TAGS]) && trim($data[DB::POST_TAGS]) != "") {
			require_once 'manager/TagManager.php';
			TagManager::createTags(explode(",", $data[DB::POST_TAGS]));
		}
		
		//salvo lo stato del post perché l'utente potrebbe aver già modificato il suo "colore".
		$this->updateState($p);
		return $p;
	}
	
	function update($post, $editor) {
		parent::update($post, $editor, self::OBJECT_CLASS);
		if(!AuthorizationManager::canUserDo(DB::EDIT_POST, $object))
			throw new Exception("L'utente non è autorizzato ad effettuare questa operazione.");
		
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
			require_once 'manager/CategoryManager.php';
			$new_cat = CategoryManager::filterWrongCategories(explode(",", $post->getCategories()));
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
		if(AuthenticationManager::isEditor($editor)) {
			$post->setEditable(false);
			$post->setRemovable(false);
			$this->updateState($post);
		}
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
		
		//salvo i tag che non esistono
		if(isset($data[DB::POST_TAGS]) && !is_null($data[DB::POST_TAGS]) && trim($data[DB::POST_TAGS]) != "") {
			require_once 'manager/TagManager.php';
			TagManager::createTags(explode(",", $data[DB::POST_TAGS])); //TODO
		}
		
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
		require_once("filter.php");
		$s = "Post/";
		$s.= Filter::textToPermalink($this->getAuthorName($post));
		$s.= "/";
		$s.= date("Y-m-d", $post->getCreationDate());
		$s.= "/";
		$s.= Filter::textToPermalink($post->getTitle());
		return $s;
	}
	
	function getAuthorName($post) {
		require_once("dao/UserDao.php");
		if(is_null($post->getAuthor()))
			return "Anonimous";
		$userdao = new UserDao();
		$userdao->setLoadDependences(false);
		$userdao->setLoadAccessCount(false);
		$u = $userdao->load($post->getAuthor());
		if(!is_null($u->getNickname()))
			return $u->getNickname();
		return $post->getAuthor();
	}

	function updateState($post) {
		parent::updateState($post, $this->table, DB::POST_ID);
	}
	
	
	protected function getAccessCount($post) {
		parent::getAccessCount($post, $this->table, DB::POST_ID);
	}
}

?>