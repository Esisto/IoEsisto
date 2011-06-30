<?php 
class TemplateManager {
	static function getDefaultTemplate() {
		return "files/default/index.xml";
	}
	
	static function getTemplateForRequest($object, $id, $action) {
		switch($object) {
			case "Post":
				if($action == "New" || $action == "Edit")
					return "files/default/post_edit.xml";
			case "Comment":
			case "Vote":
				return "files/default/post.xml";
			case "User":
				if($action == "Register")
					return "files/default/register.xml";
			case "Feedback":
				return "files/default/profile.xml";
			case "Signin":
				return "files/default/register.xml";
			case "Mail":
			case "Directory":
				return "files/default/mail.xml";
			case "Contest":
				return "files/default/contest.xml";
			case "Partner":
				return "files/default/partner.xml";
			case "Resource":
				return "files/default/resource.xml";
			case "Preferences":
				return "files/default/preferences.xml";
			case "Copyright":
			case "Rules":
			case "Conditions":
			case "Contacts":
			case "Privacy":
				return "files/default/legal.xml";
		}
		return self::getDefaultTemplate();
	}
}

class TemplateParser {
	static $NO_ERROR = 0;
	static $FILE_NOT_EXISTS = 1;
	static $PARSE_ERROR = 2;
	
	var $parser;
	
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