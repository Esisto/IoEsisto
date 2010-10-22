<?php 
class TemplateManager {
	static function getDefaultTemplate() {
		return "files/default/index.xml";
	}
	
	static function getTemplateForRequest($request) {
		switch($request["object"]) {
			case "Post":
				if($request["action"]=="New" || $request["action"]=="Edit")
					return "files/default/post.xml"; //FIXME "files/default/post_edit.xml";
			case "Comment":
			case "Vote":
				return "files/default/post.xml";
			case "User":
				if($request["action"]=="Register")
					return "files/default/register.xml";
			case "Feedback":
				return "files/default/profile.xml";
			case "Mail":
			case "Directory":
				//FIXME return "files/default/mail.xml";
			case "Contest":
				//FIXME return "files/default/contest.xml";
			case "Partner":
				//FIXME return "files/default/partner.xml";
			case "Contest":
				//FIXME return "files/default/contest.xml";
			case "Resource":
				//FIXME return "files/default/resource.xml";
			case "Preferences":
				//FIXME return "files/default/preferences.xml";
		}
		return self::getDefaultTemplate();
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
	private $index = 0;
	
	function nextElement() {
		if($this->template == null)
			return false;
		if($this->index >= count($this->template)) return false;
		
		return $this->template[$this->index++];
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