<?php
class Report {
	private $ID;
	private $author;
	private $post;
	private $report;
	
	function __construct($author, $post, $report) {
		$this->author = $author;
		$this->post = $post;
		$this->report = $report;
	}
	
	function getAuthor() {
		return $this->author;
	}
	function getPost() {
		return $this->post;
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