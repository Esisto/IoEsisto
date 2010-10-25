<?php
class SearchRule {
	var $what;
	var $keys;
	var $options;
	
	public function __construct($what, $keys = array(), $options = array()) {
		if(!is_array($what))
			$what = array($what);
		$this->what = $what;
		
		$this->keys = $keys;
		$this->options = $options;
	}
} 

class SearchManager {
	const ALL = "all_search";
	const EXCEPT = "except";
	const IN = "inner_select";
	const INTERSECT = "intersect";
	const UNION = "union";
	
	static function search($searchRules, $combinationRule = self::ALL, $echo_query = false /*DEBUG*/) {
		
	}
	
	/**
	 * Ricerca oggetti di tipo $what con gli elementi di ricerca $keys e le opzioni $options
	 *
	 * @param $what: array di oggetti da cercare, deve essere un classname. es: "Post", "User", "Contest". Se vuoto cerca ovunque.
	 * @param $keys: array delle chiavi da ricercare per esempio: name, creation interval, ecc…
	 * @param $options: array di opzioni: limit, order, group.
	 */
	static function searchBy($what, $keys, $options, $echo_query = false /*DEBUG*/) {
		if(!is_array($what)) $what = array($what);
		foreach($what as $class) {
			//echo "<p><b>WILL SEARCH " . $class . "</b> WITH KEYS: " . serialize($keys) . " AND OPTIONS: " . serialize($options) . "</p>"; //DEBUG
			if($class == "Post") return self::searchPostsBy($keys, $options);
			else if($class == "User") return self::searchUsersBy($keys, $options);
			else if($class == "Mail") return self::searchMailsBy($keys, $options);
			else if($class == "Contest") return self::searchContestsBy($keys, $options);
			else if($class == "Collection") return self::searchCollectionsBy($keys, $options);
			else if($class == "Directory") return self::searchDirectoriesBy($keys, $options);
			//else if($class == "Contact") return self::searchContactsBy($keys, $options);
		}
	}
	
	private static function searchPostsBy($keys, $options, $echo_query = false /*DEBUG*/) {
		require_once("query.php");
		require_once("strings/strings.php");
		define_tables(); definePostColumns();
		$table = Query::getDBSchema()->getTable(TABLE_POST);
		
		$loadComments = true;
		$wheres = array();
		foreach($keys as $key => $value) {
			if($key == "name" || $key == "title")
				$wheres[] = new WhereConstraint($table->getColumn(POST_TITLE), Operator::LIKE, "%" . Filter::filterText($value) . "%");
			if($key == "permalink")
				$wheres[] = new WhereConstraint($table->getColumn(POST_PERMALINK), Operator::EQUAL, intval($value));
			if($key == "id")
				$wheres[] = new WhereConstraint($table->getColumn(POST_ID), Operator::EQUAL, intval($value));
			if($key == "tag")
				$wheres[] = new WhereConstraint($table->getColumn(POST_TAGS), Operator::LIKE, "%" . Filter::filterText($value) . "%");
			if($key == "day") {
				if(!is_numeric($value))
					$value = date_timestamp_get(date_create_from_format("Y-m-d", $value));
				$daystart = date("Y-m-d",$value);
				$dayend = date("Y-m-d", $value + 24*60*60);
				
				//echo "<br />" . $daystart . "-" . $dayend; //DEBUG
				$wheres[] = new WhereConstraint($table->getColumn(POST_CREATION_DATE), Operator::GREATEROREQUAL, $daystart);
				$wheres[] = new WhereConstraint($table->getColumn(POST_CREATION_DATE), Operator::LESSER, $dayend);
			}
			if($key == "category")
				$wheres[] = new WhereConstraint($table->getColumn(POST_CATEGORIES), Operator::LIKE, "%" . Filter::filterText($value) . "%");
			if($key == "title")
				$wheres[] = new WhereConstraint($table->getColumn(POST_TITLE), Operator::LIKE, "%" . Filter::filterText($value) . "%");
			if($key == "content")
				$wheres[] = new WhereConstraint($table->getColumn(POST_CONTENT), Operator::LIKE, "%" . Filter::filterText($value) . "%");
			if($key == "author")
				$wheres[] = new WhereConstraint($table->getColumn(POST_AUTHOR), Operator::EQUAL, intval($value));
			if($key == "no_id")
				$wheres[] = new WhereConstraint($table->getColumn(POST_ID), Operator::NOTEQUAL, intval($value));
			if($key == "loadComments")
				$loadComments = $value == true;
		}
		$newopt = array();
		foreach($options as $key => $value) {
			if($key == "by") {
				if(!is_array($value)) $value = array($value);
				
				$newvalue = array();
				foreach($value as $column) {
					if(!is_a($column, "Column"))
						$column = $table->getColumn($column);
					if(!is_null($column))
						$newvalue[] = $column;
				}
				$value = $newvalue;
			}
			$newopt[$key] = $value;
		}
		
		$db = new DBManager();
		$db->execute($s = Query::generateSelectStm(array($table), array(), $wheres, $newopt));
		
		if($echo_query) echo "<font color='red'>" . $s . "</font>"; //DEBUG
		$posts = array();
		while($row = $db->fetch_result()) {
			require_once("post/Post.php");
			$posts[] = Post::createFromDBResult($row, $loadComments);
		}
		
		return $posts;
	}
	
	private static function searchUsersBy($keys, $options, $echo_query = false /*DEBUG*/) {} //TODO
	private static function searchMailsBy($keys, $options, $echo_query = false /*DEBUG*/) {} //TODO
	private static function searchContestsBy($keys, $options, $echo_query = false /*DEBUG*/) {} //TODO
	private static function searchCollectionsBy($keys, $options, $echo_query = false /*DEBUG*/) {} //TODO
	private static function searchDirectoriesBy($keys, $options, $echo_query = false /*DEBUG*/) {} //TODO
}


?>