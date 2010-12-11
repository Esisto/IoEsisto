<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");
require_once("dataobject/Report.php");

class ReportDao implements Dao {
	const OBJECT_CLASS = "Report";
	
	function __construct() {
		parent::__construct();
		$this->setMainTable(DB::TABLE_REPORT);
	}
	
	function load($id) {
		parent::load($id);
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table),
																array(),
																array(new WhereConstraint($this->table->getColumn(DB::REPORT_ID),Operator::EQUAL,intval($id))),
																array()));
		
		if($this->db->num_rows() != 1)
			throw new Exception("L'oggetto cercato non è stato trovato.");
		
		$row = $this->db->fetch_result();
		$objectClass = $row[DB::REPORT_OBJECT_CLASS];
		$objectId = $row[DB::REPORT_OBJECT_ID];
		
		$object = $this->getObject($objectId, $objectClass);
		if(is_null($object))
			throw new Exception("L'oggetto cercato non è stato trovato.");
			
		$r = new Report($row[DB::REPORT_USER], $object, $row[DB::REPORT_TEXT]);
		return $r;
	}
	
	/**
	 * Carica da database tutti i report per l'oggetto passato e li salva nell'oggetto.
	 * @param Editable $object un oggetto di cui si possono caricare i report.
	 * @return l'oggetto aggiornato 
	 */
	function loadAll($object) {
		parent::load($object);
		$objectClass = get_class($object);
		
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table), array(),
								 array(new WhereConstraint($this->table->getColumn(REPORT_OBJECT_ID),Operator::EQUAL,$object->getID()),
								 	   new WhereConstraint($this->table->getColumn(REPORT_OBJECT_CLASS),Operator::EQUAL,get_class($object))), array()));

		$reports = array();
		while($row = $this->db->fetch_result())	{
			$report = new Report(intval($row[DB::REPORT_USER]), $object, $row[DB::REPORT_TEXT]);
			$report->setID($row[REPORT_ID]);
			$reports[] = $report;
		}
		
		return $object->setReports($reports);
	}
	
	function getCount($object) {
		parent::load($object);
		$objectClass = get_class($object);
		
		$rs = $this->db->execute($s = Query::generateSelectStm(array($this->table), array(),
								 array(new WhereConstraint($this->table->getColumn(REPORT_OBJECT_ID),Operator::EQUAL,$object->getID()),
								 	   new WhereConstraint($this->table->getColumn(REPORT_OBJECT_CLASS),Operator::EQUAL,get_class($object))),
								 array("count" => 2)));
		
		if($this->db->num_rows() != 1)
			throw new Exception("Si è verificato un errore. Riprovare.");
		
		$row = $this->db->fetch_row();
		return intval($row[0]);
	}
	
	function save($report) {
		parent::save($report, self::OBJECT_CLASS);

		$data = array(DB::REPORT_TEXT => $report->getReport(),
					  DB::REPORT_OBJECT_ID => $report->getObject()->getID(),
					  DB::REPORT_OBJECT_CLASS => get_class($report->getObject()),
					  DB::REPORT_USER => $report->getAuthor());
		
		$rs = $this->db->execute($s = Query::generateInsertStm($this->table,$data), $this->table->getName(), $report);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore. Riprovare.");
		
		//aggiorno lo stato dell'oggetto che è stato segnalato.
		$this->updateObjectState($report->getObject());
		
		return $this->db->quickLoad($report->getID());
	}
	
	function delete($report) {
		parent::delete($report, self::OBJECT_CLASS);
		
		$rs = $this->db->execute($s = Query::generateDeleteStm($this->table, array(new WhereConstraint($this->table->getColumn(DB::REPORT_ID),Operator::EQUAL,intval($report->getID())))),
							     $this->table->getName(), $report);
		
		if($this->db->affected_rows() != 1)
			throw new Exception("Si è verificato un errore eliminando il dato. Riprovare.");
		return $report;
	}

	function quickLoad($report) {
		return $this->load($report);
	}
	
	function updateObjectState($object) {
		$num = $this->getCount($object);
		
		require_once("settings.php");
		if($num >= MAXREPORTS) {
			//se sono stati superati i TOT report...
			if(is_subclass_of($object, "Writable")) {
				$object->setAutoBlackContent(true); //...si rende invisibile l'oggetto
				if(is_subclass_of($object, "Editable")) {
					$object->setEditable(false); //e si imperdisce all'autore di modificarlo
					$object->setRemovable(false); //o cancellarlo
				}
				//aggiorno lo stato dell'oggetto
				$objectDao = $this->getDaoForObject(get_class($object));
				$objectDao->updateState($object); //non uso update perché è non viene modificato l'oggetto in sé ma i permessi su di esso.
			}
		}
	}
	
	private function getDaoForObject($objectClass) {
		//carico il dao corretto
		require_once("dao/" . $objectClass . "Dao.php");
		if(!class_exists($objectClass . "Dao"))
			throw new Exception("La classe cercata non esiste!");
		$objectDao = null;
		switch($objectClass) {
			case "News":
			case "VideoReportage":
			case "Collection":
			case "PhotoReportage":
			case "Album":
			case "Playlist":
			case "Magazine":
			case "Post":
				$objectDao = new PostDao();
				break;
			case "Comment":
				$objectDao = new CommentDao();
				break;
			case "Resource":
				$objectDao = new ResourceDao();
				break;
			case "User":
				$objectDao = new UserDao();
				break;
			default:
				throw new Exception("La classe cercata non esiste!");
		}
		return $objectDao;
	}
	
	private function getObject($object_id, $objectClass) {
		$objectDao = $this->getDaoForObject($objectClass);
		$object = null;
		if(!is_null($objectDao))
			$object = $objectDao->quickLoad($object_id);
		return $object;
	}
}
?>