<?php 
class TemplateManager {
	
	static function getDefaultTemplate() {
		//TODO
	}
	
	static function getTemplateForRequest($request) {
		
		
		
	}
	
	/**
	 * @deprecated serve solo come test per il parsing, usare TemplateParser.
	 * @param unknown_type $filename
	 */
	static function parseTemplateFile($filename) {
		$parser = xml_parser_create();
		if(!file_exists($filename))
			echo self::$FILE_NOT_EXISTS;
		
		if(xml_parse_into_struct($parser, file_get_contents($filename), $template))
			echo var_export($template);
		else echo self::$PARSE_ERROR;
	}
}

class TemplateParser {
	static $NO_ERROR = 0;
	static $FILE_NOT_EXISTS = 1;
	static $PARSE_ERROR = 2;
	
	private $object;
	private $parser;
	
	static function createParser($filename = null) {
		$tp = new TemplateParser();
		$tp->parser = xml_parser_create();
		return $this->setFile($filename);
	}
	
	function setFile($filename) {
		$this->filename = $filename;
		return $this;
	}
	
	private $template = null;
	private $index = 0;
	
	function nextElement() {
		if($template == null)
			if($err = $this->parseTemplate() != self::$NO_ERROR)
				return $err;
		if($index >= count($this->template)) return false;
		
		return $this->template[$index];
	}
	
	private function parseTemplate() {
		if(!file_exists($this->filename))
			return self::$FILE_NOT_EXISTS;
		
		if(xml_parse_into_struct($this->parser, file_get_contents($this->filename), $this->template))
			return self::$NO_ERROR;
		return self::$PARSE_ERROR;
	}
	
	static function validateTemplate($templatetxt) {
		$dom = new DOMDocument();
		$dom->loadXML($templatetxt);
		return $dom->schemaValidate("template/template.xsd");
	}
	
	static function saveTemplate($template) {
		if(self::validateTemplate($template)) {
			
		}
	}
}
?>