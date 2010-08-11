<?php

class Operator {
	static $UGUALE = "=";
	static $MINORE = "<";
	static $MINOREUGUALE = "<=";
	static $MAGGIORE = ">";
	static $MAGGIOREUGUALE = ">=";
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
	
	function generateWhereStm() {
		if($this->operator == null || $this->operator == "")
				$this->operator = Operator::$UGUALE;
			return $this->column + " " + $this->operator + " ?";
		}
		
	function getData() {
		return $this->data;
	}
	
	function getColumn() {
		return $this->column;
	}
}

class Query {
	private $db;
	private $dbSchema = null;
	private $rs;
	
	function __construct($db) {
		$this->db = $db;
		$this->rs; //result set
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
			
			$s.= $tables[$i];
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
		$s = "INSERT INTO " + $table;
		$s.= " (";
		$val = " VALUES (";
		$first = true;
		foreach($data as $column => $d) {
			if($column == null && $column == "" || !$this->columnExists($column)) continue;
			if(!$first) {
				$s.= ", ";
				$val.= ", ";
			} else
				$first = false;
			$s.= $column;
			$val.= "?";
		}
		if($first) return false;
		
		$s.= ")";
		$val.= ")";
		$s.= $val;
		
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
		$s = "UPDATE " + $table + " SET ";
		$first = true;
		foreach($data as $column => $d) {
			if($column == null && $column == "" || $this->columnExists($column)) continue;
			if($first) $first = false;
			else $s.= ", ";
			$s.= $column . " = ?";
		}
		if($first) return false;
		
		$first = true;
		$s1 = "";
		if(isset($whereconst) && $whereconst != null && $whereconst != "" && count($whereconst) > 0)
			$s1.= " WHERE ";
		for($i=0; $i<count($whereconst); $i++) {
			if($whereconst[$i] == null && $whereconst[$i] == "" ||
			   $this->columnExists($whereconst[$i]->getColumn())) continue;
			if($first) $first = false;
			else $s1.= ", ";
			$s1.= $whereconst->generateWhereStm();
		}
		if(!$first)
			$s.= $s1;
		
		return $s;
	}
	
	/**
	 * Controlla che una tabella esista sul database.
	 * 
	 * param $tablename: il nome della tabella da trovare.
	 * param $onDB: forza la ricerca sul database e non sui file di impostazione.
	 *
	 * return: true o false.
	 */
	function tableExists($table, $onDB) {
		if($onDB) {
			// TODO forse non necessario…
			// controlla sul database l'esistenza della tabella con una query.
			// return se il risultato è un elemento valido.
			
			return true;
		} else {
			if($this->dbSchema == null) {
				require_once("db_schema.php");
				$this->dbSchema = new DBSchema();
			}
			
			$tables = $this->dbSchema->getTables();
			return isset($tables[$table->getName()]);
		}
	}
	
	/**
	 * Controlla che una colonna esista.
	 *
	 * param $columnname: il nome della colonna da trovare.
	 * param $onDB: forza la ricerca sul database e non sui file di impostazione.
	 *
	 * return: true o false.
	 */
	function columnExists($column, $onDB) {
		if($onDB) {
			// TODO forse non necessario…
			// controlla sul database l'esistenza della colonna con una query.
			// return se il risultato è un elemento valido.
			
			return true;
		} else {
			if($this->dbSchema == null) {
				require_once("db_schema.php");
				$this->dbSchema = new DBSchema();
			}
			
			if(!$this->tableExists($column->getTable())) return false;
			$tables = $this->dbSchema->getTables();
			$table = $tables[$column->getTable()->getName()];
			$columns = $table->getColumns();
			return isset($columns[$column->getName()]);
		}
	}
}

function connect() {
	require_once("settings.php"); //file che contiene i dati d'accesso.
	
	$db = mysql_connect($hostname, $username, $password);
	return $db;
}
?>