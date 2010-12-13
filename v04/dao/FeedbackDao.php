<?php //TODO

class FeedbackDao extends Dao {

	function exists($object) {
		
	}
	
	function quickLoad($id) {
		
	}
	
	function loadAll($user) {
		
	}
	
	function loadFeedback() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFeedbackColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FEEDBACK);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(FEEDBACK_SUBJECT), Operator::EQUAL, $this->getID())),
												   array()));
			if($db->num_rows() > 0) {
				require_once("strings/strings.php");
				$fb = FEEDBACK_INITIAL_VALUE;
				while($row = $db->fetch_result()) {
					$fb+= (intval($row[FEEDBACK_VALUE]) > 0 ? 1 : -1); //se sul DB è 0 allora è -1 se è positivo allora +1;
				}
				return $this->setFeedback($fb);
			} else if($db->errno()) $db->display_error("User::loadFeedback()");
		} else $db->display_connect_error("User::loadFeedback()");
		return $this->setFeedback(FEEDBACK_INITIAL_VALUE);
	}
	
	function addFeedbackFrom($user, $value) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFeedbackColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FEEDBACK);
			
			$db->execute($s = Query::generateInsertStm($table, array(FEEDBACK_CREATOR => $user->getID(),
																 FEEDBACK_SUBJECT => $this->getID(),
																 FEEDBACK_VALUE => ($value ? 1 : 0))),
						$table->getName(), $this);
			
			if($db->affected_rows() != 1)
				$db->display_error("User::addFeedbackFrom()"); //Genera un errore ma ritorna comunque $this
		} else $db->display_connect_error("User::addFeedbackFrom()");
		return $this->loadFeedback();
	}
}
?>