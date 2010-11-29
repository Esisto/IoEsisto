<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");

class PostDao implements Dao {
	private $loadReports = false;
	private $loadComments = true;
	private $db;
	private $table_post;
	
	const DEFAULT_CATEGORY = "News";
	
	function __construct() {
		$this->table_post = Query::getDBSchema()->getTable(DB::TABLE_POST);
		
		$this->db = new DBManager();
		if($this->db->connect_errno())
			$this->db->display_connect_error("PostDao::__construct()");
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
		if(is_null($id)) throw new Exception("Attenzione! Non hai inserito un id.");
		
		if($this->db->connect_errno())
			throw new Exception("Si è verificato un errore di connessione. Aggiornare la pagina e riprovare.");
		
		$rs = $db->execute($s = Query::generateSelectStm(array($this->table_post),
														 array(),
														 array(new WhereConstraint($this->table_post->getColumn(DB::POST_ID),Operator::EQUAL,intval($id))),
														 array()),
							$this->table_post->getName(), null);
		
		if($db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $db->fetch_result();
		$p = $this->createFromDBResult($row);
		return $p;
	}
	
	function loadByPermalink($permalink) {
		if(is_null($id)) throw new Exception("Attenzione! Non hai inserito un permalink.");
		
		if($this->db->connect_errno())
			throw new Exception("Si è verificato un errore di connessione. Aggiornare la pagina e riprovare.");
		$rs = $db->execute($s = Query::generateSelectStm(array($this->table_post),
														 array(),
														 array(new WhereConstraint($this->table_post->getColumn(DB::POST_PERMALINK),Operator::EQUAL,$permalink)),
														 array()),
							$this->table_post->getName(), null);
			
		//echo "<p>" . $s . "</p>"; //DEBUG
		//echo "<p>" . $db->num_rows() . "</p>"; //DEBUG
		if($db->num_rows() == 1) {
			//echo serialize($db->fetch_result()); //DEBUG
			$row = $db->fetch_result();
			$p = $this->createFromDBResult($row);
			//echo "<p>" .$p ."</p>";
			return $p;
		} else $db->display_error("PostDao::loadByPermalink()");
		throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
	}
	
	function createFromDBResult($row) {
		require_once("dataobject/Post.php");
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
		$post->setVote(VoteDao::getVote($post));
		
		$user = Session::getUser();
		if($this->loadReports && $user->getRole() == "admin") {
			require_once 'dao/ReportDao.php';
			$reportDao = new ReportDao();
			$reportDao->loadAll($p);
		}
		
		$p->setPermalink($row[DB::POST_PERMALINK]);
		
		require_once("common.php");
		$p->setAccessCount(LogManager::getAccessCount("Post", $p->getID())); //TODO modificare LogManager
		
		return $p;
	}

	function permalinkExists($permalink) {
		if($this->db->connect_errno())
			throw new Exception("Si è verificato un errore di connessione. Aggiornare la pagina e riprovare.");
		
		$rs = $db->execute($s = Query::generateSelectStm(array($this->table_post), array(),
														 array(new WhereConstraint($this->table_post->getColumn(DB::POST_PERMALINK),Operator::EQUAL,$permalink)),
														 array("count" => 2)));
		if($db->num_rows() == 1) {
			$row = $db->fetch_result();
			return $row["COUNT(*)"] > 0;
		} else $db->display_error("Post::permalinkExists()");
		
		return false;
	}
	
	function exists($post) {
		$loadC = $this->loadComments; $this->loadComments = false;
		$loadR = $this->loadReports; $this->loadReports = true;
		
		$result = false;
		try {
			$p = $this->load($post->getID());
			$result = is_subclass_of($p, "Post");
		} catch(Exception $e) {
			$result = false;
		}
		$this->loadComments = $loadC;
		$this->loadReports = $loadR;
		return $result;
	}
	
	function save($post) {
		if($this->db->connect_errno())
			throw new Exception("Si è verificato un errore di connessione. Aggiornare la pagina e riprovare.");

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
		$data[DB::POST_PERMALINK] = $post->getPermalink();
		$rand = "";
		while(!$this->permalinkExists($post->getPermalink() . $rand)) {
			//finché esiste già un permalink del genere, ne genero uno radfom.
			$rand = "(" . rand(65535, $_SERVER["REQUEST_TIME"]) . ")";
			$data[DB::POST_PERMALINK] = $post->getPermalink() . $rand;
		}
		$data[DB::POST_CREATION_DATE] = date("Y-m-d G:i:s", $_SERVER["REQUEST_TIME"]);
		
		$rs = $this->db->execute($s = Query::generateInsertStm($this->table_post,$data), $this->table_post->getName(), $this);
		if($this->db->affected_rows() == 1) {
			$post->setID($this->db->last_inserted_id());
			$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table_post),
														 array(),
														 array(new WhereConstraint($this->table_post->getColumn(DB::POST_ID),Operator::EQUAL,$this->getID())),
														 array()),
							  $this->table_post->getName(), $this);
			
