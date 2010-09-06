<?php

class FileManager {
	
	static function getFile($filename, $mode) {
		return fopen($filename, $mode);
	}
	
	static function writeToFile($filename, $towrite) {
		
	}
	
	static function appendToFile($filename, $towrite) {
		$fp = fopen($filename, "a+");
		if($fp !== false) {
			return fwrite($fp, $towrite);
		}
		return false;
	}
	
	static function getServerPath() {
		$s = "http://"; //$s = ($_SERVER["HTTPS"] ? "https://" : "http://");
		$s.= $_SERVER["SERVER_NAME"];
		$s.= ":";
		$s.= ($_SERVER["SERVER_PORT"] != '80' /*or whatever*/ ? $_SERVER["SERVER_PORT"] : "");
		return $s;
	}
}

class HTAccessManager {
	
	static function parse() {
		
	}
	
	static function addRule($rule) {
		return FileManager::appendToFile(".htaccess", $rule);
	}
}


?>