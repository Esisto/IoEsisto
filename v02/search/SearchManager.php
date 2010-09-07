<?php

class SearchManager {
	/**
	 * Ricerca oggetti di tipo $what con gli elementi di ricerca $keys e le opzioni $options
	 *
	 * param $what: array di oggetti da cercare, deve essere un classname. es: "Post", "User", "Contest". Se vuoto cerca ovunque.
	 * param $keys: array delle chiavi da ricercare per esempio: name, creation interval, ecc…
	 * param $options: array di opzioni: limit, order, group.
	 */
	static function searchBy($what, $keys, $options) {
		foreach($what as $class) {
			//echo "<p><b>WILL SEARCH " . $class . "</b> WITH KEYS: " . serialize($keys) . " AND OPTIONS: " . serialize($options) . "</p>"; //DEBUG
			if($class == "Post") return self::searchPostsBy($keys, $options);
			// if user… if contest…
		}
	}
	
	private static function searchPostsBy($keys, $options) {
		require_once("query.php");
		require_once("strings/strings.php");
		define_tables(); definePostColumns();
		$table = Query::getDBSchema()->getTable(TABLE_POST);
		$wheres = array();
		foreach($keys as $key => $value) {
			if($key == "name" || $key == "title")
				$wheres[] = new WhereConstraint($table->getColumn(POST_TITLE), Operator::$LIKE, "%" . Filter::filterText($value) . "%");
			if($key == "permalink")
				$wheres[] = new WhereConstraint($table->getColumn(POST_PERMALINK), Operator::$UGUALE, $value);
			if($key == "id")
				$wheres[] = new WhereConstraint($table->getColumn(POST_ID), Operator::$UGUALE, $value);
			if($key == "tag")
				$wheres[] = new WhereConstraint($table->getColumn(POST_TAGS), Operator::$LIKE, "%" . Filter::filterText($value) . "%");
			if($key == "day") {
				if(!is_numeric($value))
					$value = date_timestamp_get(date_create_from_format("Y-m-d", $value));
				$daystart = date("Y-m-d",$value);
				$dayend = date("Y-m-d", $value + 24*60*60);
				
				//echo "<br />" . $daystart . "-" . $dayend; //DEBUG
				$wheres[] = new WhereConstraint($table->getColumn(POST_CREATION_DATE), Operator::$MAGGIOREUGUALE, $daystart);
				$wheres[] = new WhereConstraint($table->getColumn(POST_CREATION_DATE), Operator::$MINORE, $dayend);
			}
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
		
		//echo "<font color='red'>" . $s . "</font>"; //DEBUG
		$posts = array();
		while($row = $db->fetch_result()) {
			require_once("post/Post.php");
			$posts[] = Post::createFromDBResult($row);
		}
		
		return $posts;
	}
	
}


?>