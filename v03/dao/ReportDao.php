<?php
require_once 'dao/Dao.php';
require_once("db.php");
require_once("query.php");

class ReportDao implements Dao {
	private $db;
	private $table_report;
	
	function __construct() {
		$this->table_report = Query::getDBSchema()->getTable(DB::TABLE_REPORT);
		
		$this->db = new DBManager();
		if($this->db->connect_errno())
			$this->db->display_connect_error("ReportDao::__construct()");
	}
	
	function load($id) {
		//TODO
	}
	
	function loadAll($object) { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineReportColumns();
			$table = Query::getDBSchema()->getTable(TABLE_REPORT);
			$rs = $db->execute($s = Query::generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn(REPORT_POST),Operator::EQUAL,$this->getID())),
														 array()),
							  $table->getName(), $this);
			if($rs !== false) {
				$reports = array();
				while($row = $db->fetch_result()) {
					require_once("common.php");
					$report = new Report(intval($row[REPORT_USER]), intval($row[REPORT_POST]), $row[REPORT_TEXT]);
					$report->setID($row[REPORT_ID]);
					$reports[] = $report;
				}
				$this->setReports($reports);
			} else {
				if($db->errno())
					$db->display_error("Post::loadReports()");
			}
		} else $db->display_connect_error("Post::loadReports()");
		return $this;
	}
	
	function getCount($object) {
		//TODO
	}
	
	function save() { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_REPORT);
			$data = array(REPORT_TEXT => $this->getReport(),
						  REPORT_POST => $this->getPost(),
						  REPORT_USER => $this->getAuthor());
			$rs = $db->execute($s = Query::generateInsertStm($table,$data), $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			//echo "<br />" . serialize($rs); //DEBUG
			if($db->affected_rows() == 1) {
				$this->ID = $db->last_inserted_id();
				//echo "<br />" . $this; //DEBUG
				return $this->getID();
			} else $db->display_error("Report::save()");
		} else $db->display_connect_error("Report::save()");
		return false;
	}
	
	function delete() { //TODO
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			$table = Query::getDBSchema()->getTable(TABLE_REPORT);
			$rs = $db->execute($s = Query::generateDeleteStm($table,
														 array(new WhereConstraint($table->getColumn(REPORT_ID),Operator::EQUAL,$this->getID()))),
							  $table->getName(), $this);
			//echo "<br />" . $s; //DEBUG
			if($db->affected_rows() == 1) {
				return $this;
			} else $db->display_error("Report::delete()");
		} else $db->display_connect_error("Report::delete()");
		return false;			
	}
	
}
?>