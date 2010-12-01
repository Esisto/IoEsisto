<?php
require_once("query.php");
require_once("dataobject/Editable.php");

abstract class Dao {
	protected $db;
	protected $table;
	protected $historyTable;
	
	function __construct() {
		$this->db = new DBManager();
		if($this->db->connect_errno())
			$this->db->display_connect_error("Dao::__construct()");
		
		$this->historyTable = Query::getDBSchema()->getTable(TABLE_HISTORY);
	}
	
	function load($id) {
		if(is_null($id)) throw new Exception("Attenzione! Non hai inserito il parametro di ricerca.");
		$this->checkConnection();
		return null;
	}
	
	function save($object, $objetcClass = null) {
		if(is_null($object)) throw new Exception("Attenzione! Non hai inserito l'oggetto da salvare.");
		
		if(!is_null($objectClass) && is_string($objectClass))
			if(!is_subclass_of($object, $objectClass))
				throw new Exception("Attenzione! L'oggetto da salvare non è del tipo richiesto.");
				
		$this->checkConnection();
		return null;
	}
	
	function delete($object, $objetcClass = null) {
		if(is_null($object)) throw new Exception("Attenzione! Non hai inserito l'oggetto da eliminare.");

		if(!is_null($objectClass) && is_string($objectClass))
			if(!is_subclass_of($object, $objectClass))
				throw new Exception("Attenzione! L'oggetto da eliminare non è del tipo richiesto.");
		
		if(is_subclass_of($object, "Editable"))
			if(!$object->isRemovable())
				throw new Exception("L'oggetto non può essere eliminato perché è stato iscritto ad un contest o è sotto revisione di un redattore.");
				
		$this->checkConnection();
		return null;
	}
	
	function update($object, $editor, $objetcClass = null) {
		if(is_null($object)) throw new Exception("Attenzione! Non hai inserito l'oggetto da modificare.");
		
		if(!is_null($objectClass) && is_string($objectClass))
			if(!is_subclass_of($object, $objectClass))
				throw new Exception("Attenzione! L'oggetto da modificare non è di tipo: " . $objectClass);
				
		if(!$editor->isEditor() && is_subclass_of($object, "Editable"))
			if(!$object->isEditable())
				throw new Exception("L'oggetto non può essere modificato perché è stato iscritto ad un contest o è sotto revisione di un redattore.");
				
		$this->checkConnection();
		return null;
	}
	
	protected function checkConnection() {
		if($this->db->connect_errno())
			throw new Exception("Si è verificato un errore di connessione. Aggiornare la pagina e riprovare.");
	}
	
	function setMainTable($tablename) {
		$this->table = Query::getDBSchema()->getTable($tablename);
	}
	
	function exists($object);
	
	function quickLoad($id);
	
	function updateState($object);
	
	function saveHistory($object, $editor, $operation) {
		//FIXME controllare che vada bene che stia in questa classe e la sua implementazione.
		$this->save($object);
		
		if(!is_a($editor, "User"))
			throw new Exception("Non hai settato chi ha fatto la modifica.");
		$data = array(DB::HISTORY_OBJECT => serialize($object),
					  DB::HISTORY_DATE => time(),
					  DB::HISTORY_EDITOR => $editor->getID(),
					  DB::HISTORY_OPERATION => $operation);
		
		$this->db->execute(Query::generateInsertStm($this->historyTable, $data), $this->historyTable, $object);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Errore salvando la precedente vesrione dell'oggetto. Riprovare.");
		
		return $this->db->last_inserted_id();
	}
}

?>