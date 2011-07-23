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
		
	/**
	 * Safely truncate text containing markup.
	 *
	 * @author Marc Görtz (http://marcgoertz.de/)
	 * @license http://creativecommons.org/licenses/by-sa/3.0/de/
	 * @param string $markup text containing markup
	 * @param int $length maximum length of truncated text (default: 400)
	 * @param string $appendix text added after truncated text (default: ellipsis)
	 * @return string truncated markup
	*/
	function truncateMarkup($markup, $length = 400, $appendix = '…') {
		$truncated = '';
		$lengthOutput = 0;
		$position = 0;
		$tags = array();
	
		// to avoid UTF-8 multibyte glitches we need entities, but no special characters for tags or existing entities
		$markup = str_replace(array(
			'&lt;', '&gt;', '&amp;',
		), array(
			'<', '>', '&',
		), htmlentities($markup, ENT_NOQUOTES, 'UTF-8'));
	
		// loop thru text
		while ($lengthOutput < $length && preg_match('{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}', $markup, $match, PREG_OFFSET_CAPTURE, $position)) {
			list($tag, $positionTag) = $match[0];
	
			// add text leading up to the tag or entity
			$text = substr($markup, $position, $positionTag - $position);
			if ($lengthOutput + strlen($text) > $length) {
				$truncated .= substr($text, 0, $length - $lengthOutput);
				$lengthOutput = $length;
				break;
			}
			$truncated .= $text;
			$lengthOutput += strlen($text);
	
			// add tags and entities
			if ($tag[0] === '&') {
				// handle the entity...
				$truncated .= $tag;
				// ... which is only one character
				$lengthOutput++;
			} else {
				// handle the tag
				$tagName = $match[1][0];
				if ($tag[1] === '/') {
					// this is a closing tag
					$openingTag = array_pop($tags);
					// check that tags are properly nested
					assert($openingTag === $tagName);
					$truncated .= $tag;
				} else if ($tag[strlen($tag) - 2] === '/') {
					// self-closing tag in XML dialect
					$truncated .= $tag;
				} else {
					// opening tag
					$truncated .= $tag;
					$tags[] = $tagName;
				}
			}
	
			// continue after the tag
			$position = $positionTag + strlen($tag);
		}
	
		// add any remaining text
		if ($lengthOutput < $length && $position < strlen($markup)) {
			$truncated .= substr($markup, $position, $length - $lengthOutput);
		}
	
		// close any open tags
		while (!empty($tags)) {
			$truncated .= sprintf('</%s>', array_pop($tags));
		}
	
		return $truncated . $appendix;
	}

}
?>