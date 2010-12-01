<?php
require_once("post/Post.php");

class Collection extends Post {
	
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		// DEBUG
		if(isset($data["content"])) {
			if(!is_array($data["content"]))
				$data["content"] = array($data["content"]);
			$this->setContent($data["content"]);
		} else {
			$this->setContent(array());
		}
		// END DEBUG
		require_once("post/PostCommon.php");
		$this->setType(PostType::COLLECTION);
	}
	
	function addPost($post){
		if(isset($this->content) && is_array($this->content)) {
			$this->content[] = $post;
		} else {
			parent::setContent(array($post));
		}
		
		$this->update();
		return $this;
	}
	
	/**
	 * @Override
	 */
	function setContent($content) {
		if(!is_array($content))
			$content = array($content);
		//TODO Check che il contenuto sia di tipo Post.
		$this->content = $content;
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "Post (ID = " . $this->getID() .
			 " | postType = " . $this->getType() .
			 " | title = " . $this->getTitle() .
			 " | subtitle = " . $this->getSubtitle() .
			 " | headline = " . $this->getHeadline() .
			 " | author = " . $this->getAuthor() .
			 " | creationDate = " . date("d/m/Y G:i", $this->getCreationDate()) .
			 " | modificationDate = " . date("d/m/Y G:i", $this->getModificationDate()) .
			 " | tags = (" . $this->tags .
			 ") | categories = (" . $this->categories .
			 ") | comments = (";
		for($i=0; $i<count($this->getComments()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->comments[$i];
		}
		$s.= ") | votes = (";
		for($i=0; $i<count($this->getVotes()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->votes[$i];
		}
		$s.= ") | content = (";
		for($i=0; $i<count($this->getContent()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->content[$i];
		}
		$vis = $this->isVisible() ? "true" : "false";
		$s.= ") | visible = " . $vis .
			 " | signals = (";
		for($i=0; $i<count($this->getReports()); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->reports[$i];
		}
		$s.= "))";
		return $s;
	}
}

class Album extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::ALBUM);
	}
	
	/**
	 * @Override
	 * Controlla inoltre che il post da aggiungere sia un PostType::PHOTOREPORTAGE.
	 */
	function addPost($photo){
		require_once("post/PostCommon.php");
		if($photo->getType()==PostType::PHOTOREPORTAGE){
			return parent::addPost($photo->getID());
		}
		return false;
	}
}

class Magazine extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::MAGAZINE);
	}
	
	/**
	 * @Override
	 * Controlla inoltre che il post da aggiungere sia un PostType::NEWS.
	 */
	function addPost($news){
		require_once("post/PostCommon.php");
		if($news->getType()==PostType::NEWS){
			return parent::addPost($news->getID());
		}
		return false;
	}
}

class PhotoReportage extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::PHOTOREPORTAGE);
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
	 * @param $photo: la foto da aggiungere come oggetti di tipo Resource.
	 */
	function addPhoto($photo) {
		require_once("common.php");
		if($photo->getType()==ResourceType::$PHOTO){
			return parent::addPost($photo->getID());
		}
		return false;
	}
}

class Playlist extends Collection {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::PLAYLIST);
	}
	
	/**
	 * @Override
	 * Controlla inoltre che il post da aggiungere sia un PostType::VIDEOREPORTAGE.
	 */
	function addPost($video){
		require_once("post/PostCommon.php");
		if($video->getType()==PostType::VIDEOREPORTAGE){
			return parent::addPost($video->getID());
		}
		return false;
	}
}
?>