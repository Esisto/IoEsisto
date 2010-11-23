<?php
require_once 'dataobject/Editable.php';

class Resource extends Editable {
	private $ID;
	private $owner;
	private $path;
	private $type;
	private $accessCount = 1;			// numero di accessi
	
	const VIDEO = "video";
	const PHOTO = "photo";
	
	function __construct($owner, $path, $type) {
		$this->owner = $owner;
		$this->path = $path;
		$this->type = $type;
	}
	
	function getOwner() {
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
	function getAccessCount() {
		return $this->accessCount;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	function setAccessCount($accessCount) {
		if(is_numeric($accessCount))
			$this->accessCount = intval($accessCount);
		return $this;
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "Resource (ID = " . $this->getID() .
			 " | owner = " . $this->getOwner() .
			 " | path = " . $this->getPath() .
			 " | type = " . $this->getType() .
			 ")";
		return $s;
	}
}
?>