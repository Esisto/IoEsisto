<?php
class Table {
	private $name;
	private $columns;
	
	function __construct($name, $columns) {
		$this->name = $name;
		$this->columns = $columns;
		for($i=0; $i<count($columns); $i++)
			$columns[$i]->setTable($this);
	}
	
	function getName() {
		return $this->name;
	}
	
	function getColumns() {
		return $this->columns;
	}
	
	function __toString() {
		$s = "TABLE (name = " . $this->name .
			 " | columns = (";
		for($i=0; $i<count($this->columns); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->columns[$i];
		}
		$s.= "))";
		return $s;
	}
}

class Column {
	private $name;
	private $table;
	private $properties;
	
	/**
	 * param $table: non è obbligatorio.
	 */
	function __construct($name, $properties, $table) {
		$this->name = $name;
		$this->properties = $properties;
		$this->table = $table;
	}
	
	function getName() {
		return $this->name;
	}
	
	function getProprties() {
		return $this->properties;
	}
	
	function getTable() {
		return $this->table;
	}
	
	function setTable($table) {
		$this->table = $table;
	}
	
	function __toString() {
		$s = "COLUMN (name = " . $this->table->getName() . "." . $this->name .
			 " | properties = (";
		for($i=0; $i<count($this->properties); $i++) {
			if($i>0) $s.= ", ";
			$s.= $this->properties[$i];
		}
		$s.= "))";
		return $s;
	}
}

class Property {
	static $PRIMARYKEY = "PRIMARY KEY";
	static $FOREIGNKEY = "FOREIGN KEY";
	static $UNIQUE = "UNIQUE";
	static $NULL = "NULL";
	static $NOTNULL = "NOT NULL";
}

class DBSchema {
	private $tables; //array associativo nome tabella => Table
	
	function __construct($tables) {
		if(!isset($tables) || !is_array($tables)) {
			if(file_exists("db_schema.dbs")) {
				$this->tables = unserialize(file_get_contents("db_schema.dbs"));
			} else {
				$this->loadFromDatabase();
			}
		} else {
			$t = array();
			for($i=0; $i<count($tables); $i++) {
				$t[$tables[$i]->getName()] = $tables[$i];
			}
			$this->tables = $t;
		}
	}
	
	/**
	 * @deprecated
	 * da usare solo per debug, togliere in versione finale.
	 */
	function __destruct() {
		$this->save();
	}
	
	function save() {
		$fp = fopen("db_schema.dbs", "w+");
		fwrite($fp, serialize($this->tables));
	}
	
	function loadFromDatabase() {
		// TODO recuperare lo schema dal db
	}
}
?>