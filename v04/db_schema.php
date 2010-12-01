<?php
require_once("strings/strings.php");

class Table {
	private $name;
	private $columns;
	
	/**
	 *
	 * @param $name: nome della tabella
	 * @param $columns: array associativo nome_colonna => Column
	 */
	function __construct($name, $columns) {
		$this->name = $name;
		$this->columns = $columns;
		foreach($this->columns as $cname => $c) {
			$c->setTable($this->name);
		}
	}
	
	function getName() {
		return $this->name;
	}
	
	function getColumn($columnname) {
		if(isset($this->columns[$columnname]))
			return clone $this->columns[$columnname];
		return false;
	}
	
	function getColumns() {
		return $this->columns;
	}
	
	function __toString() {
		$s = "<b>TABLE</b> (name = <u>" . $this->name . "</u>" .
			 " | columns = (<br />";
		$first = true;
		foreach($this->columns as $name => $col) {
			if($first) $first = false;
			else $s.= ",<br />\t ";
			$s.= $col;
		}
		$s.= "))";
		return $s;
	}
	
	function __clone() {
		$c = array();
		foreach($this->columns as $name => $col)
			$c[$name] = clone $col;
		$this->columns = $c;
	}
}

class Column {
	private $name;
	private $table;
	private $properties;
	
	/**
	 *
	 * @param $name: nome della colonna.
	 * @param $properties: array (non associativo) di elementi di Property
	 * @param $tablename: nome della tabella, non è obbligatorio in quanto ogni tabella setta questo valore quando si aggiunge la colonna.
	 */
	function __construct($name, $properties, $tablename) {
		$this->name = $name;
		$this->properties = $properties;
		$this->table = $tablename;
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
		$s = "<b>COLUMN</b> (name = <u>" . $this->getTable() . "." . $this->name . "</u>" .
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
	static $FOREIGNKEY = "FOREIGN KEY"; // NOT IMPLEMENTED
	static $UNIQUE = "UNIQUE";
	static $NOTNULL = "NOT NULL";
}

class DBSchema {
	private $tables; 
	
	/**
	 * @param $tables: tabelle array associativo nome_tabella => Table
	 */
	function __construct($tables) {
		if(!isset($tables) || !is_array($tables)) {
			if(file_exists("db_schema.dbs")) {
				$this->tables = unserialize(file_get_contents("db_schema.dbs"));
				$GLOBALS["db_schema_status"] = "Schema loaded from file"; //DEBUG
			} else {
				$this->loadFromDatabase();
				$GLOBALS["db_schema_status"] = "Schema loaded from database"; //DEBUG
				$this->save();
			}
		} else {
			$t = array();
			for($i=0; $i<count($tables); $i++) {
				$t[$tables[$i]->getName()] = $tables[$i];
			}
			$this->tables = $t;
			$GLOBALS["db_schema_status"] = "Schame passed by user";
			$this->save();
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
		require_once("query.php");
		
		$db = mysql_connect(DB_HOSTNAME . ":" . DB_PORT, DB_USERNAME, DB_PASSWORD);
		mysql_select_db(DB_NAME, $db);
		//echo serialize($db); //DEBUG
		if(isset($db)) {
			$rs = mysql_query("SHOW TABLES", $db);
			$tabs = array();
			while($row = mysql_fetch_row($rs))
				$tabs[] = $row[0];
			//echo "<br />". serialize($rs); // DEBUG
			for($i=0; $i<count($tabs); $i++) {
				$rs = mysql_query("SELECT * FROM $tabs[$i] LIMIT 1");
				$cols = array();
				while($row = mysql_fetch_field($rs)) {
					$props = array();
					if($row->primary_key) $props[] = Property::$PRIMARYKEY;
					if($row->unique_key) $props[] = Property::$UNIQUE;
					if($row->not_null) $props[] = Property::$NOTNULL;
					//if($row->type = "string") $props[] = Property::$TYPE_STRING; //ritorna tutte string quindi l'ho tolta
					
					$cols[$row->name] = new Column($row->name,$props,$row->table);
					//echo "<br />". $cols[$row->name]; // DEBUG
				}
				$this->tables[$tabs[$i]] = new Table($tabs[$i],$cols);
				//echo "<br />". $this->tables[$tabs[$i]]; // DEBUG
			}
		}
	}
	
	function __toString() {
		$s = "";
		foreach($this->tables as $name => $table)
			$s.= $table . "<br />";
		return $s;
	}
	
	/**
	 * Restituisce un clone della tabella di nome $tablename.
	 * @param $tablename: nome della tabella ricercata.
	 * @return: il clone della tabella oppure false se $tablename non esiste.
	 */
	function getTable($tablename) {
		if(isset($this->tables[$tablename])) {
			$t = clone $this->tables[$tablename];
			//echo "<br />" . serialize($t); //DEBUG
			return $t;
		} else if(isset($this->tables[strtolower($tablename)])) {
			$t = clone $this->tables[strtolower($tablename)];
			//echo "<br />" . serialize($t); //DEBUG
			return $t;
		}
		return false;
	}
}
?>