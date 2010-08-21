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
		$this->setType(PostType::$COLLECTION);
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

?>