<?php
require_once 'dao/Dao.php';

class PostDao implements Dao {
	const REPORTS = "reports";
	const COMMENTS = "comments";
	const NO_REPORTS = "no_reports";
	const NO_COMMENTS = "no_comments";
	
	const DEFAULT_CATEGORY = "News";
	
	static function load($id, $options) {
		if(is_null($id)) throw new Exception("Attenzione! Non hai inserito un id.");
		
		require_once("db.php");
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$s = "SELECT * FROM " . DB::TABLE_POST . " WHERE " . DB::POST_ID . " = " . intval($id);
			$rs = $db->execute($s, $table->getName(), null);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			//echo "<p>" . $db->num_rows() . "</p>"; //DEBUG
			if($db->num_rows() == 1) {
				//echo serialize($db->fetch_result()); //DEBUG
				$row = $db->fetch_result();
				$p = self::createFromDBResult($row, $options);
				//echo "<p>" .$p ."</p>";
				return $p;
			} else $db->display_error("Post::loadFromDatabase()");
		} else $db->display_connect_error("Post::loadFromDatabase()");
		return false;
	}
	
	static function createFromDBResult($row, $options) {
		if(is_null($options) || !is_array($options) || count($options) == 0)
			$options = array(self::NO_REPORTS, self::COMMENTS);
		
		require_once("db.php");
		require_once("dataobject/Post.php");
		$type = $row[DB::POST_TYPE];
		if($type == POST::NEWS || $type == POST::VIDEOREP)
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
		if(array_search(self::COMMENTS) !== false) self::loadComments($p); //TODO
		
		$user = Session::getUser();
		if(array_search(self::REPORTS) !== false && $user !== false && $user->getRole() == "admin")
			self::loadReports($p); //TODO
		
		$p->setPermalink($row[DB::POST_PERMALINK]);
		
		require_once("common.php");
		$p->setAccessCount(LogManager::getAccessCount("Post", $p->getID())); //TODO modificare LogManager
		
		return $p;
	}
}

?>