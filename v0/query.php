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
		return this.columnleft + " = " + this.columnright;
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
	private $db;
	private $dbSchema = null;
	private $query_type;
	private $num_fields;
	private $num_rows;
	private $rsindex;
	private $last_inserted_id;
	private $affected_rows;
	private $rs; //result set
	
	function __construct() {
		$this->db = connect();
		require_once("db_schema.php");
		$this->dbSchema = new DBSchema(null);
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
	function generateSelectStm($tables, $joinconst, $whereconst, $options) {
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
			if(!$this->tableExists($tables[$i])) continue;
			if(!$first) $s.= ", ";
			else $first = false;
			$t = $tables[$i];
			$s.= $t->getName();
		}
		if($first) return false;
		// WHERE
		$first = true;
		for($i=0; $i<count($joinconst); $i++) {
			if(!$this->columnExists($joinconst[$i]->getColumnLeft()) ||
			   !$this->columnExists($joinconst[$i]->getColumnLeft()))
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
			if(!$this->columnExists($whereconst[$i]->getColumn()))
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
				if($by[$i] == null || $this->columnExists($by[$i])) continue;
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
				if($group[$i] == null || $this->columnExists($group[$i])) continue;
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
	function generateComplexSelectStm($selectstms, $operators) {
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
	function generateInsertStm($table, $data) {
		if(!isset($table) || $table == null || $table == "" ||
		   !isset($data) || !is_array($data) || count($data) == 0)
			return false;
		
		if(!$this->tableExists($table)) return false;
		$s = "INSERT INTO " . $table->getName();
		$s.= " (";
		$val = " VALUES (";
		$first = true;
		foreach($data as $column => $d) {
			if($column == null && $column == "" || !$this->columnExists($table->getColumn($column))) continue;
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
	function generateDeleteStm($table, $whereconst) {
		if(!isset($table) || $table == null || $table == "" ||
		   !isset($whereconst) || !is_array($whereconst) || count($whereconst) == 0)
			return false;
		
		if(!$this->tableExists($table)) return false;
		$s = "DELETE FROM " . $table->getName();
		
		$first = true;
		$s1 = "";
		if(isset($whereconst) && $whereconst != null && $whereconst != "" && count($whereconst) > 0)
			$s1.= " WHERE ";
		for($i=0; $i<count($whereconst); $i++) {
			if($whereconst[$i] == null && $whereconst[$i] == "" ||
			   !$this->columnExists($whereconst[$i]->getColumn())) continue;
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
	function generateUpdateStm($table, $data, $whereconst) {
		if(!isset($table) || $table == null || $table == "" ||
		   !isset($data) || !is_array($data) || count($data) == 0)
			return false;
		
		if(!$this->tableExists($table)) return false;
		$s = "UPDATE " . $table->getName() . " SET ";
		$first = true;
		foreach($data as $column => $d) {
			if($column == null && $column == "" || !$this->columnExists($table->getColumn($column))) continue;
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
			   !$this->columnExists($whereconst[$i]->getColumn())) continue;
			if($first) $first = false;
			else $s1.= " AND ";
			$s1.= $whereconst[$i]->generateWhereStm();
		}
		if(!$first)
			$s.= $s1;
		
		return $s;
	}
	
	function execute($query, $tablename, $object) {
		$this->rs = mysql_query($query, $this->db);
		if($object == "LogManager") return;
		$this->query_type = substr($query, 0, 6);
		$this->num_fields = null;
		$this->num_fields = $this->num_fields();
		$this->num_rows = null;
		$this->num_rows = $this->num_rows();
		$this->rsindex = 0;
		$this->last_inserted_id = null;
		$this->last_inserted_id = $this->last_inserted_id();
		$this->affected_rows = null;
		$this->affected_rows = $this->affected_rows();
		//echo $this->affected_rows(); //DEBUG
		if($this->affected_rows() > 0) {
			require_once("common.php");
			require_once("session.php");
			LogManager::addLogEntry(Session::getUser(), $this->query_type, $tablename, $object, $this);
		}
		return $this->rs;
	}
	
	/**
	 * Controlla che una tabella esista sul database.
	 * 
	 * param $tablename: il nome della tabella da trovare.
	 *
	 * return: true o false.
	 */
	function tableExists($table) {
		if($this->dbSchema == null) {
			require_once("db_schema.php");
			$this->dbSchema = new DBSchema();
		}
		
		//echo $table; //DEBUG
		return false !== $this->dbSchema->getTable($table->getName());
	}
	
	/**
	 * Controlla che una colonna esista.
	 *
	 * param $columnname: il nome della colonna da trovare.
	 *
	 * return: true o false.
	 */
	function columnExists($column) {
		if($this->dbSchema == null) {
			require_once("db_schema.php");
			$this->dbSchema = new DBSchema();
		}
		
		if($column === false) return false;
		if(!$this->tableExists($this->dbSchema->getTable($column->getTable()))) return false;
		$table = $this->dbSchema->getTable($column->getTable());
		return $table->getColumn($column->getName()) !== false;
	}
	
	//function __destruct() {
	//	mysql_close($this->db);
	//}
	
	function getDBSchema() {
		return $this->dbSchema;
	}
	
	function affected_rows() {
		if(!is_null($this->affected_rows))
			return $this->affected_rows;
		if(!isset($this->query_type) || is_null($this->query_type) ||
		   $this->query_type == "SELECT")
			return 0;
		return mysql_affected_rows($this->db);
	}
	
	function num_rows() {
		if(!is_null($this->num_rows))
			return $this->num_rows;
		if(!isset($this->query_type) || is_null($this->query_type) || $this->query_type != "SELECT")
			return 0;
		return mysql_num_rows($this->rs);
	}
	
	function num_fields() {
		if(isset($this->num_fields) && !is_null($this->num_fields) && $this->num_fields != 0)
			return $this->num_fields;
		if(!isset($this->query_type) || is_null($this->query_type) || $this->query_type != "SELECT")
			return 0;
		$this->num_fields = mysql_num_rows($this->rs);
		return $this->num_fields;
	}
	
	function hasNext() {
		return $this->rsindex < $this->num_fields();
	}
	
	function next() {
		if(!isset($this->query_type) || is_null($this->query_type) || $this->query_type != "SELECT")
			return false;
		$this->rsindex++;
		return mysql_fetch_assoc($this->rs);
	}
	
	function getField($fieldname) {
		if(!isset($this->query_type) || is_null($this->query_type) || $this->query_type != "SELECT")
			return null;
		return mysql_result($this->rs, $this->rsindex, $fieldname);
	}
	
	function fields() {
		if(!isset($this->query_type) || is_null($this->query_type) || $this->query_type != "SELECT")
			return array();
		$fields = array();
		while($row = mysql_fetch_field($this->rs)) {
			$fields[] = $row["name"];
		}
		return $fields;
	}
	
	function last_inserted_id() {
		if(!is_null($this->last_inserted_id))
			return $this->last_inserted_id;
		if(!isset($this->query_type) || is_null($this->query_type) || $this->query_type != "INSERT")
			return false;
		return mysql_insert_id();
	}
}

function connect() {
	require_once("settings.php"); //file che contiene i dati d'accesso.
	require_once("strings/" . LANG . "strings.php");
	
	$db = mysql_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
	if(false !== $db && mysql_select_db(DB_NAME,$db))
		$GLOBALS["db_status"] = DB_CONNECTED . DB_HOSTNAME . "/" . DB_NAME;
	else
		$GLOBALS["db_status"] = DB_NOT_CONNECTED;
	
	// DEBUG
	//echo $GLOBALS["db_state"];
	// END DEBUG
	return $db;
}
?>