<?php
class Report {
	private $ID;
	private $author_id;
	private $object;
	private $report;
	
	function __construct($author_id, $object, $report) {
		$this->author_id = $author_id;
		$this->object = $object;
		$this->report = $report;
	}
	
	function getAuthorID() {
		return $this->author_id;
	}
	function getObject() {
		return $this->object;
	}
	function getReport() {
		return $this->report;
	}
	function getID() {
		return $this->ID;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
}
?>