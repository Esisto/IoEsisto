<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Contact.php");

class ContactDao implements Dao {
	const OBJECT_CLASS = "Contact";
	private $table_ct;

	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_CONTACT);
		$this->table_ct = Query::getDBSchema()->getTable(DB::TABLE_CONTACT_TYPE);
	}
	
	function load($id) {
		parent::load($id);
		
		$this->db->execute($s = Query::generateSelectStm(array($this->table, $this->table_ct),
														 array(new JoinConstraint($this->table->getColumn(DB::CONTACT_NAME), $this->table_ct->getColumn(DB::CONTACT_TYPE_NAME))),
														 array(new WhereConstraint($this->table->getColumn(DB::CONTACT_ID), Operator::EQUAL, intval($id))),
														 array()));
			
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $this->db->fetch_result();
		return $this->createFromDBRow($row);
	}
	
	function loadAll($user) {
		parent::load($id);
		if(is_numeric($user)) {
			$id = $user;
		} else {
			if(!is_subclass_of($post, "User"))
				throw new Exception("Attenzione! Il parametro di ricerca non è un utente.");
			$id = $user->getID();
		}
			
		$this->db->execute(Query::generateSelectStm(array($this->table, $this->table_ct),
													array(new JoinConstraint($this->table->getColumn(DB::CONTACT_NAME), $this->table_ct->getColumn(DB::CONTACT_TYPE_NAME))),
													array(new WhereConstraint($this->table->getColumn(CONTACT_USER), Operator::EQUAL, intval($id))),
													array()));
			
		$conts = array();
		while($row = $this->db->fetch_result())
			$conts[] = $this->createFromDBRow($row);
		return $conts;
	}
	
	private function createFromDBRow($row) {
		$data = array(Contact::NAME => $row[DB::CONTACT_NAME],
					  Contact::CONTACT => $row[DB::CONTACT_CONTACT],
					  Contact::TYPE => $row[DB::CONTACT_TYPE_TYPE],
					  Contact::USER => $row[DB::CONTACT_USER]);
				
		$c = new Contact($data);
		return $c->setID(intval($row[DB::CONTACT_ID]));
	}

	function save($contact) {
		parent::save($contact, self::OBJECT_CLASS);
		
		$data = array(DB::CONTACT_CONTACT => $contact->getContact(),
					  DB::CONTACT_NAME => $contact->getName(),
					  DB::CONTACT_USER => $contact->getUserId());
			
		$this->db->execute($s = Query::generateInsertStm($this->table, $data), $this->table->getName(), $contact);
			
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");

		$c = $this->quickLoad($this->db->last_inserted_id());
		return $c;
	}
	
	function update($contact, $editor) {
		parent::update($resource, $editor, self::OBJECT_CLASS);
		
		$old = $this->quickLoad($resource->getID());
		if(is_null($r_old))
			throw new Exception("L'oggetto da modificare non esiste.");
		
		$data = array();
		if($contact->getContact() != $old->getContact())
			$data[DB::CONTACT_CONTACT] = $contact->getContact();
		if($contact->getName() != $old->getName())
			$data[DB::CONTACT_NAME] = $contact->getName();
			
		$this->db->execute($s = Query::generateUpdateStm($this->table, $data, array(new WhereConstraint($this->table->getColumn(DB::CONTACT_ID), Operator::EQUAL, $contact->getID()))),
							$this->table->getName(), $contact);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
			
		return $contact;
	}
	
	function delete($contact) {
		parent::delete($contact, self::OBJECT_CLASS);
		
		$this->db->execute(Query::generateDeleteStm($this->table,
													array(new WhereConstraint($this->table->getColumn(DB::CONTACT_ID),Operator::EQUAL,$contact->getID()))),
							$this->table->getName(), $contact);
		
		//salvo la risorsa nella storia.
		$this->saveHistory($contact, "DELETED");
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore eliminando il dato. Riprovare.");
		return $contact;
	}
	
	function getContactsNames() {
		$this->checkConnection();
		
		$this->db->execute($s = Query::generateSelectStm(array($this->table_ct), array(), array(), array()));
			
		$names = array();
		while($row = $this->db->fetch_result())
			$names[$row[DB::CONTACT_TYPE_NAME]] = $row[DB::CONTACT_TYPE_TYPE];
		
		return $names;
	}
}
?>