<?php
require_once 'dataobject/Editable.php';

class Comment extends Editable {
	private $ID;
	private $author;
	private $post;
	private $comment;
	private $creationDate;
	private $reports;
	
	private $removable = true;				// cancellabile dall'autore
	private $blackContent = false;			// bollino nero: un redattore ha 'censurato' il commento
	private $autoBlackContent = false;		// bollino nero automatico: il commento ha superato le TOT segnalazioni
	
	function __construct($comment, $post, $author) {
		$this->author = $author;
		$this->post = $post;
		$this->comment = $comment;
	}
	
	function getAuthor() {
		return $this->author;
	}
	function getPost() {
		return $this->post;
	}
	function getComment() {
		return $this->comment;
	}
	function getCreationDate() {
		return $this->creationDate;
	}
	function getID() {
		return $this->ID;
	}
	function getReports() {
		return $this->reports;
	}
	/** @Override */
	function getPreviousVersion() {
		return null;
	}
	/** @Override */
	function isEditable() {
		return false;
	}
	/** @Override */
	function hasYellowContent() {
		return false;
	}
	/** @Override */
	function hasRedContent() {
		return false;
	}
	
	function setCreationDate($creationDate) {
		$this->creationDate = $creationDate;
		return $this;
	}
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	function setReports($reports) {
		if(!is_array($reports) && !is_null($reports)) $reports = array($reports);
		$this->reports = $reports;
		return $this;
	}
	function setYellowContent($yellowContent) {
		return $this;
	}
	function setRedContent($redContent) {
		return $this;
	}
	function setEditable($editable) {
		return $this;
	}
	function setPreviousVersion($history_id) {
		return $this;
	}

	/**
	 * @Override
	 */
	function __toString() {
		$s = "Comment (ID = " . $this->ID .
			 " | author = " . $this->author .
			 " | post = " . $this->post .
			 " | comment = " . $this->comment .
			 " | creationDate = " . date("d/m/Y G:i:s", $this->creationDate) .
			 " | reports = (";
		for($i=0; $i<count($this->reports); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->reports[$i];
		}
		$s.= "))";
		return $s;
	}
}
?>