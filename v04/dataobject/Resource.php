<?php
require_once 'dataobject/Editable.php';

class Resource extends Editable {
	private $ID;
	private $owner;
	private $path;
	private $description;
	private $tags;
	private $creationDate;
	private $modificationDate;
	private $type;
	private $reports;
	private $accessCount = 1;			// numero di accessi
	
	const VIDEO = "video";
	const PHOTO = "photo";
	const DESC = 'description';
	const TAGS = 'tags';
	
	function __construct($owner, $path, $type) {
		$this->owner = $owner;
		$this->path = $path;
		$this->type = $type;
	}
	
	function getOwnerId() {
		return $this->owner;
	}
	function getPath() {
		return $this->path;
	}
	function getType() {
		return $this->type;
	}
	function getID() {
		return $this->ID;
	}
	function getDescription() {
		return $this->description;
	}
	function getTags() {
		return $this->tags;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function getModificationDate() {
		return $this->modificationDate;
	}
	function getAccessCount() {
		return $this->accessCount;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	function setDescription($description) {
		$this->description = $description;
		return $this;
	}
	function setTags($tags) {
		$this->tags = $tags;
		return $this;
	}
	function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
		return $this;
	}
	function setModificationDate($modificationDate) {
		$this->modificationDate = $modificationDate;
		return $this;
	}
	function setAccessCount($accessCount) {
		if(is_numeric($accessCount))
			$this->accessCount = intval($accessCount);
		return $this;
	}
	
	function edit($data){
		if(isset($data[Resource::DESC]))
			$this->setDescription($data[Resource::DESC]);
		if(isset($data[Resource::TAGS]))
			$this->setTags($data[Resource::TAGS]);
		return $this;
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "<font color='" . $this->getContentColor() . "'>Resource (ID = " . $this->getID() .
			 " | description = " . $this->getDescription() .
			 " | tags = (" . $this->getTags() .
			 ") | owner = " . $this->getOwnerId() .
			 " | path = " . $this->getPath() .
			 " | type = " . $this->getType() .
			 ")</font>";
		return $s;
	}
}
?>