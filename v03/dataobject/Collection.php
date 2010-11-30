<?php
require_once("dataobject/Post.php");

class Collection extends Post {
	
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setType(Post::COLLECTION);
	}
	
	function addPost($post){
		if(is_null($post))
			throw new Exception("Attenzione! Non hai selezionato nessun Post.");
		if(!is_subclass_of($post, "Post"))
			throw new Exception("ERRORE!!! Non stai aggiungendo un Post.");
			
		if(isset($this->content) && is_array($this->content))
			$this->content[] = $post;
		else
			$this->setContent(array($post));
		return $this;
	}
	
	/**
	 * @Override
	 */
	function setContent($content) {
		if(!is_array($content))
			$content = array($content);
		
		foreach ($content as $post)
			if(!is_subclass_of($post, "Post"))
				throw new Exception("ERRORE!!! Non stai aggiungendo dei Post");
		$this->content = $content;
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "<font color='" . $this->getContentColor() . "'>Post (ID = " . $this->getID() .
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
		$s.= "))</font>";
		return $s;
	}
}
?>