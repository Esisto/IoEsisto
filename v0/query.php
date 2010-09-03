<?php

class Operator {
	static $UGUALE = "=";
	static $MINORE = "<";
	static $MINOREUGUALE = "<=";
	static $MAGGIORE = ">";
	static $MAGGIOREUGUALE = ">=";
	static $LIKE = "LIKE";
}

class SelectOperator {
	static $UNION = "UNION";
	static $INTERSECT = "INTERSECT";
	static $EXCEPT = "EXCEPT";
}

class JoinConstraint {
	private $columnleft;
	private $columnright;
	
	function __construct($left, $right) {
		$this->columnleft = $left;
		$this->columnright = $right;
	}
	
	function generateWhereStm() {
		return $this->getColumnleft()->getName() . " = " . $this->getColumnright()->getName();
	}
	
	function getColumnLeft() {
		return $this->columnleft;
	}
	
	function getColumnRight() {
		return $this->columnright;
	}
}

class WhereConstraint {
	private $column;
	private $operator;
	private $data;
	
	function __construct($column, $operator, $data) {
		$this->column = $column;
		$this->operator = $operator;
		if($this->operator == null || $this->operator == "")
			$this->operator = Operator::$UGUALE;
		$this->data = $data;
	}
	
	function generateWhereStm(/*$alias*/) {
		if($this->operator == null || $this->operator == "")
			$this->operator = Operator::$UGUALE;
		$s = "";
		if(isset($alias) && !is_null($alias) && $alias != "")
			$s.= $alias . ".";
		$s = $this->getColumn()->getName() . " " . $this->getOperator() . " ";
		if(is_string($this->getData())) $s.= "'";
		if(is_bool($this->getData())) {
			if($this->getData()) $s.= 1;
			else $s.= 0;
		}
		$s.= $this->getData();
		if(is_string($this->getData())) $s.= "'";
		return $s;
	}
		
	function getData() {
		return $this->data;
	}
	
	function getColumn() {
		return $this->column;
	}
	
	function getOperator() {
		return $this->operator;
	}
}

class Query {
	private static $dbSchema = null;
	
	
	var $query_type; //deprecated
	var $num_rows; //deprecated
	private $rsindex; //deprecated
	var $last_inserted_id; //deprecated
	var $affected_rows; //deprecated
	private $rs; //deprecated
	var $error; //deprecated
	
	function initDBSchema() {
		require_once("db_schema.php");
		self::$dbSchema = new DBSchema(null);
	}
		
