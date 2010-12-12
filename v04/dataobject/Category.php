<?php
class Category {
	private $name;
	private $children;
	private $parent_name;
	private $author_id;
	private $creationDate;
	private $accessCount = 1;			// numero di accessi
	
	function __construct($name, $parent_name = null) {
		$this->name = $name;
		$this->setParentName($parent_name);
	}

	function getParentName() {
		return $this->parent_name;
	}
	function getName() {
		return $this->name;
	}
	function getChildren() {
		if(!isset($this->children) || is_null($this->children))
			return array();
		return $this->children;
	}
	function getAuthorId() {
		return $this->author_id;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function getAccessCount() {
		return $this->accessoCount;
	}
	
	function setParentName($parent_name) {
		$this->parent_name = $parent_name;
		return $this;
	}
	function setChildren($children) {
		if(is_null($children))
			$children = array();
		if(!is_array($children))
			$children = array($children);
		$this->children = $children;
		return $this;
	}
	function setAuthorId($author_id) {
		$this->author_id = $author_id;
		return $this;
	}
	function setCreationDate($date) {
		$this->creationDate = $date;
		return $this;
	}
	function setAccessCount($accessCount) {
		if(is_numeric($accessCount))
			$this->accessCount = intval($accessCount);
		return $this;
	}
}
?>