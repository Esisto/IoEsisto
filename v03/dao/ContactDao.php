<?php //TODO
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");

class ContactDao implements Dao {
	private $db;
	private $table_contact;
	private $table_contact_type;
	
	function __construct() {
		$this->table_contact = Query::getDBSchema()->getTable(DB::TABLE_CONTACT);
		$this->table_contact_type = Query::getDBSchema()->getTable(DB::TABLE_CONTACT_TYPE);
		
		$this->db = new DBManager();
		if($this->db->connect_errno())
			$this->db->display_connect_error("ContactDao::__construct()");
	}

	function save() { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			$data = array(CONTACT_CONTACT => $this->getContact(),
						  CONTACT_NAME => $this->getName(),
						  CONTACT_USER => $this->getUser());
			
			$db->execute($s = Query::generateInsertStm($table, $data), $table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			if($db->affected_rows() == 1) {
				$this->setID($db->last_inserted_id());
				
				return $this;
			} else $db->display_error("Contact::save()");
		} else $db->display_connect_error("Contact::save()");
		return false;
	}
	
	function update() { //TODO
		$old = self::loadFromDatabase($this->getID());
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			
			$data = array();
			if($this->getContact() != $old->getContact())
				$data[CONTACT_CONTACT] = $this->getContact();
			if($this->getName() != $old->getName())
				$data[CONTACT_NAME] = $this->getName();
				
			$db->execute($s = Query::generateUpdateStm($table, $data, array(new WhereConstraint($table->getColumn(CONTACT_ID), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Contact::update()");
		} else $db->display_connect_error("Contact::update()");
		return false;
	}
	
	function delete() { //TODO
        require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			
			$db->execute($s = Query::generateDeleteStm($table, array(new WhereConstraint($table->getColumn(CONTACT_ID), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			
			//echo "<p>" . $s . "</p>"; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Contact::delete()");
		} else $db->display_connect_error("Contact::delete()");
		return false;
	}
	
	static function loadFromDatabase($id) { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns(); defineContactTypeColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			$table1 = Query::getDBSchema()->getTable(TABLE_CONTACT_TYPE);
			
			$db->execute($s = Query::generateSelectStm(array($table, $table1),
												   array(new JoinConstraint($table->getColumn(CONTACT_NAME), $table1->getColumn(CONTACT_TYPE_NAME))),
												   array(new WhereConstraint($table->getColumn(CONTACT_ID), Operator::EQUAL, $id)),
												   array()));
			
			if($db->num_rows() == 1) {
				$row = $db->fetch_result();
				$data = array(NAME => $row[CONTACT_NAME],
							  CONTACT => $row[CONTACT_CONTACT],
							  USER => $row[CONTACT_USER]);
				
				$c = new Contact($data);
				$c->setType($row[CONTACT_TYPE_TYPE]);
				return $c->setID(intval($row[CONTACT_ID]));
			} else $db->display_error("Contact::loadFromDatabase()");
		} else $db->display_connect_error("Contact::loadFromDatabase()");
		return false;
	}
	
	static function loadContactsForUser($userid) { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactColumns(); defineContactTypeColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT);
			$table1 = Query::getDBSchema()->getTable(TABLE_CONTACT_TYPE);
			
			$rs = $db->execute($s = Query::generateSelectStm(array($table, $table1),
												   array(new JoinConstraint($table->getColumn(CONTACT_NAME), $table1->getColumn(CONTACT_TYPE_NAME))),
												   array(new WhereConstraint($table->getColumn(CONTACT_USER), Operator::EQUAL, $userid)),
												   array()));
			
			//echo "<p>" . mysql_affected_rows() . mysql_num_rows($rs) . $s . "</p>"; //DEBUG
			$conts = array();
			if($db->num_rows() > 0) {
				while($row = $db->fetch_result()) {
					$data = array(NAME => $row[CONTACT_NAME],
								  CONTACT => $row[CONTACT_CONTACT],
								  USER => $row[CONTACT_USER]);
					
					$c = new Contact($data);
					$c->setType($row[CONTACT_TYPE_TYPE]);
					$c->setID(intval($row[CONTACT_ID]));
					$conts[] = $c;
				}
			} else  {
				if($db->errno())
					$db->display_error("Contact::loadContactsForUser()");
			}
			return $conts;
		} else $db->display_connect_error("Contact::loadContactsForUser()");
	}
	
	static function getContactsNames() { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineContactTypeColumns();
			$table = Query::getDBSchema()->getTable(TABLE_CONTACT_TYPE);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(), array(), array()));
			
			$names = array();
			if($db->num_rows() > 0) {
				while($row = $db->fetch_result()) {
					$names[$row[CONTACT_TYPE_NAME]] = $row[CONTACT_TYPE_TYPE];
				}
			} else  {
				if($db->errno())
					$db->display_error("Contact::getContactsNames()");
			}
			return $names;
		} else $db->display_connect_error("Contact::getContactsNames()");
	}
}
?>