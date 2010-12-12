<?php
require_once("query.php");

class LogManager {
	static $INSERT = "INSERT";
	static $DELETE = "DELETE";
	static $UPDATE = "UPDATE";
	
	/**
	 * Recupera il contenuto del Log da $from a $to.
	 * 
	 * @param $from: data TimeStamp da cui selezionare le entry del Log. Se 0 parte dall'inizio.
	 * @param $to: data TimeStamp in cui finire la selezione delle entry del Log. Se 0 arriva fino alla fine.
	 * @return: array contenente tutte le entry.
	 */
	static function getLog($from, $to) {
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineLogColumns();
			$table = Query::getDBSchema()->getTable(TABLE_LOG);
			$s = "";
			if(is_numeric($from) && $from != 0) {
				$s1 = Query::generateSelectStm(array($table), array(),
											array(new WhereConstraint($table->getColumn(LOG_TIMESTAMP),Operator::GREATEROREQUAL,$from)),
											array());
			}
			if(is_numeric($to) && $to != 0) {
				$s2 = Query::generateSelectStm(array($table), array(),
											array(new WhereConstraint($table->getColumn(LOG_TIMESTAMP),Operator::LESSEROREQUAL,$to)),
											array("order" => 1, "by" => LOG_TIMESTAMP));
			}
			if(is_numeric($from) && $from != 0 && is_numeric($to) && $to != 0) {
				$s = Query::generateComplexSelectStm(array($s1, $s2), array(SelectOperator::INTERSECT));
			} else if(is_numeric($from) && $from != 0) {
				$s = $s1;
			} else if(is_numeric($to) && $to != 0) {
				$s = $s2;
			} else {
				return array();
			}
			//echo "<br />" . $s; //DEBUG
			$rs = $db->execute($s, $table->getName(), LOGMANAGER);
			$log_result = array();
			if($db->num_rows() > 0) {
				while($row = mysql_fetch_row) {
					$log_result[] = $row;
				}
				//echo "<br />" . serialize($log_result); //DEBUG
				return $log_result;
			} else $db->display_error("LogManager::getLog()");
		} else $db->display_connect_error("LogManager::getLog()");
		return array();
	}
	
	/**
	 * Aggiunge una entry al Log.
	 *
	 * @param $user: l'utente che ha fatto l'azione.
	 * @param $action: l'azione eseguita dall'utente, fa parte delle chiavi di LogManager::$actions.
	 * @param $object: l'oggetto che subisce l'azione (prima che venga eseguita).
	 *
	 * @return: l'id della entry inserita, false se non c'è riuscito.
	 */
	static function addLogEntry($user, $action, $tablename, $object) {
		if($object == LOGMANAGER) return;
		//echo $user. $action. $tablename . serialize(is_object($object)); //DEBUG
		if(!isset($user) || !is_numeric($user) ||
		   !isset($action) || ($action != self::$DELETE && $action != self::$INSERT && $action != self::$UPDATE) ||
		   !isset($object) || is_null($object) || !is_object($object))
			return false;
		
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineLogColumns();
			$table = Query::getDBSchema()->getTable(TABLE_LOG);
			//echo "<br />" . $tablename; //DEBUG
			
			$sha1ed = sha1(serialize($object));
			$id = sha1($sha1ed . $action . $table . $user . time());
			$data = array(LOG_ID => $id,
						  LOG_ACTION => $action,
						  LOG_TABLE => $tablename,
						  LOG_SUBJECT => $user,
						  LOG_OBJECT => $sha1ed);
			$s = Query::generateInsertStm($table, $data);
			//echo "<br />" . $s; //DEBUG
			$rs = mysql_query($s, $db->dblink); //devo fare così è non usare DBManager::execute() perché non avrei affected_rows.
			if(mysql_affected_rows($db->dblink)) {
				return $id;
			} else $db->display_error("LogManager::addLogEntry()");
		} else $db->display_connect_error("LogManager::addLogEntry()");
		return false;
	}

	/**
	 * @deprecated
	 * Enter description here ...
	 * @param unknown_type $type
	 * @param unknown_type $id
	 */
	static function getAccessCount($type, $id) {
		if($type == null || $type == "" || $id == null)
			return 0;
			
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineLogColumns();
			$table = Query::getDBSchema()->getTable("AccessLog");
			
			$exists = false;
			if($type == "Post") {
				require_once 'post/PostManager.php';
				return 0;
				$exists = PostManager::postExists($id);
			} else if ($type == "User") {
				require_once 'user/UserManager.php';
				return 0;
				$exists = UserManager::userExists($id);
			} elseif ($type == "Partner") {
				//TODO: implementa Partner
//				require_once 'post/PartnerManager.php';
//				$exists = PartnerManager::partnerExists($id);
			}
			if($exists) {
				$wheres = array(new WhereConstraint($table->getColumn("alog_type"), Operator::EQUAL, $type),
								new WhereConstraint($table->getColumn("alog_id"), Operator::EQUAL, $id));
				$db->execute($s = Query::generateSelectStm(array($table), array(), $wheres, array()));
				if($db->num_rows() == 1) {
					$row = $db->fetch_result();
					$data = array("alog_count" => ++$row["alog_count"]);
					$db->execute($s = Query::generateUpdateStm($table, $data, $wheres), null, LOGMANAGER);
					if($db->affected_rows() == 1)
						return $row["alog_count"];
				} else {
					$data = array("alog_type" => $type, "alog_id" => $id);
					$db->execute($s = Query::generateInsertStm($table, $data));
					if($db->affected_rows() == 1);
						return 1;
				}
			}
			return 0;
		}
	}
}
?>