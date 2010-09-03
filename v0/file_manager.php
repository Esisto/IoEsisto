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
	
}

class HTAccessManager {
	
	static function parse() {
		
	}
	
	static function addRule($rule) {
		return FileManager::appendToFile(".htaccess", $rule);
	}
}


?>