	/**
	 * Genera uno statement Select.
	 * 
	 * param tables: le tabelle che deve selezionare (array di Table) genera automaticamente degli alias
	 * param joinconst: le condizioni per ogni join come array di JoinConstraint (solo se più tabelle).
	 * param whereconst: le condizioni sugli attributi come array di WhereConstraint (opzionale).
	 * param options: opzioni aggiuntive, il sistema cerca:
	 * 			count: se usare l'operatore count. (boolean)
	 * 			limit: se usare l'operatore limit. (int, se 0 non usa limit)
	 * 			order: se usare l'operatore order. (int, >0 per ASC, altrimenti DESC)
	 * 			by: se usare l'operatore order. (array di stringhe, se vuoto non usa order)
	 * 			group: se usare l'operatore group. (array di stringhe, se vuoto non usa group)
	 *
	 * 	return stringa contenente la query SELECT desiderata. FALSE se c'è un errore.
	 */
	static function generateSelectStm($tables, $joinconst, $whereconst, $options) {
		if(!isset($tables) || !is_array($tables) ||
		   (count($tables)>1 && !isset($joinconst)) ||
		   (isset($joinconst) && !is_array($joinconst)) ||
		   (count($tables)>1 && count($joinconst)==0) ||
		   (isset($whereconst) && !is_array($whereconst)))
			return false;
		// SELECT
		$s = "SELECT ";
		if(isset($options["count"])) $s.= "COUNT(*)";
		else $s.= "*";
		// FROM
		$s.= " FROM ";
		$first = true;
		for($i=0; $i<count($tables); $i++) {
			if(!self::tableExists($tables[$i])) continue;
			if(!$first) $s.= ", ";
			else $first = false;
			$t = $tables[$i];
			$s.= $t->getName();
		}
		if($first) return false;
		// WHERE
		$first = true;
		for($i=0; $i<count($joinconst); $i++) {
			if(!self::columnExists($joinconst[$i]->getColumnLeft()) ||
			   !self::columnExists($joinconst[$i]->getColumnLeft()))
				continue;
			if($first) {
				$s.= " WHERE ";
				$first = false;
			} else
				$s.= " AND ";
			
			$s.= $joinconst[$i]->generateWhereStm(/*$leftalias, $rightalias*/);
		}
		if(count($tables)>1 && $first) return false;
		for($i=0; $i<count($whereconst); $i++) {
			if(!self::columnExists($whereconst[$i]->getColumn()))
				continue;
			if($first) {
				$s.= " WHERE ";
				$first = false;
			} else
				$s.= " AND ";
			$s.= $whereconst[$i]->generateWhereStm(/*$alias*/);
		}
		// OPZIONI
		// ORDER BY
		if(isset($options["order"]) && isset($options["by"]) &&
		   is_array($options["by"]) && count($options["by"] > 0)) {
			$by = $options["by"];
			$s1= " ORDER BY ";
			$first = true;
			for($i=0; $i<count($by); $i++) {
				if($by[$i] == null || self::columnExists($by[$i])) continue;
				if($first) $first = false;
				else $s1.= ", ";
				$s1.= $by[$i];
			}
			if($first) break; //se mi da un array di null non devo inserire order
			else $s.= $s1;
			
			if($options["order"] > 0)
				$s.= " ASC";
			else
				$s.= " DESC";
		}
		// LIMIT
		if(isset($option["limit"]) && $option["limit"] > 0)
			$s. " LIMIT " . $option["limit"];
		// GROUP BY
		if(isset($options["group"]) && is_array($options["group"]) && count($options["group"] > 0)) {
			$group = $options["group"];
			$s1= " GROUP BY ";
			$first = true;
			for($i=0; $i<count($group); $i++) {
				if($group[$i] == null || self::columnExists($group[$i])) continue;
				if($first) $first = false;
				else $s1.= ", ";
				$s1.= $group[$i];
			}
			if($first) break; //se mi da un array di null non devo inserire group
			else $s.= $s1;
		}
		
		return $s;
	}
	
	/**
	 * Genera uno statement Select composto tramite UNION, INTERSECT, EXCEPT ecc...
	 *	
	 *	param $selectstms: un array di statement Select (generati con generateSelectStm).
	 *	param $operators: un array di operatory di tipo SelectOperator.
	 *	Questo array dovrà contenere n-1 elementi rispetto a $selectstms oppure uno solo,
	 *	in questo modo verrà inserito tra tutti gli statement in $selectstms.
	 *
	 *	return: lo statement composto. Se c'è un errore FALSE
	 */
	static function generateComplexSelectStm($selectstms, $operators) {
		if(!isset($selectstms) || !is_array($selectstms) || count($selectstms) == 0 ||
		   !isset($operators) || !is_array($operators) ||
		   (count($operators) != 1 && count($operators) != (count($selectstms)-1)))
			return false;
		
		$first = true;
		$s = "";
		for($i=0; $i<count($selectstms); $i++) {
			if($selectstms[$i] == "" || $selectstms[$i] == null)
				continue;
			if($first) $first = false;
			else {
				if(count($operators)==1)
					$s.= " " . $operators[0] . " ";
				else
					$s.= " " . $operators[$i-1] . " ";
			}
			$s.= $selectstms[$i];
		}
		return $first ? false : $s;
	}
	
	/**
	 * Genera uno statement Insert.
	 *
	 * param $table: la tabella in cui inserire una tupla.
	 * param $data: un array associativo contenente i valori da inserire per ogni colonna. 
	 *
	 * return: lo statement Insert. Se c'è un errore FALSE.
	 */
	static function generateInsertStm($table, $data) {
		if(!isset($table) || $table == null || $table == "" ||
		   !isset($data) || !is_array($data) || count($data) == 0)
			return false;
		
		if(!self::tableExists($table)) return false;
		$s = "INSERT INTO " . $table->getName();
		$s.= " (";
		$val = " VALUES (";
		$first = true;
		foreach($data as $column => $d) {
			if($column == null && $column == "" || !self::columnExists($table->getColumn($column))) continue;
			if(!$first) {
				$s.= ", ";
				$val.= ", ";
			} else
				$first = false;
			$s.= $column;
			if(is_string($d)) $val.= "'";
			$val.= $d;
			if(is_string($d)) $val.= "'";
		}
		if($first) return false;
		
		$s.= ")";
		$val.= ")";
		$s.= $val;
		
		return $s;
	}
	
