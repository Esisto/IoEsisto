<?php

class TagDao {
	
	
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
													   array(new WhereConstraint($table->getColumn(TAG_NAME), Operator::EQUAL, $tag)),
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

?>