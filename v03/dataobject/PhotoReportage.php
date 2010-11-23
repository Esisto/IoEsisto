<?php
require_once("dataobject/Collection.php");

class PhotoReportage extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setType(Post::PHOTOREP);
	}
	
	/**
	 * @Override
	 */
	function setContent($content) {
		if(!is_array($content))
			$content = array($content);
		
		require_once("dataobject/Resource.php");
		foreach ($content as $resource)
			if(!is_subclass_of($resource, "Resource") || $content->getType() != Resource::PHOTO)
				throw new Exception("ERRORE!!! Non stai inserendo delle Foto.");

		$this->content = $content;
		return $this;
	}

	/**
	 * Aggiunge una foto al contenuto.
	 *
	 * @param $photo: la foto da aggiungere come oggetti di tipo Resource.
	 */
	function addPhoto($photo) {
		if(is_null($post))
			throw new Exception("Attenzione! Non hai selezionato nessuna Foto.");	
		require_once("dataobject/Resource.php");
		if(!is_subclass_of($content,"Resouce") || $content->getType() != Resource::PHOTO)
			throw new Exception("ERRORE!!! Non stai aggiungendo una Foto.");
		
		if(isset($this->content) && is_array($this->content))
			$this->content[] = $post;
		else
			$this->setContent(array($post));
		return $this;
	}
	
	function addPost($post) {
		return $this->addPhoto($post);
	}
}
?>