<?php
require_once("post/collecion/Collection.php");

class PhotoReportage extends Collection {
	
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::$PHOTOREPORTAGE);
	}
	
	/**
	 * @Override
	 */
	function setContent($content) {
		if(!is_array($content))
			$content = array($content);
		//TODO check if are Resources.
		$this->content = $content;
		return $this;
	}

	/**
	 * Aggiunge una foto al contenuto.
	 *
	 * param $photo: la foto da aggiungere come oggetti di tipo Resource.
	 */
	function addPhoto($photo) {
		require_once("common.php");
		if($photo->getType()==ResourceType::$PHOTO){
			return parent::addPost($photo->getID());
		}
		return false;
	}
}

?>