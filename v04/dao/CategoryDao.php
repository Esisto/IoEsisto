<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Category.php");

class CategoryDao extends Dao {
	const OBJECT_CLASS = "Category";
	private $table_sc;
	private $loadChildren = true;
	private $loadParent = true;
	private $loadAccessCount = true;

	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_CATEGORY);
		$this->table_sc = Query::getDBSchema()->getTable(DB::TABLE_SUB_CATEGORY);
	}
	
	function load($name) {
		parent::load($name);
		$this->db->execute(Query::generateSelectStm(array($this->table),
													array(),
													array(new WhereConstraint($this->table->getColumn(DB::CATEGORY_NAME),Operator::EQUAL,$name)),
													array()));
		
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $this->db->fetch_result();
		return $this->createFromDBRow($row);
	}
	
	private function createFromDBRow($row) {
		$cat = new Category($row[DB::CATEGORY_NAME]);
		$cat->setAuthorId($row[DB::CATEGORY_AUTHOR])
			->setCreationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $row[DB::CATEGORY_CREATION_DATE])));
		
		if($this->loadChildren)
			$this->loadChildren($cat);
		if($this->loadParent)
			$this->loadParentName($cat);
		if($this->loadAccessCount)
			$this->getAccessCount($cat);
		
		return $cat;
	}
	
	function quickLoad($name) {
		$loadC = $this->loadChildren; $this->setLoadChildren(false);
		$loadP = $this->loadParent; $this->setLoadParent(false);
		$loadA = $this->loadAccessCount; $this->loadAccessCount = false;
		try {
			$c = $this->load($name);
			$this->setLoadChildren($loadC);
			$this->setLoadParent($loadP);
			$this->loadAccessCount = $loadA;
			return $c;
		} catch (Exception $e) {
			$this->setLoadChildren($loadC);
			$this->setLoadParent($loadP);
			$this->loadAccessCount = $loadA;
			throw $e;
		}
	}
	
	function loadChildren($cat) {
		parent::load($cat);
		if(!is_a($cat, "Category"))
			throw new Exception("Attenzione! Il parametro di ricerca non è una categoria.");
		
		$this->db->execute(Query::generateSelectStm(array($this->table,$this->table_sc),
													array(new JoinConstraint($this->table->getColumn(DB::CATEGORY_NAME), $this->table_sc->getColumn(DB::SUB_CATEGORY_CATEGORY))),
													array(new WhereConstraint($this->table_sc->getColumn(DB::SUB_CATEGORY_PARENT), Operator::EQUAL, $cat->getName())), array()));
		
		$cats = array();
		$res = $this->db->fetch_all_results();
		foreach($res as $row)
			$cats[] = $this->createFromDBRow($row);

		$cat->setChildren($cats);
		return $cat;
	}
	
	function loadParentName($cat) {
		parent::load($cat);
		if(!is_a($cat, "Category"))
			throw new Exception("Attenzione! Il parametro di ricerca non è una categoria.");
		
		$this->db->execute($s = Query::generateSelectStm(array($this->table_sc), array(), array(new WhereConstraint($this->table_sc->getColumn(DB::SUB_CATEGORY_CATEGORY), Operator::EQUAL, $cat->getName())), array()));
		if($this->db->num_rows() == 1) {
			$row = $this->db->fetch_result();
			$cat->parent = $row[DB::SUB_CATEGORY_PARENT];
		}
		return $cat;
	}
	
	function exists($cat) {
		try {
			if(is_string($cat))
				$name = $cat;
			else
				$name = $cat->getName();
				
			$c = $this->quickLoad($name);
			return is_a($c, self::OBJECT_CLASS);
		} catch(Exception $e) {
			return false;
		}
	}
	
	function save($cat) {
		parent::save($cat, self::OBJECT_CLASS);
		
		$data = array();
		if(!is_null($cat->getName()))
			$data[DB::CATEGORY_NAME] = $cat->getName();
		if(!is_null($cat->getAuthorId()))
			$data[DB::CATEGORY_AUTHOR] = $cat->getAuthorId();
		if(!is_null($cat->getCreationDate()))
			$data[DB::CATEGORY_CREATION_DATE] = date("Y-m-d G:i:s", $cat->getCreationDate());
			
		$this->db->execute(Query::generateInsertStm($this->table, $data));
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		
		if(!is_null($cat->getParentName())) {
			$this->db->execute(Query::generateInsertStm($this->table_sc, array(DB::SUB_CATEGORY_CATEGORY => $cat->getName(), DB::SUB_CATEGORY_PARENT => $cat->getParentName())));
			if($this->db->affected_rows() != 1)
				throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		}
		return $cat;
	}
	
	function loadAllNew() { //utilizza solo una query!!!
		$loadA = $this->loadAccessCount; $this->loadAccessCount = false;
		parent::checkConnection();
			
		$s = "
		SELECT " . DB::TABLE_CATEGORY . ".* , NULL AS " . DB::SUB_CATEGORY_PARENT . ", " . DB::CATEGORY_NAME . " AS " . DB::SUB_CATEGORY_CATEGORY . "
		FROM  " . DB::TABLE_CATEGORY . "
		WHERE " . DB::CATEGORY_NAME . " NOT IN (
				SELECT DISTINCT " . DB::SUB_CATEGORY_CATEGORY . "
				FROM " . DB::TABLE_SUB_CATEGORY . ")
		UNION
		SELECT * 
		FROM " . DB::TABLE_CATEGORY . ", " . DB::TABLE_SUB_CATEGORY . "
		WHERE " . DB::CATEGORY_NAME . " = " . DB::SUB_CATEGORY_CATEGORY;
		 
		$this->db->execute($s);
			
		$cats = array(); //array di risultato
		$children = array(); //matrice dei figli
		$children = array(); //array di tutte le categorie
		$res = $this->db->fetch_all_results();
		
		$loadC = $this->loadChildren; $this->setLoadChildren(false);
		$loadP = $this->loadParent; $this->setLoadParent(false);
		$loadA = $this->loadAccessCount; $this->loadAccessCount = false;
		foreach($res as $row) {
			$cat = $this->createFromDBRow($row); //creo la categoria
			if(!isset($children[$cat->getName()]))
				$children[$cat->getName()] = array(); //creo un array per i suoi figli
			$allCats[] = $cat; //la aggiungo alla lista di tutte le categorie
				
			if(is_null($row[DB::SUB_CATEGORY_PARENT])) {
				$cats[] = $cat; //se non è figlia di nessuno la aggiungo all'array che verrà restituito 
			} else {
				$cat->setParentName($row[DB::SUB_CATEGORY_PARENT]); //setto il nome del padre
				if(!isset($children[$cat->getParentName()]))
					$children[$cat->getParentName()] = array(); //se non esiste un array dei figli per suo padre lo creo
				$children[$cat->getParentName()][] = $cat; //la aggiungo all'array dei figli di suo padre
			}
		}
		$this->setLoadChildren($loadC);
		$this->setLoadParent($loadP);
		$this->loadAccessCount = $loadA;
		
		foreach($allCats as $c)
			$c->setChildren($children[$c->getName()]); //setto i figli
		
		return $cats;
	}
	
	
	function loadAll() {
		$loadA = $this->loadAccessCount; $this->loadAccessCount = false;
		parent::checkConnection();
			
		$s = "SELECT * from " . DB::TABLE_CATEGORY . " WHERE " . DB::CATEGORY_NAME . " NOT IN (SELECT DISTINCT " . DB::SUB_CATEGORY_CATEGORY . " FROM " . DB::TABLE_SUB_CATEGORY . ")";
		$this->db->execute(Query::generateSelectStm(array($this->table,$this->table_sc),
													array(new JoinConstraint($this->table->getColumn(DB::CATEGORY_NAME), $this->table_sc->getColumn(DB::SUB_CATEGORY_CATEGORY))),
													array(), array()));
		$this->db->execute($s);
			
		$cat = array();
		$res = $this->db->fetch_all_results();
		foreach($res as $row)
			$cat[] = $this->createFromDBRow($row);
		return $cat;
	}
	
	function setLoadChildren($load) {
		settype($load, "boolean");
		$this->loadChildren = $load;
		return $this;
	}

	function setLoadParent($load) {
		settype($load, "boolean");
		$this->loadParent = $load;
		return $this;
	}
	
	protected function getAccessCount($cat) {
		parent::getAccessCount($cat, $this->table, DB::CATEGORY_NAME);
	}
}
?>