<?php
require_once("dataobject/Collection.php");
require_once("dataobject/Post.php");

class Playlist extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setType(Post::PLAYLIST);
	}
	
	/**
	 * @Override
	 * Controlla inoltre che il post da aggiungere sia un PostType::VIDEOREPORTAGE.
	 */
	function addPost($video){
		if(is_null($post))
			throw new Exception("Attenzione! Non hai selezionato nessun Post.");
		require_once("dataobject/VideoReportage.php");
		require_once("dataobject/Post.php");
		if(!is_a($post, "VideoReportage") || (is_subclass_of($post, "Post") && $video->getType()!=Post::VIDEOREP))
			throw new Exception("ERRORE!!! Non stai aggiungendo un VideoReportage.");
		
		if(isset($this->content) && is_array($this->content))
			$this->content[] = $post;
		else
			$this->setContent(array($post));
		return false;
	}
}
?>