<?php

require_once 'post/PostCommon.php';

class AdminCategoryManager extends CategoryManager {
	static $POSTDELETE = "PostDelete";
	static $POSTUPDATE = "PostUpdate";
	static $CHILDRENDELETE = "ChildrenDelete";
	static $CHILDRENUPDATE = "ChildrenUpdate";
	
	static function editCategory($categoryid, $newname, $parent) {
		//TODO da implementare
	}
	
	static function renameCategory($categoryid, $newname) {
		return self::editCategory($categoryid, $newname, null);
	}
	
	static function setParent($categoryid, $newparent) {
		return self::editCategory($categoryid, null, $newparent);
	}
	
	static function createCategory($name, $parent) {
		//TODO da implementare
	}
	
	/**
	 * Elimina una categoria dal database ed esegue le operazioni richieste da options
	 * @param string $categoryid
	 * @param array $options un array associativo con le seguenti opzioni:
	 * PostDelete => y se cancellare tutti i post di quella categoria altrimenti esegue il PostUpdate default_category.
	 * PostUpdate => nome_categoria, per spostare tutti i post che rimarrebbero senza categoria in nome_categoria, se non definito vengono spostate in default_category.
	 * ChildrenDelete => y per cancellare tutte le sottocategorie, altrimenti esegue il ChildrenUpdate default_category.
	 * ChildrenUpdate => nome_categoria, per spostare tutte le sottocategorie in nome_categoria, se non definito vengono spostate in default_category.
	 * se vuoto, i post che rimarrebbero senza categoria e le sottocategorie vengono spostati in default_category.
	 */
	static function deleteCategory($categoryid, $options = null) {
		//TODO da implementare
	}
}

class CategoryPage {
	static function showEditCategoryForm($categoryid = null) {
		//TODO da implementare
	}
	
	static function showNewCategoryForm() {
		self::showEditCategoryForm();
	}
}

require_once 'post/contest/ContestManager.php';
class AdminContestManager extends ContestManager {
	static function createContest($data) {
		require_once("common.php");
		$data = Filter::filterArray($data);
		
		$c = new Contest($data);
		$c->save();
		return $c;		
	}
	
	static function editContest($contest, $data) {
		require_once("common.php");
		$data = Filter::filterArray($data);
		
		if(isset($data["title"]))
			$contest->setTitle($data["title"]);
		if(isset($data["description"]))
			$contest->setDescription($data["description"]);
		if(isset($data["rules"]))
			$contest->setRules($data["rules"]);
		if(isset($data["prizes"]))
			$contest->setPrizes($data["prizes"]);
		if(isset($data["start"]))
			$contest->setStart($data["start"]);
		if(isset($data["end"]))
			$contest->setEnd($data["end"]);
			
		$contest->update();
		
		return $contest;
	}
	
	static function deleteContest($contest) {
		return $contest->delete();
	}
}

class ContestPage {
	static function showEditContestForm($contestid = null) {
		//TODO da implementare
	}
	
	static function showNewContestForm() {
		self::showEditContestForm();
	}
}

?>