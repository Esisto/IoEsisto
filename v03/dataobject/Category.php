<?php //TODO
class Category {
	private $name;
	private $children;
	private $parent;
	private $author;
	private $creationDate;
	private $accessCount = 1;			// numero di accessi
	
	function __construct($name, $parent = null) {
		$this->name = $name;
		$this->setParent($parent);
	}

	function getParent() {
		return $this->parent;
	}
	function getName() {
		return $this->name;
	}
	function getChildren() {
		if(!isset($this->children) || is_null($this->children))
			return array();
		return $this->children;
	}
	function getAuthor() {
		return $this->author;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function getAccessCount() {
		return $this->accessoCount;
	}
	
	function setParent($parent) {
		$this->parent = $parent;
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
	function setAuthor($author) {
		$this->author = $author;
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