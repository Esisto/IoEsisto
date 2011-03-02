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
		
		$this->historyTable = Query::getDBSchema()->getTable(DB::TABLE_HISTORY);
	}
	
	protected function load($id) {
		if(is_null($id)) throw new Exception("Attenzione! Non hai inserito il parametro di ricerca.");
		$this->checkConnection();
		return null;
	}
	
	protected function save($object, $objectClass = null) {
		if(is_null($object)) throw new Exception("Attenzione! Non hai inserito l'oggetto da salvare.");
		
		if(!is_null($objectClass) && is_string($objectClass))
			if(!is_a($object, $objectClass) && !is_subclass_of($object, $objectClass))
				throw new Exception("Attenzione! L'oggetto da salvare non è del tipo richiesto.");
				
		$this->checkConnection();
		return null;
	}
	
	protected function delete($object, $objectClass = null) {
		if(is_null($object)) throw new Exception("Attenzione! Non hai inserito l'oggetto da eliminare.");

		if(!is_null($objectClass) && is_string($objectClass))
			if(!is_a($object, $objectClass) && !is_subclass_of($object, $objectClass))
				throw new Exception("Attenzione! L'oggetto da eliminare non è del tipo richiesto.");
		
		if(is_subclass_of($object, "Editable"))
			if(!$object->isRemovable())
				throw new Exception("L'oggetto non può essere eliminato perché è stato iscritto ad un contest o è sotto revisione di un redattore.");
				
		$this->checkConnection();
		return null;
	}
	
	protected function update($object, $editor, $objectClass = null) {
		if(is_null($object)) throw new Exception("Attenzione! Non hai inserito l'oggetto da modificare.");
		
		if(!is_null($objectClass) && is_string($objectClass))
			if(!is_a($object, $objectClass) && !is_subclass_of($object, $objectClass))
				throw new Exception("Attenzione! L'oggetto da modificare non è di tipo: " . $objectClass);
				
		if(!AuthorizationManager::canUserDo(AuthorizationManager::EDIT, $object) && is_subclass_of($object, "Editable"))
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
	
	abstract function exists($object);
	
	abstract function quickLoad($id);
	
	protected function updateState($object, $table, $id_column_name) {
		if(is_null($object)) throw new Exception("Attenzione! Non hai inserito l'oggetto da modificare.");		
		$this->checkConnection();
		
		$old = $this->quickLoad($object->getID());
		if(is_null($old))
			throw new Exception("L'oggetto da modificare non esiste.");
		
		$data = array();
		if(is_a($object, "Writable") || is_subclass_of($object,"Writable")) {
			if($object->hasAutoBlackContent() != $old->hasAutoBlackContent())
				$data[DB::AUTO_BLACK_CONTENT] = $object->hasAutoBlackContent();
			if($object->hasBlackContent() != $old->hasBlackContent())
				$data[DB::BLACK_CONTENT] = $object->hasBlackContent();
			if($object->hasYellowContent() != $old->hasYellowContent())
				$data[DB::YELLOW_CONTENT] = $object->hasYellowContent();
			if($object->hasRedContent() != $old->hasRedContent())
				$data[DB::RED_CONTENT] = $object->hasRedContent();
		}
		if(is_a($object, "Editable") || is_subclass_of($object, "Editable")) {
			if($object->getPreviousVersion() > $old->getPreviousVersion())
				$data[DB::PREVIOUS_VERSION] = $object->getPreviousVersion();
			if($object->isEditable() != $old->isEditable())
				$data[DB::EDITABLE] = $object->isEditable();
			if($object->isRemovable() != $old->isRemovable())
				$data[DB::REMOVABLE] = $object->isRemovable();
		}
		
		if(count($data) == 0) return $object;
		
		$this->db->execute(Query::generateUpdateStm($table, $data, new WhereConstraint($table->getColumn($id_column_name),Operator::EQUAL,$object->getID())), null, LOGMANAGER);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
		return $object;
	}
	
	protected function getAccessCount($object, $table, $id_column_name) {
		if(is_null($object)) throw new Exception("Attenzione! Non hai inserito l'oggetto da modificare.");		
		$this->checkConnection();
		
		$s = "SELECT " . DB::ACCESS_COUNT . " FROM " . $table->getName() . " WHERE " . $id_column_name . " = " . $object->getID();
		$this->db->execute($s, null, LOGMANAGER);
		//if($this->db->num_rows() != 1)
		//	throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		$row = $this->db->fetch_result();
		$n = intval($row[DB::ACCESS_COUNT]);
		
		//FIXME finché non è fatto bene non aggiorno...
		//$s = "UPDATE " . $table->getName() . " SET " . DB::ACCESS_COUNT . " = " . ++$n . " WHERE " . $id_column_name . " = " . $object->getID();
		//$this->db->execute($s, null, LOGMANAGER);
		//if($this->db->affected_rows() != 1)
		//	throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
		return $object;
	}
	
	/**
	 * Salva l'oggetto nella cartella di storicizzazione.
	 * @param Object $object l'oggetto da salvare
	 * @param User $editor l'utente che lo modifica
	 * @param string $operation l'operazione che è stata fatta (modifica o eliminazione)
	 * @throws Exception lancia eccezione se non è riuscito a salvare l'oggetto.
	 * @return l'id della riga salvata.
	 */
	protected function saveHistory($object, $editor, $operation) {
		$this->save($object);
		
		if(!is_a($editor, "User"))
			throw new Exception("Non hai settato chi ha fatto la modifica.");
		$modDate = $_SERVER["REQUEST_TIME"];
		$data = array(DB::HISTORY_OBJECT => serialize($object),
					  DB::HISTORY_DATE => date("Y/m/d G:i:s", $modDate),
					  DB::HISTORY_EDITOR => $editor->getID(),
					  DB::HISTORY_OPERATION => $operation);
		
		$this->db->execute(Query::generateInsertStm($this->historyTable, $data), $this->historyTable, $object);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Errore salvando la precedente vesrione dell'oggetto. Riprovare.");
		
		return $this->db->last_inserted_id();
	}
}

?>