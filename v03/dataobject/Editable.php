<?php
require_once 'dataobject/Writable.php';
abstract class Editable extends Writable {
	const PREVIOUS_VERSION = "previousversion";
	const EDITABLE = "editable";
	const REMOVABLE = "removable";
	protected $previousVersion = null;			// id versione precedente
	protected $editable = true;					// modificabile dall'autore
	protected $removable = true;					// cancellabile dall'autore

	function getPreviousVersion() {
		return $this->previousVersion;
	}
	function isRemovable() {
		return $this->removable;
	}
	function isEditable() {
		return $this->editable;
	}
	
	function setEditable($editable) {
		settype($editable, "boolean");
		$this->editable = $editable;
		return $this;
	}
	function setRemovable($removable) {
		settype($removable, "boolean");
		$this->removable = $removable;
		return $this;
	}
	function setPreviousVersion($history_id) {
		$this->previousVersion = $history_id;
		return $this;
	}
}
?>