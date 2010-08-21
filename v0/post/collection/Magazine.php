<?php
require_once("post/collection/Collection.php");

class Magazine extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::$MAGAZINE);
	}
	
	/**
	 * @Override
	 * Controlla inoltre che il post da aggiungere sia un PostType::$NEWS.
	 */
	function addPost($news){
		require_once("post/PostCommon.php");
		if($news->getType()==PostType::$NEWS){
			return parent::addPost($news->getID());
		}
		return false;
	}
}

?>