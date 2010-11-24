<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");

class ResourceDao implements Dao {
	private $db;
	private $table_resource;
	
	function __construct() {
		$this->table_resource = Query::getDBSchema()->getTable(DB::TABLE_RESOURCE);
		
		$this->db = new DBManager();
		if($this->db->connect_errno())
			$this->db->display_connect_error("ResourceDao::__construct()");
	}
	
	function save() { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_RESOURCE);
			$data = array(RESOURCE_OWNER => $this->getOwner(),
						  RESOURCE_PATH => $this->getPath(),
						  RESOURCE_TYPE => $this->getType());
			$rs = $db->execute($s = Query::generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			if($db->affected_rows() == 1) {
				$this->setID($db->last_inserted_id());
				//echo "<br />" . $this; //DEBUG
				return $this->getID();
			} else $db->display_error("Resource::save()");
		} else $db->display_connect_error("Resource::save()");
		return false;
	}
	
	function delete() { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_RESOURCE);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(RESOURCE_ID),Operator::EQUAL,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Resource::delete()");
		} else $db->display_connect_error("Resource::delete()");
		return false;			
	}
}
?>