	/**
	 * Genera uno statement Delete.
	 *
	 * param $table: la tabella in cui inserire una tupla.
	 * param $whereconst: un array di WhereConstraint. 
	 *
	 * return: lo statement Delete. Se c'è un errore FALSE.
	 */
	static function generateDeleteStm($table, $whereconst) {
		if(!isset($table) || $table == null || $table == "" ||
		   !isset($whereconst) || !is_array($whereconst) || count($whereconst) == 0)
			return false;
		
		if(!self::tableExists($table)) return false;
		$s = "DELETE FROM " . $table->getName();
		
		$first = true;
		$s1 = "";
		if(isset($whereconst) && $whereconst != null && $whereconst != "" && count($whereconst) > 0)
			$s1.= " WHERE ";
		for($i=0; $i<count($whereconst); $i++) {
			if($whereconst[$i] == null && $whereconst[$i] == "" ||
			   !self::columnExists($whereconst[$i]->getColumn())) continue;
			if($first) $first = false;
			else $s1.= " AND ";
			$s1.= $whereconst[$i]->generateWhereStm();
		}
		if(!$first)
			$s.= $s1;
		
		return $s;
	}
	
	/**
	 * Genera uno statement Update.
	 *
	 * param $table: tabella da aggiornare.
	 * param $data: array associativo di dati da aggiornare per ogni colonna.
	 * param $whereconst: array di WhereContraint per scegliere le tuple da aggiornare.
	 *
	 * return: lo statement Update. Se c'è un errore FALSE.
	 */
	static function generateUpdateStm($table, $data, $whereconst) {
		if(!isset($table) || $table == null || $table == "" ||
		   !isset($data) || !is_array($data) || count($data) == 0)
			return false;
		
		if(!self::tableExists($table)) return false;
		$s = "UPDATE " . $table->getName() . " SET ";
		$first = true;
		foreach($data as $column => $d) {
			if($column == null && $column == "" || !self::columnExists($table->getColumn($column))) continue;
			if($first) $first = false;
			else $s.= ", ";
			$s.= $column . " = ";
			if(is_string($d)) $s.= "'";
			$s.= $d;
			if(is_string($d)) $s.= "'";
		}
		if($first) return false;
		
		$first = true;
		$s1 = "";
		if(isset($whereconst) && $whereconst != null && $whereconst != "" && count($whereconst) > 0)
			$s1.= " WHERE ";
		for($i=0; $i<count($whereconst); $i++) {
			if($whereconst[$i] == null && $whereconst[$i] == "" ||
			   !self::columnExists($whereconst[$i]->getColumn())) continue;
			if($first) $first = false;
			else $s1.= " AND ";
			$s1.= $whereconst[$i]->generateWhereStm();
		}
		if(!$first)
			$s.= $s1;
		
		return $s;
	}
	
	/**
	 * Controlla che una tabella esista sul database.
	 * 
	 * param $tablename: il nome della tabella da trovare.
	 *
	 * return: true o false.
	 */
	function tableExists($table) {
		if(self::$dbSchema == null)
			self::initDBSchema();
		
		//echo $table; //DEBUG
		return false !== self::$dbSchema->getTable($table->getName());
	}
	
	/**
	 * Controlla che una colonna esista.
	 *
	 * param $columnname: il nome della colonna da trovare.
	 *
	 * return: true o false.
	 */
	function columnExists($column) {
		if(self::$dbSchema == null)
			self::initDBSchema();
		
		if($column === false) return false;
		if(!self::tableExists(self::$dbSchema->getTable($column->getTable()))) return false;
		$table = self::$dbSchema->getTable($column->getTable());
		return $table->getColumn($column->getName()) !== false;
	}
	
	static function getDBSchema() {
		if(self::$dbSchema == null)
			self::initDBSchema();
		return self::$dbSchema;
	}
}

class DBManager {
	var $dblink = null; //it is public so I can use it directly
	var $result = null;
	private $errno;
	private $error;
	private $affected_rows;
	private $num_rows;
	private $last_inserted_id;
	private $info;
	private $query_type;
	
