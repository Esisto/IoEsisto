<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Resource.php");

class ResourceDao implements Dao {
	const OBJECT_CLASS = "Resource";
	private $loadReports = false;
	
	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_RESOURCE);
	}
	
	function load($id) {
		parent::load($id);
		$this->db->execute(Query::generateSelectStm(array($this->table), array(),
									array(new WhereConstraint($this->table->getColumn(DB::RESOURCE_ID),Operator::EQUAL,intval($resource->getID()))),
									array()));
		
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato. Riprovare.");
		
		$row = $db->fetch_result();
		return $this->createFromDBRow($row);
	}
	
	function loadList($ids) {
		parent::load($ids);
		if(!is_array($ids)) $ids = array($ids);
		$s = "SELECT * FROM " . $this->table->getName() . " WHERE " . DB::RESOURCE_ID . " IN (" . Filter::arrayToText($ids, ", ") . ")";
		$this->db->execute($s);
		
		$resources = array();
		
		while($row = $db->fetch_result()) {
			$resources[] = $this->createFromDBRow($row);
		}
		return $resources;
	}
	
	function loadForAuthor($author) {
		parent::load($ids);
		if(!is_array($ids)) $ids = array($ids);
		$this->db->execute(Query::generateSelectStm(array($this->table), array(),
									array(new WhereConstraint($this->table->getColumn(DB::RESOURCE_OWNER),Operator::EQUAL,intval($resource->getOwnerId()))),
									array()));
				
		$resources = array();
		
		while($row = $db->fetch_result()) {
			$resources[] = $this->createFromDBRow($row);
		}
		return $resources;
		}
	
	private function createFromDBRow($row) {
		$r = new Resource($row[DB::RESOURCE_OWNER], $row[DB::RESOURCE_PATH], $row[DB::RESOURCE_TYPE]);
		$r->setDescription($row[DB::RESOURCE_DESCRIPTION])
				->setCreationDate($row[DB::RESOURCE_CREATION_DATE])
				->setTags($row[DB::RESOURCE_TAGS]);
		if(!is_null($row[DB::RESOURCE_MODIFICATION_DATE]))
			$mod = $row[DB::RESOURCE_MODIFICATION_DATE];
		else
			$mod = $row[DB::RESOURCE_CREATION_DATE];
		$r->setModificationDate(date_timestamp_get(date_create_from_format("Y-m-d G:i:s", $mod)));
		//setto lo stato
		$r->setEditable($row[DB::EDITABLE])->setRemovable($row[DB::REMOVABLE]);
		$r->setBlackContent($row[DB::BLACK_CONTENT])
				->setRedContent($row[DB::RED_CONTENT])
				->setYellowContent($row[DB::YELLOW_CONTENT])
				->setAutoBlackContent($row[DB::AUTO_BLACK_CONTENT]);
		
		$user = Session::getUser();
		if($this->loadReports && $user->isEditor()) { //FIXME usa authorizationManager o roleManager
			require_once 'dao/ReportDao.php';
			$reportDao = new ReportDao();
			$reportDao->loadAll($r);
		}
		$r->setAccessCount($this->getAccessCount($r));
		return $r;
	}
	
	function save($resource) {
		parent::save($resource, self::OBJECT_CLASS);
		
		$data = array(RESOURCE_OWNER => $resource->getOwnerId(),
					  RESOURCE_PATH => $resource->getPath(),
					  RESOURCE_TYPE => $resource->getType());
		if(!is_null($resource->getDescription()))
			$data[RESOURCE_DESCRIPTION] = $resource->getDescription();
		if(!is_null($resource->getTags()))
			$data[DB::RESOURCE_TAGS] = $resource->getTags();
		$data[DB::POST_CREATION_DATE] = date("Y-m-d G:i:s", $_SERVER["REQUEST_TIME"]);
			
		$rs = $db->execute($s = Query::generateInsertStm($table,$data), $table->getName(), $this);
		if($db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore salvando l'oggetto. Riprovare.");
		
		$r = $this->quickLoad($this->db->last_inserted_id());
		//inserisco i tag nuovi
		if(!is_null($resource->getTags()) && trim($resource->getTags()) != "")
			TagManager::createTags(explode(",", $resource->getTags()));
		
		return $r;
	}
	
	function delete($resource) {
		parent::delete($resource, self::OBJECT_CLASS);
		
		//carico la risorsa, completa dei suoi derivati (che andrebbero persi).
		$loadR = $this->loadReports; $this->loadReports = true;
		$r_complete = null;
		try {
			$r_complete = $this->load($resource->getID());
			$this->loadReports = $loadR;
		} catch(Exception $e) {
			$this->loadReports = $loadR;
			throw $e;
		}
		
		$rs = $db->execute($s = Query::generateDeleteStm($table,
								array(new WhereConstraint($table->getColumn(RESOURCE_ID),Operator::EQUAL,$this->getID()))),
							  	$table->getName(), $this);
		
		//salvo la risorsa nella storia.
		$this->saveHistory($r_complete, "DELETED");
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore eliminando il dato. Riprovare.");
		return $resource;			
	}
	
	function update($resource, $editor) {
		parent::update($resource, $editor, self::OBJECT_CLASS);
		if(!is_a($editor, "User"))
			throw new Exception("Non hai settato chi ha fatto la modifica.");
		
		$r_old = $this->quickLoad($resource->getID());
		if(is_null($p_old))
			throw new Exception("L'oggetto da modificare non esiste.");
		
		$data = array();
		//cerco le differenze POSSIBILI e le salvo.
		if($r_old->setDescription() != $resource->setDescription())
			$data[DB::RESOURCE_DESCRIPTION] = $resource->setDescription();
		if($r_old->getTags() != $resource->getTags())
			$data[DB::RESOURCE_TAGS] = $resource->getTags();
		$modDate = $_SERVER["REQUEST_TIME"];
		$data[DB::POST_MODIFICATION_DATE] = date("Y/m/d G:i:s", $modDate);
		
		//salvo la versione precedente e ne tengo traccia.
		$history_id = $this->saveHistory($r_old, "UPDATED");
		$resource->setPreviousVersion($history_id);
		$data[DB::POST_PREVIOUS_VERSION] = $resource->getPreviousVersion();
		
		$rs = $this->db->execute($s = Query::generateUpdateStm($this->table, $data,
									array(new WhereConstraint($this->table->getColumn(DB::RESOURCE_ID),Operator::EQUAL,$resource->getID()))),
									$this->table->getName(), $resource);
		//aggiorno lo stato della risorsa (se chi l'ha modificata è un redattore).
		if($editor->isEditor()) { //TODO usa authorization manager
			$resource->setEditable(false);
			$resource->setRemovable(false);
			$this->updateState($resource);
		}
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore aggiornando il dato. Riprovare.");
		//salvo i tag che non esistono
		if(!is_null($resource->getTags()) && trim($resource->getTags()) != "")
			TagManager::createTags(explode(",", $resource->getTags()));
		
		return $resource->setModificationDate($modDate);
	}
		
	function exists($resource) {
		//TODO
	}
	
	function quickLoad($id) {
		$loadR = $this->loadReports; $this->loadReports = false;
		$r = null;
		try {
			$r = $this->load($id);
			$this->loadReports = $loadR;
		} catch(Exception $e) {
			$this->loadReports = $loadR;
			throw $e;
		}
		return $r;
	}
	
	function updateState($resource) {
		//TODO
	}
	
	function setLoadReports($loadReports) {
		settype($load, "boolean");
		$this->loadReports = $load;
		return $this;
	}

	private function getAccessCount($post) {
		//TODO
		//aggiunge 1 all'accesscount e aggiorna il db.
		//restituisce il conto.
		//questo fino all'arrivo di googleanalitics
	}
}
?>