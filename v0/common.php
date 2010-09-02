<?php

class Report {
	private $ID;
	private $author;
	private $post;
	private $report;
	
	function __construct($author,$post,$report){
		$this->author = $author;
		$this->post = $post;
		$this->report = $report;
	}
	
	function getAuthor() {
		return $this->author;
	}
	
	function getPost() {
		return $this->post;
	}
	
	function getReport() {
		return $this->report;
	}
	
	function getID() {
		return $this->ID;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	
	/**
	 * Salva il report nel database.
	 * 
	 * param savingMode: uno dei valori della classe SavingMode.
	 * se INSERT: crea una nuova tupla in Report.
	 * se UPDATE: non fa nulla. Non si può modificare un report.
	 */
	function save() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $_SESSION["q"]->getDBSchema()->getTable(TABLE_REPORT);
			$data = array(REPORT_TEXT => $this->getReport(),
						  REPORT_POST => $this->getPost(),
						  REPORT_USER => $this->getAuthor());
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			$this->ID = $_SESSION["q"]->last_inserted_id();
			//echo "<br />" . $this; //DEBUG
			return $this->getID();
		}
		return false;
	}
	
	function delete() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $_SESSION["q"]->getDBSchema()->getTable(TABLE_REPORT);
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(REPORT_ID),Operator::$UGUALE,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($_SESSION["q"]->affected_rows() == 1) {
				return $this;
			}
		}
		return false;			
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "Report (author = " . $this->getAuthor() .
			 " | post = " . $this->getPost() .
			 " | report = " . $this->getReport() .
			 ")";
		return $s;
	}
}

class Resource {
	private $ID;
	private $owner;
	private $path;
	private $type;
	
	static $VIDEO = "video";
	static $PHOTO = "photo";
	
	function __construct($owner,$path,$type){
		$this->owner = $owner;
		$this->path = $path;
		$this->type = $type;
	}
	
	function getOwner() {
		return $this->owner;
	}
	
	function getPath() {
		return $this->path;
	}
	
	function getType() {
		return $this->type;
	}
	
	function getID() {
		return $this->ID;
	}
	
	function setID($id) {
		$this->ID = $id;
		return $this;
	}
	
	/**
	 * Salva il report nel database.
	 * 
	 * param savingMode: uno dei valori della classe SavingMode.
	 * se INSERT: crea una nuova tupla in Report.
	 * se UPDATE: non fa nulla. Non si può modificare un report.
	 */
	function save() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $_SESSION["q"]->getDBSchema()->getTable(TABLE_RESOURCE);
			$data = array(RESOURCE_OWNER => $this->getOwner(),
						  RESOURCE_PATH => $this->getPath(),
						  RESOURCE_TYPE => $this->getType());
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			$this->setID($_SESSION["q"]->last_inserted_id());
			//echo "<br />" . $this; //DEBUG
			return $this->getID();
		}
		return false;
	}
	
	function delete() {
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$table = $_SESSION["q"]->getDBSchema()->getTable(TABLE_RESOURCE);
			$rs = $_SESSION["q"]->execute($s = $_SESSION["q"]->generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(RESOURCE_ID),Operator::$UGUALE,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($_SESSION["q"]->affected_rows() == 1) {
				return $this;
			}
		}
		return false;			
	}
	
	/**
	 * @Override
	 */
	function __toString() {
		$s = "Resource (ID = " . $this->getID() .
			 " | owner = " . $this->getOwner() .
			 " | path = " . $this->getPath() .
			 " | type = " . $this->getType() .
			 ")";
		return $s;
	}
}

class LogManager {
	static $INSERT = "INSERT";
	static $DELETE = "DELETE";
	static $UPDATE = "UPDATE";
	
