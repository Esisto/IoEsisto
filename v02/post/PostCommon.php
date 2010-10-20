<?php

class PostType {
	static $PHOTOREPORTAGE = "photoreportage";
	static $VIDEOREPORTAGE = "videoreportage";
	static $NEWS = "news";
	static $COLLECTION = "collection";
	static $ALBUM = "album";
	static $MAGAZINE = "magazine";
	static $PLAYLIST = "playlist";
}


class CategoryManager {
	static function categoryExists($category) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			require_once("strings/strings.php");
			define_tables(); defineCategoryColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CATEGORY);
			
			$db->execute($s = Query::generateSelectStm(array($table),
													   array(),
													   array(new WhereConstraint($table->getColumn(CATEGORY_NAME), Operator::$EQUAL, $category)),
													   array()));
			if($db->num_rows() == 1)
				return true;
			else
				return false;
		} else $db->display_connect_error("CategoryManager::categoryExists()");
		return false;
	}
	
	/**
	 * Toglie da un array i nomi delle categorie che non esistono.
	 * @param array $categories array di stringhe di nomi di categorie.
	 * @return array contenente i nomi delle categorie che esistono.
	 */
	static function filterWrongCategories($categories) {
		if(!isset($categories) || is_null($categories)) return array();
		if(!is_array($categories)) $categories = array($categories);
		
		$new_categories = array();
		foreach ($categories as $cat) {
			if(self::categoryExists($cat))
				$new_categories[] = $cat;
		}
		return $new_categories;
	}
	
	static function createCategoriesFromArray($array, $parent = null) {
		if(!is_array($array)) {
			if(self::categoryExists($array)) return;
			if(self::createCategoryWithParent($array, $parent))
				$created++;
		} else {
			foreach($array as $index => $value) {
				if(!is_numeric($index)) {
					self::createCategoriesFromArray($index, $parent);
					self::createCategoriesFromArray($value, $index);
				} else {
					self::createCategoriesFromArray($value);
				}
			}
		};
	}
	
	static function createCategory($name) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineCategoryColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CATEGORY);
			
			$db->execute($s = Query::generateInsertStm($table, array(CATEGORY_NAME => $name)));
			if($db->num_rows() == 1)
				return true;
			else
				return false;
		} else $db->display_connect_error("CategoryManager::createCategory()");
		return false;
	}
	
	static function createCategoryWithParent($category, $parent) {
		$ret = self::createCategory($category);
		
		if(!is_null($parent)) {
			require_once("query.php");
			$db = new DBManager();
			if(!$db->connect_errno()) {
				define_tables(); defineSubCategoryColumns();
				$table = Query::getDBSchema()->getTable(TABLE_SUB_CATEGORY);
				
				$db->execute($s = Query::generateInsertStm($table, array(SUB_CATEGORY_PARENT => $parent, SUB_CATEGORY_CATEGORY => $category)));
				if($db->num_rows() == 1)
					return true;
				else
					return $ret || false;
			} else $db->display_connect_error("CategoryManager::createCategoryWithParent()");
			return $ret || false;
		}
		return $ret;
	}
}

class TagManager {
	
	/**
	 * Controlla l'esistenza di un tag
	 * @param string $tag il nome di un tag.
	 * @return TRUE se il tag esiste già nel sistema, FALSE altrimenti.
	 */
	static function tagExists($tag) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineTagColumns();
			$table = Query::getDBSchema()->getTable(TABLE_TAG);
			$data = array(TAG_NAME => $tag);
			
			$db->execute($s = Query::generateSelectStm(array($table),
													   array(),
													   array(new WhereConstraint($table->getColumn(TAG_NAME), Operator::$EQUAL, $tag)),
													   array()));
			if($db->num_rows() == 1)
				return true;
			else
				return false;
		} else $db->display_connect_error("TagManager::tagExists()");
		return false;
	}
	
	/**
	 * Crea i tag presenti in un array, se il tag esiste già non dà problemi.
	 * @param array $tags un array di nomi di tag
	 */
	static function createTags($tags) {
		if(!isset($tags) || is_null($tags)) return;
		if(!is_array($tags)) $tags = array($tags);
		
		foreach($tags as $tag) {
			self::createTag(trim($tag));
		}
	}
	
	/**
	 * Crea un tag.
	 * @param string $tag
	 * @return TRUE se il tag è stato creato, FALSE altrimenti.
	 */
	static function createTag($tag) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineTagColumns();
			$table = Query::getDBSchema()->getTable(TABLE_TAG);
			$data = array(TAG_NAME => $tag);
			
			$db->execute($s = Query::generateInsertStm($table, $data), $table->getName(), $tag);
			if($db->affected_rows() == 1) {
				return true;
			} else {
				return false;
			}
		} else $db->display_connect_error("TagManager::createTag()");
	}
}

