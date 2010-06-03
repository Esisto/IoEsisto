<?php
//------------------------------------------------------
//----------------- CLASSI DI DATI ---------------------
//------------------------------------------------------
class post {
	var $title;
	var $author;
	var $creationData;
	var $text;
	var $comments;
	
	function fillPost($title,$author,$text) {
		$this->title = $title;
		$this->author = $author;
		$this->text = $text;
		$this->creationData = time();
		$comments = array();
	}
	
	function addComment($comment) {
		$this->comments[$comment->getData()] = $comment;
	}
	
	function removeComment($comment) {
		$this->comments[$comment->getData()] = "";
	}
	
	function setText($text,$refreshData) {
		$t = $this->text;
		$this->text = $text;
		if($refreshData)
			$this->creationData = time();
		return $t;
	}
	
	function getComments() {
		return $this->comments;
	}

	function getData() {
		return $this->creationData;
	}
}

class comment {
	var $author;
	var $creationData;
	var $text;

	function fillComment($author,$text) {
		$this->author = $author;
		$this->text = $text;
		$this->creationData = time();
	}
	
	function setText($text) {
		$this->text = $text;
	}
	
	function getData() {
		return $this->creationData;
	}
}


//from blogpagescripts.php
function getContent($pagename,$subtitle,&$err) {
	if($pagename=="index" || $pagename=="crea")
		return "";
	if($pagename=="leggi") return intval($subtitle);
	if($pagename=="cancellacomm" || $pagename=="modificacomm" || $pagename=="cancellapost" || $pagename=="modificapost") return array(intval($subtitle),intval($err));
	if($pagename=="archivio") return $subtitle;
}

function getData($pagename,$content,&$err) {
	if(is_int($content)) {
		if($pagename=="leggi") {
			if(file_exists("post/" . $content . ".txt"))
				$dati = unserialize(loadFile("post/" . $content . ".txt","post",&$err));
			else if(file_exists("archivio/" . date("Y_m",$content) . "/" . $content . ".txt"))
				$dati = unserialize(loadFile("archivio/" . date("Y_m",$content) . "/" . $content . ".txt","post",&$err));
		}
	} else if(is_array($content)) {
		if($pagename=="modificapost" || $pagename == "modificacomm" || $pagename=="cancellapost" || $pagename == "cancellacomm") {
			if(file_exists("post/" . $content[0] . ".txt"))
				$dati[0] = unserialize(loadFile("post/" . $content[0] . ".txt","post",&$err));
			else if(file_exists("archivio/" . date("Y_m",$content[0]) . "/" . $content[0] . ".txt"))
				$dati[0] = unserialize(loadFile("archivio/" . date("Y_m",$content[0]) . "/" . $content[0] . ".txt","post",&$err));
			$dati[1] = $content[1];
		}
	} else {
		if($pagename=="index") {
			$dir = "post/";
			$testo = "";
			$a = 0;
			// legge i post
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (!is_dir($dir . $file)) {
							if(($file !== ".")&&($file !== "..")&&($file !== "")) {
								$filename = substr($file, 0, strlen($file) - 4);
								$dati[$filename] = unserialize(loadFile("post/" . $file,"post",&$err));
								$a++;
							}
						}
					}
					closedir($dh);
				}
			}
			krsort($dati);
		} else if($pagename=="crea") {
			if(!isset($dati))
				$dati = "new";
			else 
				if(isset($titolo))
					$dati = array($dati, $titolo);
		} else if($pagename=="archivio") {
			$dir = "./" . $content;
			$testo = "";
			$a = 0;
			// legge i post
			if (is_dir($dir)) {
				if ($dh = opendir($dir)) {
					while (($file = readdir($dh)) !== false) {
						if (!is_dir($dir . "/" . $file)) {
							if(($file !== ".")&&($file !== "..")) {
								$filename = substr($file, 0, strlen($file) - 4);
								$dati[$filename] = unserialize(loadFile($dir . "/" . $file,"post",&$err));
								$a++;
							}
						}
					}
					closedir($dh);
				}
			}
		}
	}
	return $dati;
}

function loadFile($file_path,$type,&$err) {
	$testo = file_get_contents($file_path);
	if($type=="post" || $type=="commento") {
		return $testo;
	}
	$err = "notype";
}
?>