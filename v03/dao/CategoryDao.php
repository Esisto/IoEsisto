<?php //TODO
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Category.php");

class ResourceDao implements Dao {
	const OBJECT_CLASS = "Category";
	private $table_sc;
	private $loadChildren = false;

	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_CATEGORY);
		$this->table_sc = Query::getDBSchema()->getTable(DB::TABLE_SUB_CATEGORY);
	}
	
	function load($name) {
		parent::load($name);
		$this->db->execute(Query::generateSelectStm(array($this->table), array(),
									array(new WhereConstraint($this->table->getColumn(DB::CATEGORY_NAME),Operator::EQUAL,intval($resource->getID()))),
									array()));
		
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $db->fetch_result();
		return $this->createFromDBRow($row);
	}
	
}
?>