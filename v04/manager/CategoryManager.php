<?php
require_once 'dao/CategoryDao.php';
require_once 'dataobject/Category.php';

class CategoryManager {
	
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
			$catdao = new CategoryDao();
			if($catdao->exists(trim($cat)))
				$new_categories[] = trim($cat);
		}
		return $new_categories;
	}
	
	static function createCategoriesFromArray($array, $author_id, $parent = null) {
		if(!is_array($array)) {
			echo "<p>Trying to create category " . $array . "</p>";
			$catdao = new CategoryDao();
			if($catdao->exists($array)) return;
			
			$cat = new Category($array, $parent);
			$cat->setAuthorId($author_id);
			$cat->setCreationDate(time());
			
			$catdao->save($cat);
			echo "<p style='color:green;'>Created category " . $array . "</p>";
		} else {
			foreach($array as $index => $value) {
				if(!is_numeric($index)) {
					self::createCategoriesFromArray($index, $author_id, $parent);
					self::createCategoriesFromArray($value, $author_id, $index);
				} else {
					self::createCategoriesFromArray($value, $author_id, $parent);
				}
			}
		};
	}
	
	static function loadAllCategories() {
		$catdao = new CategoryDao();
		return $catdao->loadAllNew();
	}
}	
?>