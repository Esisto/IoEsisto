<?php
abstract class Writable {
	const BLACK_CONTENT = "blackcontent";
	const YELLOW_CONTENT = "yellowcontent";
	const RED_CONTENT = "redcontent";
	const AUTO_BLACK_CONTENT = "autoblackcontent";
	protected $redContent = false;				// bollino rosso: contenuti non adatti ai minori
	protected $yellowContent = false;			// bollino giallo: contenuti offensivi
	protected $blackContent = false;			// bollino nero: un redattore ha 'censurato' la risorsa
	protected $autoBlackContent = false;			// bollino nero automatico: la risorsa ha superato le TOT segnalazioni
	
	function hasBlackContent() {
		return $this->blackContent || $this->autoBlackContent;
	}
	function hasAutoBlackContent() {
		return $this->autoBlackContent;
	}
	function hasYellowContent() {
		return $this->yellowContent;
	}
	function hasRedContent() {
		return $this->redContent;
	}
	function getContentColor() {
		require_once 'settings.php';
		if($this->hasBlackContent())
			return HIDE;
		if($this->hasRedContent())
			return VM18;
		if($this->hasYellowContent())
			return OFFENSE;
		return GOOD;
	}
	
	function setBlackContent($blackContent) {
		settype($blackContent, "boolean");
		$this->blackContent = $blackContent;
		return $this;
	}
	function setYellowContent($yellowContent) {
		settype($yellowContent, "boolean");
		$this->yellowContent = $yellowContent;
		return $this;
	}
	function setRedContent($redContent) {
		settype($redContent, "boolean");
		$this->redContent = $redContent;
		return $this;
	}
	function setAutoBlackContent($blackContent) {
		settype($blackContent, "boolean");
		$this->blackContent = $blackContent;
		return $this;
	}
}
?>