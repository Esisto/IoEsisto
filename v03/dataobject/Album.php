<?php
require_once("dataobject/Collection.php");
require_once("dataobject/Post.php");

class Album extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setType(Post::ALBUM);
	}
	
	/**
	 * @Override
	 * Controlla inoltre che il post da aggiungere sia un PostType::PHOTOREPORTAGE.
	 */
	function addPost($photo){
		if(is_null($post))
			throw new Exception("Attenzione! Non hai selezionato nessun Post.");
		require_once("dataobject/PhotoReportage.php");
		require_once("dataobject/Post.php");
		if(!is_a($post, "PhotoReportage") || (is_subclass_of($post, "Post") && $video->getType()!=Post::PHOTOREP))
			throw new Exception("ERRORE!!! Non stai aggiungendo un FotoReportage.");
		
		if(isset($this->content) && is_array($this->content))
			$this->content[] = $post;
		else
			$this->setContent(array($post));
		return false;
	}
}?>