	function __construct() {
		$this->connect();
	}
	
	function execute($query, $tablename = null, $object = null) {
		if(isset($this->result) && !is_null($this->result))
			$this->free_result();
		
		$this->result = mysql_query($query, $this->dblink);
		$this->info = mysql_info($this->dblink);
		$this->errno = mysql_errno($this->dblink);
		$this->error = mysql_error($this->dblink);
		if($this->errno())
			$this->display_error("DBManager::execute()");
		if($object == LOGMANAGER) return;
		
		$this->query_type = substr($query, 0, 6);
		//DEBUG
		if(DEBUG) {
			echo "<p>" . $this->query_type . ": " . $this->info(); //DEBUG
			//echo "<br /><font color='green'>" . var_export($this->result, true) . "</font>";
			echo "<br />" . $query; //DEBUG
		}

		if($this->query_type == "SELECT") {
			$this->num_rows = mysql_num_rows($this->result);
		} else $this->num_rows = 0;
		if($this->query_type == "INSERT") {
			$this->last_inserted_id = mysql_insert_id($this->dblink);
		} else $this->last_inserted_id = false;
		if($this->query_type == "INSERT" || $this->query_type == "DELETE") {
			$this->affected_rows = mysql_affected_rows($this->dblink);
		} else $this->affected_rows = 0;
		if($this->query_type == "UPDATE")
			$this->affected_rows = intval(substr($this->info(), strpos($this->info(), "Changed: ")+9, strpos($this->info(), "Warnings: ")-1-9));
		
		//DEBUG		
		if(DEBUG)
			echo "<br /> aff = " . $this->affected_rows() . " | num = " . $this->num_rows() . " | lid = " . $this->last_inserted_id() . "</p>"; //DEBUG
		//END DEBUG
		if($this->affected_rows() > 0) {
			require_once("session.php");
			require_once("common.php");
			LogManager::addLogEntry(Session::getUser(), substr($query, 0, 6), $tablename, $object);
		}
		return $this->result;
	}
	
	function affected_rows() { return $this->affected_rows; }
	function error() { return $this->error; }
	function errno() { return $this->errno; }
	function connect_error() { return $this->error; }
	function connect_errno() { return $this->errno; }
	function info() { return $this->info; }
	function last_inserted_id() { return $this->last_inserted_id; }
	
	function num_rows() {
		return $this->num_rows;
	}
	function fetch_result() {
		return mysql_fetch_assoc($this->result);
	}
	function fetch_field() {
		if(is_a($this->result, "misqli_result"))
			return $this->result->fetch_field;
		return false;
	}
	function free_result() {
		//if(is_object($this->result) && is_a($this->result, "misqli_result")) $this->result->free();
		//if(is_bool($this->result)) $this->result = null;
	}
	
	function connect() {
		require_once("settings.php"); //file che contiene i dati d'accesso.
		require_once("strings/" . LANG . "strings.php");
		
		$this->dblink = mysql_connect(DB_HOSTNAME . ":" . DB_PORT, DB_USERNAME, DB_PASSWORD);
		
		if(!mysql_errno() && mysql_select_db(DB_NAME, $this->dblink)) {
			$GLOBALS[DB_STATUS] = DB_CONNECTED . DB_HOSTNAME . "/" . DB_NAME;
		} else {
			$GLOBALS[DB_STATUS] = DB_NOT_CONNECTED;
			echo "<p><b>CONNECTION ERROR " . mysql_errno($this->dblink) . ": </b><font color='red'>" . mysql_error($this->dblink) . "</font></p>";
		}
		
		//echo serialize($db); //DEBUG
		return $this->dblink;
	}
	
	function display_error($from) {
		if($this->errno())
			echo "<p><b>$from:</b> SQL ERROR " . $this->errno() . ": <font color='red'>" . $this->error() . "</font></p>";
		echo "<p><b>$from:</b> NO SQL ERROR</p>"; //DEBUG deve dare un errore solo se c'è!
	}
	
	function display_connect_error($from) {
		if($this->errno())
			echo "<p><b>$from:</b> CONNECTION ERROR " . $this->errno() . ": <font color='red'>" . $this->error() . "</font></p>";
		echo "<p><b>$from:</b> NO CONNECTION ERROR</p>"; //DEBUG deve dare un errore solo se c'è!
	}
}


?>