	/**
	 * Recupera il contenuto del Log da $from a $to.
	 * 
	 * param $from: data TimeStamp da cui selezionare le entry del Log. Se 0 parte dall'inizio.
	 * param $to: data TimeStamp in cui finire la selezione delle entry del Log. Se 0 arriva fino alla fine.
	 * return: array contenente tutte le entry.
	 */
	static function getLog($from, $to) {
		require_once("query.php");
		if(!isset($GLOBALS["q"]))
			if(!isset($_SESSION["q"]))
				$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			$dbs = $_SESSION["q"]->getDBSchema();
			$table = $dbs->getTable(TABLE_LOG);
			$s = "";
			if(is_numeric($from) && $from != 0) {
				$s1 = $_SESSION["q"]->generateSelectStm(array($table), array(),
											array(new WhereConstraint($table->getColumn(LOG_TIMESTAMP),Operator::$MAGGIOREUGUALE,$from)),
											array());
			}
			if(is_numeric($to) && $to != 0) {
				$s2 = $_SESSION["q"]->generateSelectStm(array($table), array(),
											array(new WhereConstraint($table->getColumn(LOG_TIMESTAMP),Operator::$MINOREUGUALE,$to)),
											array("order" => 1, "by" => LOG_TIMESTAMP));
			}
			if(is_numeric($from) && $from != 0 && is_numeric($to) && $to != 0) {
				$s = $_SESSION["q"]->generateComplexSelectStm(array($s1, $s2), array(SelectOperator::$INTERSECT));
			} else if(is_numeric($from) && $from != 0) {
				$s = $s1;
			} else if(is_numeric($to) && $to != 0) {
				$s = $s2;
			} else {
				return array();
			}
			//echo "<br />" . $s; //DEBUG
			$rs = $_SESSION["q"]->execute($s, $table->getName(), LOGMANAGER);
			$log_result = array();
			while($row = mysql_fetch_row) {
				$log_result[] = $row;
			}
			//echo "<br />" . serialize($log_result); //DEBUG
			return $log_result;
		}
		return array();
	}
	
	/**
	 * Aggiunge una entry al Log.
	 *
	 * param $user: l'utente che ha fatto l'azione.
	 * param $action: l'azione eseguita dall'utente, fa parte delle chiavi di LogManager::$actions.
	 * param $object: l'oggetto che subisce l'azione (prima che venga eseguita).
	 *
	 * return: l'id della entry inserita, false se non c'è riuscito.
	 */
	static function addLogEntry($user, $action, $tablename, $object) {
		if($object == LOGMANAGER) return;
		//echo $user. $action. $tablename . serialize(is_object($object)); //DEBUG
		if(!isset($user) || !is_numeric($user) ||
		   !isset($action) || ($action != self::$DELETE && $action != self::$INSERT && $action != self::$UPDATE) ||
		   !isset($object) || is_null($object) || !is_object($object))
			return false;
		
		require_once("query.php");
		if(!isset($_SESSION["q"]))
			$_SESSION["q"] = new Query();
		if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
			define_tables(); defineLogColumns();
			$table = $_SESSION["q"]->getDBSchema()->getTable(TABLE_LOG);
			//echo "<br />" . $tablename; //DEBUG
			
			$data = array(LOG_ACTION => $action,
						  LOG_TABLE => $tablename,
						  LOG_SUBJECT => $user,
						  LOG_OBJECT => sha1(serialize($object)));
			$s = $_SESSION["q"]->generateInsertStm($table, $data);
			//echo "<br />" . $s; //DEBUG
			$rs = $_SESSION["q"]->execute($s, $table->getName(), LOGMANAGER);
			return mysql_insert_id();
		}
		return false;
	}
}

class Filter {
	static function filterText($text) {
		return htmlspecialchars(htmlentities(addslashes($text)));
	}
	
	// TODO controlla il funzionamento
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
	
	static function decodeFilteredText($text) {
		return stripcslashes(html_entity_decode(htmlspecialchars_decode($text)));
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
	
	function clean($value) {
		// Stripslashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes( $value );
		}
		
		// Quote if not a number or a numeric string
		if (!is_numeric($value) && !empty($value)) {
			$value = mysql_real_escape_string($value);
		}
		return $value;
	}
}
?>