			if($this->db->num_rows() == 1) {
				$row = $this->db->fetch_result();
				$post->setPermalink($row[DB::POST_PERMALINK]);
				$post->setModificationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::POST_CREATION_DATE])));
				
				//salvo i tag che non esistono
				if(isset($data[DB::POST_TAGS]) && !is_null($data[DB::POST_TAGS]) && trim($data[DB::POST_TAGS]) != "")
					TagManager::createTags(explode(",", $data[DB::POST_TAGS]));
				
				return $post->getID();
			}
		}
		throw new Exception("Si è verificato un errore salvando il dato. Riprovare.");
	}
	
	function update($post) {
		if(!$post->isEditable())
			throw new Exception("Il post non può essere modificato perché è stato iscritto ad un contest o è sotto revisione di un redattore.");
		
		if($this->db->connect_errno())
			throw new Exception("Si è verificato un errore di connessione. Aggiornare la pagina e riprovare.");
		
		//carico il post per trovare le differenze.
		$loadC = $this->loadComments; $this->loadComments = false;
		$loadR = $this->loadReports; $this->loadReports = true;
		$p_old = $this->load($post->getID());
		$this->loadComments = $loadC;
		$this->loadReports = $loadR;
	
		$data = array();
		if($p_old != null) {
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
			$data[DB::POST_MODIFICATION_DATE] = date("Y/m/d G:i:s", $_SERVER["REQUEST_TIME"]); // se mi dicono di fare l'update, cambio modificationDate
			
			//TODO salvare $p_old nella storia e inserire previous version in $post.
			
			$rs = $this->db->execute($s = Query::generateUpdateStm($this->table_post,
														 $data,
														 array(new WhereConstraint($this->table_post->getColumn(DB::POST_ID),Operator::EQUAL,$this->getID()))),
							  $this->table_post->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . mysql_affected_rows(); //DEBUG
			if($this->db->affected_rows() == 1) {
				//salvo i tag che non esistono
				if(isset($data[DB::POST_TAGS]) && !is_null($data[DB::POST_TAGS]) && trim($data[DB::POST_TAGS]) != "")
					TagManager::createTags(explode(",", $data[DB::POST_TAGS])); //TODO
				
				//echo "<br />" . $this; //DEBUG
				return $post->getModificationDate();
			}
		}
		throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
	}
	
	function delete($post) {
		if(!$post->isRemovable())
			throw new Exception("Il post non può essere eliminato perché è stato iscritto ad un contest o è sotto revisione di un redattore.");
		
		if($this->db->connect_errno())
			throw new Exception("Si è verificato un errore di connessione. Aggiornare la pagina e riprovare.");
		$this->db->execute($s = Query::generateDeleteStm($this->table_post,
													 	array(new WhereConstraint($this->table_post->getColumn(DB::POST_ID),Operator::EQUAL,$post->getID()))),
						  $this->table_post->getName(), $this);
						  
		if($this->db->affected_rows() == 1)
			return $post;
		throw new Exception("Si è verificato un errore eliminando il dato. Riprovare.");
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
}

?>