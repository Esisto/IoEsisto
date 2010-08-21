<?php
require_once("post/collection/Collection.php");

class Playlist extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::$PLAYLIST);
	}
	
	/**
	 * @Override
	 * Controlla inoltre che il post da aggiungere sia un PostType::$VIDEOREPORTAGE.
	 */
	function addPost($video){
		require_once("post/PostCommon.php");
		if($video->getType()==PostType::$VIDEOREPORTAGE){
			return parent::addPost($video->getID());
		}
		return false;
	}
}

?>