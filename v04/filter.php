<?php
class Filter {
	static function filterText($text) {
		//return self::clean(htmlentities(addslashes(self::escape_utf8($text))));
		return htmlspecialchars(htmlentities(self::clean($text)));
	}
	
	static function textToHyperlink($text) {
		return preg_replace("#http://([A-z0-9./-]+)#", '<a href="$1">$0</a>', $text);
	}
	
	static function filterArray($array) {
		$newarray = array();
		foreach($array as $key => $value) {
			if(is_string($value))
				$value = self::filterText($value);
			$newarray[$key] = $value;
		}
		return $newarray;
	}
	
	private static function escape_utf8($text) {
		$text = str_replace("&", "&amp", $text);
		$text = str_replace("à", "&agrave", $text);
		$text = str_replace("è", "&egrave", $text);
		$text = str_replace("é", "&eacute", $text);
		$text = str_replace("ì", "&igrave", $text);
		$text = str_replace("ò", "&ograve", $text);
		$text = str_replace("ù", "&ugrave", $text);
		return $text;
	}
	
	static function decodeFilteredText($text) {
		//return html_entity_decode(stripslashes($text));
		return stripslashes(html_entity_decode(htmlspecialchars_decode($text)));
	}
	
	static function decodeFilteredArray($array) {
		$newarray = array();
		foreach($array as $key => $value) {
			if(is_string($value))
				$value = self::decodeFilteredText($value);
			$newarray[$key] = $value;
		}
		return $newarray;
	}
	
	static function textToPermalink($text) {
		$permalink = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
		$permalink = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $permalink);
		$permalink = strtolower(trim($permalink, '-'));
		$permalink = preg_replace("/[\/_|+ -]+/", '_', $permalink);
	
		return $permalink;
	}
	
	static function clean($value) {
		// Stripslashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		
		// Quote if not a number or a numeric string
		if (!is_numeric($value) && !empty($value)) {
			require_once 'query.php';
			$db = new DBManager();
			$value = mysql_real_escape_string($value, $db->dblink);
		}
		return $value;
	}
	
	/**
	 * Converte un array di stringhe in una unica stringa dove le varie stringhe sono separate da virgole.
	 * @param array $array un array di stringhe.
	 * @param string $separator una stringa contenente un separatore. (Default ",")
	 * @return una stringa con tutti i dati dell'array.
	 */
	static function arrayToText($array, $separator = ", ") {
		$s = "";
		$first = true;
		foreach ($array as $value) {
			if($first) $first = false;
			else $s.= $separator;
			$s.= $value;
		}
		return $s;
	}
	
	static function hash($text) {
		return sha1($text);	
	}
	
	static function encodePassword($password) {
		return self::hash($password); //si può decidere di cambiare questo encoding
	}
}
?>