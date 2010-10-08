<?php 
class TemplateManager {
	
	static function getDefaultTemplate() {
		//TODO
	}
	
	static function getTemplateForRequest($request) {
		
		
		
	}
	
}

class TemplateParser {
	static $FILE_NOT_EXISTS = 1;
	
	static function parseTemplateFile($filename) {
		if(!file_exists($filename))
			return self::$FILE_NOT_EXISTS;
		

		$parser = xml_parser_create();
		
		while($piece = xml_parse($parser, $tml) !== false) {
			//TODO
		}
	}
	
	static function validateTemplate($templatetxt) {
		$dom = new DOMDocument();
		$dom->loadHTML($templatetxt);
		return $dom->validate();
	}
	
	static function saveTemplate($template) {
		if(self::validateTemplate($template)) {
			
		}
	}
}
?>