class Comment {
	private $ID;
	private $author;
	private $post;
	private $comment;
	private $creationDate;
	private $reports;
	
	
	function __construct($data){
		$this->author = $data["author"];
		$this->post = $data["post"];
		$this->comment = $data["comment"];
	}
	
	function getAuthor() {
		return $this->author;
	}
	
	function getPost() {
		return $this->post;
	}
	
	function getComment() {
		return $this->comment;
	}
	
	function getCreationDate() {
		return $this->creationDate;
	}
	
	function getID() {
		return $this->ID;
	}
	
	function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
		return $this;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	
	/**
	 * @deprecated
	 */
	function loadReports() {}
	
	/**
	 * Salva il commento nel database.
	 * 
	 * @param savingMode: uno dei valori della classe SavingMode.
	 * se INSERT: crea una nuova tupla in Post.
	 * se UPDATE: confronta il Post con quello presente nel database e aggiorna le differenze.
	 */
	function save() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineCommentColumns();
			$table = Query::getDBSchema()->getTable(TABLE_COMMENT);
			$data = array(COMMENT_COMMENT => $this->getComment(),
						  COMMENT_POST => $this->getPost(),
						  COMMENT_AUTHOR => $this->getAuthor());
			$rs = $db->execute($s = Query::generateInsertStm($table,$data),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			if($db->affected_rows() == 1) {
				$this->ID = $db->last_inserted_id();
				//echo "<br />" . serialize($this->ID); //DEBUG
				$rs = $db->execute($s = Query::generateSelectStm(array($table),
															 array(),
															 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::$EQUAL,$this->getID())),
															 array()),
								  $table->getName(), $this);
				//echo "<br />" . $s; //DEBUG
				if($db->num_rows() == 1) {
					$row = $db->fetch_result();
					$this->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[COMMENT_CREATION_DATE])));
					//echo "<br />" . serialize($row[COMMENT_CREATION_DATE]); //DEBUG
					//echo "<br />" . $this; //DEBUG
					return $this->ID;
				} else $db->display_error("Comment::save()");
			} else $db->display_error("Comment::save()");
		} else $db->display_connect_error("Comment::save()");
		return false;
	}
	
	function delete() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineCommentColumns();
			$table = Query::getDBSchema()->getTable(TABLE_COMMENT);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::$EQUAL,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Comment::delete()");
		} else $db->display_connect_error("Comment::delete()");
		return false;
	}
	
	/**
	 * Crea un commento caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Comment().
	 *
	 * @param $id: l'ID del commento da caricare.
	 * @return: il commento caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($id) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_COMMENT);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(COMMENT_ID),Operator::$EQUAL,$id)),
														 array()),
							  $table->getName(), null);
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				$data = array("comment" => $row[COMMENT_COMMENT],
							  "author" => intval($row[COMMENT_AUTHOR]),
							  "post" => intval($row[COMMENT_POST]));
				$c = new Comment($data);
				$c->setID(intval($row[COMMENT_ID]));
				$c->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[COMMENT_CREATION_DATE])));
				//$c->loadReports();
				return $c;
			} else $db->display_error("Comment::loadFromDatabase()");
		} else $db->display_connect_error("Comment::loadFromDatabase()");
		return false;
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "Comment (ID = " . $this->ID .
			 " | author = " . $this->author .
			 " | post = " . $this->post .
			 " | comment = " . $this->comment .
			 " | creationDate = " . date("d/m/Y G:i:s", $this->creationDate) .
			 " | reports = (";
		for($i=0; $i<count($this->reports); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->reports[$i];
		}
		$s.= "))";
		return $s;
	}
}

class Vote {
	private $author;
	private $post;
	private $vote;
	private $creationDate;
	
	function __construct($author,$post,$vote){
		$this->author = $author;
		$this->post = $post;
		$this->vote = $vote;
	}
	
	function getAuthor() {
		return $this->author;
	}
	
	function getPost() {
		return $this->post;
	}
	
	function getVote() {
		return $this->vote;
	}
	
	function getCreationDate() {
		return $this->vote;
	}
	
	function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
		return $this;
	}
	
	/**
	 * Salva il voto nel database.
	 * 
	 * @param savingMode: uno dei valori della classe SavingMode.
	 * se INSERT: crea una nuova tupla in Post.
	 * se UPDATE: confronta il Post con quello presente nel database e aggiorna le differenze.
	 */
	function save() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineVoteColumns();
			$table = Query::getDBSchema()->getTable(TABLE_VOTE);
			$data = array(VOTE_VOTE => $this->getVote(),
						  VOTE_POST => $this->getPost(),
						  VOTE_AUTHOR => $this->getAuthor());
			$db->execute($s = Query::generateInsertStm($table,$data),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			if($db->affected_rows() == 1) {
				$db->execute($s = Query::generateSelectStm(array($table),
															 array(),
															 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$EQUAL,$this->getAuthor()),
																   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$EQUAL,$this->getPost())),
															 array()),
								  $table->getName(), $this);
				//echo "<br />" . $s; //DEBUG
				if($db->num_rows() == 1) {
					$row = $db->fetch_result();
					$this->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[VOTE_CREATION_DATE])));
					//echo "<br />" . serialize($row[VOTE_CREATION_DATE]); //DEBUG
					//echo "<br />" . $this; //DEBUG
					return $this->creationDate;
				} else $db->display_error("Vote::save()");
			} else $db->display_error("Vote::save()");
		} else $db->display_connect_error("Vote::save()");
		return false;
	}
		
	function update() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_VOTE);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$EQUAL,$this->getAuthor()),
															   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$EQUAL,$this->getPost())),
														 array()),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			$data = array();
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				//cerco le differenze e le salvo.
				if($row[VOTE_VOTE] != $this->getVote())
					$data[VOTE_VOTE] = $this->getVote();
				break;
				//echo "<br />" . serialize($data); //DEBUG
				
				$rs = $db->execute($s = Query::generateUpdateStm($table,
															 $data,
															 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$EQUAL,$this->getAuthor()),
																   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$EQUAL,$this->getPost()))),
								  $table->getName(), $this);
				//echo "<br />" . $s; //DEBUG
				//echo "<br />" . $rs; //DEBUG
				if($db->affected_rows() == 1) {
					//echo "<br />" . $this; //DEBUG
					return $this;
				} else $db->display_error("Vote::update()");
			} else $db->display_error("Vote::update()");
		} else $db->display_connect_error("Vote::update()");
		return false;
	}
	
	function delete() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineVoteColumns();
			$table = Query::getDBSchema()->getTable(TABLE_VOTE);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$EQUAL,$this->getAuthor()),
															   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$EQUAL,$this->getPost()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Vote::delete()");
		} else $db->display_connect_error("Vote::delete()");
		return false;			
	}
	
	/**
	 * Crea un voto caricando i dati dal database.
	 * È come fare una ricerca sul database e poi fare new Vote().
	 *
	 * @param $id: l'ID del voto da caricare.
	 * @return: il voto caricato o FALSE se non lo trova.
	 */
	static function loadFromDatabase($author, $post) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_VOTE);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(VOTE_AUTHOR),Operator::$EQUAL,$author),
															   new WhereConstraint($table->getColumn(VOTE_POST),Operator::$EQUAL,$post)),
														 array()),
							  $table->getName(), null);
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				$v = new Vote(intval($row[VOTE_AUTHOR]), intval($row[VOTE_POST]), $row[VOTE_VOTE] > 0);
				$v->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[VOTE_CREATION_DATE])));
				return $v;
			} else $db->display_error("Vote::loadFromDatabase()");
		} else $db->display_connect_error("Vote::LoadFromDatabase()");
		return false;
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "Vote (author = " . $this->author .
			 " | post = " . $this->post .
			 " | vote = " . $this->vote .
			 ")";
		return $s;
	}
}

?>