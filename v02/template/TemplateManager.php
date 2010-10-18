<?php 
class TemplateManager {
	
	static function getDefaultTemplate() {
		//TODO
	}
	
	static function getTemplateForRequest($request) {
		
		return "files/default/default.xml";
		
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
	
	var $parser;
	
	/**
	 * @deprecated
	 */
	static function createParser($filename) {
		$tp = new TemplateParser();
		$tp->parser = xml_parser_create();
		$tp->setFile($filename);
		//var_dump($tp); //DEBUG
		return $tp;
	}
	
	function setFile($filename) {
		$this->filename = $filename;
		return $this;
	}
	
	private $template = null;
	private $index = -1;
	
	function nextElement() {
		if($this->template == null)
			return false;
		if($this->index >= count($this->template)) return false;
		
		$this->index++;
		return $this->template[$this->index];
	}
	
	static function parseTemplate($template) {
		$tp = new TemplateParser();
		$tp->parser = xml_parser_create();
		$tp->setFile($template);
		if(!file_exists($tp->filename))
			return self::$FILE_NOT_EXISTS;
		
		if(xml_parse_into_struct($tp->parser, file_get_contents($tp->filename), $tp->template)) {
			//echo serialize($tp->template); //DEBUG
			return $tp;
		}
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