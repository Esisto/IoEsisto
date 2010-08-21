<?php
require_once("post/collection/Collection.php");

class Album extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::$ALBUM);
	}
	
	/**
	 * @Override
	 * Controlla inoltre che il post da aggiungere sia un PostType::$PHOTOREPORTAGE.
	 */
	function addPost($photo){
		require_once("post/PostCommon.php");
		if($photo->getType()==PostType::$PHOTOREPORTAGE){
			return parent::addPost($photo->getID());
		}
		return false;
	}
	
}

?>