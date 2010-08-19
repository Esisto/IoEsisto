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
		
		/**
		 * Salva il report nel database.
		 * 
		 * param savingMode: uno dei valori della classe SavingMode.
		 * se INSERT: crea una nuova tupla in Report.
		 * se UPDATE: non fa nulla. Non si può modificare un report.
		 */
		function save($savingMode) {
			if($savingMode == SavingMode::$INSERT) {
				require_once("query.php");
				if(!isset($GLOBALS["q"]))
					$q = new Query();
				if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
					$dbs = $q->getDBSchema();
					$table = $dbs->getTable("Report");
					$data = array("rp_report" => $this->getReport(),
								  "rp_post" => $this->getPost(),
								  "rp_user" => $this->getAuthor());
					$rs = $q->execute($s = $q->generateInsertStm($table,$data));
					//echo "<br />" . $s; //DEBUG
					//echo "<br />" . serialize($rs); //DEBUG
					$this->ID = mysql_insert_id();
					//echo "<br />" . $this; //DEBUG
					require_once("common.php");
					LogManager::addLogEntry($this->getAuthor(), LogManager::$INSERT, $this);
					return $this->getID();
				}
			}
			return false;
		}
		
		function delete() {
			require_once("query.php");
			if(!isset($GLOBALS["q"]))
				$q = new Query();
			if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
				$dbs = $q->getDBSchema();
				$table = $dbs->getTable("Report");
				$rs = $q->execute($s = $q->generateDeleteStm($table,
															 array(new WhereConstraint($table->getColumn("rp_ID"),Operator::$UGUALE,$this->getID()))));
				//echo "<br />" . $s; //DEBUG
				if(mysql_affected_rows() == 1) {
					require_once("common.php");
					LogManager::addLogEntry($this->getAuthor(), LogManager::$DELETE, $this);
					return $this;
				}
			}
			return false;			
		}
		
		/**
		 * @Override
		 */
		function __toString() {
			$s = "Vote (author = " . $this->author .
				 " | post = " . $this->post .
				 " | vote = " . $this->vote .
				 ")";
			return $s;
		}
	}
	
	class LogManager {
		static $INSERT = "Insert";
		static $DELETE = "Delete";
		static $UPDATE = "Update";
		
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
				$q = new Query();
			if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
				$dbs = $q->getDBSchema();
				$table = $dbs->getTable("Log");
				$s = "";
				if(is_numeric($from) && $from != 0) {
					$s1 = $q->generateSelectStm(array($table), array(),
												array(new WhereConstraint($table->getColumn("log_timestamp"),Operator::$MAGGIOREUGUALE,$from)),
												array());
				}
				if(is_numeric($to) && $to != 0) {
					$s2 = $q->generateSelectStm(array($table), array(),
												array(new WhereConstraint($table->getColumn("log_timestamp"),Operator::$MINOREUGUALE,$to)),
												array("order" => 1, "by" => "log_timestamp"));
				}
				if(is_numeric($from) && $from != 0 && is_numeric($to) && $to != 0) {
					$s = $q->generateComplexSelectStm(array($s1, $s2), array(SelectOperator::$INTERSECT));
				} else if(is_numeric($from) && $from != 0) {
					$s = $s1;
				} else if(is_numeric($to) && $to != 0) {
					$s = $s2;
				} else {
					return array();
				}
				//echo "<br />" . $s; //DEBUG
				$rs = $q->execute($s);
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
		static function addLogEntry($user, $action, $object) {
			if(!isset($user) || !is_numeric($user) ||
			   !isset($action) || ($action != self::$DELETE && $action != self::$INSERT && $action != self::$UPDATE) ||
			   !isset($object) || is_null($object) || !is_object($object))
				return false;
			
			require_once("query.php");
			if(!isset($GLOBALS["q"]))
				$q = new Query();
			if($GLOBALS["db_status"] != DB_NOT_CONNECTED) {
				$table = $q->getDBSchema()->getTable("Log");
				//echo "<br />" . $table; //DEBUG
				
				$data = array("log_action" => $action,
							  "log_user" => $user,
							  "log_object" => serialize($object));
				$s = $q->generateInsertStm($table, $data);
				//echo "<br />" . $s; //DEBUG
				$rs = $q->execute($s);
				return mysql_insert_id();
			}
			return false;
		}
	}
	
	class Filter {
		static function filterText($text) {
			return htmlspecialchars(htmlentities(addslashes($text)));
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
	}
?>