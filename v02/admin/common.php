<?php

require_once 'post/PostCommon.php';

class AdminCategoryManager {
	static $POSTDELETE = "PostDelete";
	static $POSTUPDATE = "PostUpdate";
	static $CHILDRENDELETE = "ChildrenDelete";
	static $CHILDRENUPDATE = "ChildrenUpdate";
	
	static function editCategory($categoryid, $newname, $parent) {
		//TODO
	}
	
	static function renameCategory($categoryid, $newname) {
		return self::editCategory($categoryid, $newname, null);
	}
	
	static function setParent($categoryid, $newparent) {
		return self::editCategory($categoryid, null, $newparent);
	}
	
	static function createCategory($name, $parent) {
		//TODO
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
		//TODO
	}
}

class CategoryPage {
	static function showEditCategoryForm($categoryid = null) {
		//TODO
	}
	
	static function showNewCategoryForm() {
		self::showEditCategoryForm();
	}
}

?>