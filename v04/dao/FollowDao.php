<?php //TODO

class FollowDao extends Dao {
	
	function exists($object) {
		
	}
	
	function quickLoad($id) {
		
	}
	
	function loadAllFollowers($user) {
		
	}

	function loadAllFollows($user) {
		
	}
	
	function loadFollowers() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFollowColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FOLLOW);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(FOLLOW_SUBJECT), Operator::EQUAL, $this->getID())),
												   array()));
			if($db->num_rows() > 0) {
				$fols = array();
				while($row = $db->fetch_result()) {
					$f = self::loadFromDatabase(intval($row[FOLLOW_FOLLOWER]), false);
					if($f !== false)
						$fols[$f->getID()] = $f;
				}
				return $this->setFollowers($fols);
			} else {
				if($db->errno())
					$db->display_error("User::loadFollowers()");
			}
		} else $db->display_connect_error("User::loadFollowers()");
		return $this->setFollowers(array());
	}
	
	function loadFollows() {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFollowColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FOLLOW);
			
			$db->execute($s = Query::generateSelectStm(array($table), array(),
												   array(new WhereConstraint($table->getColumn(FOLLOW_FOLLOWER), Operator::EQUAL, $this->getID())),
												   array()));
			
			if($db->num_rows() > 0) {
				$fols = array();
				while($row = $db->fetch_result()) {
					define_tables(); defineFollowColumns();
					$f = self::loadFromDatabase(intval($row[FOLLOW_SUBJECT]), false);
					if($f !== false)
						$fols[$f->getID()] = $f;
				}
				return $this->setFollows($fols);
			} else {
				if($db->errno())
					$db->display_error("User::loadFollows()");
			}
		} else $db->display_connect_error("User::loadFollows()");
		return $this->setFollows(array());
	}

	function stopFollowing($user) {
		if($user->removeFollower($this) !== false) {
			$user->loadFollows();
			return $this->loadFollows();
		}
		return false;
	}

	function follow($user) {
		$f = $user->getFollowers();
		if(!isset($f[$this->getID()]))
			$user->addFollower($this);
		return $this->loadFollows();
	}

	function removeFollower($user) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFollowColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FOLLOW);
			
			$db->execute($s = Query::generateDeleteStm($table, array(new WhereConstraint($table->getColumn(FOLLOW_FOLLOWER), Operator::EQUAL, $user->getID()),
																 new WhereConstraint($table->getColumn(FOLLOW_SUBJECT), Operator::EQUAL, $this->getID()))),
						$table->getName(), $this);
			
			if($db->affected_rows() > 1)
				$db->display_error("User::removeFollower()"); //Genera un errore ma ritorna comunque $this
		} else $db->display_connect_error("User::removeFollower()");
		return $this->loadFollowers();
	}

	function addFollower($user) {
		require_once("query.php");
		$db = new DBManager();
		if(!$db->connect_errno()) {
			define_tables(); defineFollowColumns();
			$table = Query::getDBSchema()->getTable(TABLE_FOLLOW);
			
			$db->execute($s = Query::generateInsertStm($table, array(FOLLOW_FOLLOWER => $user->getID(),
																 FOLLOW_SUBJECT => $this->getID())),
						$table->getName(), $this);
			
			if($db->affected_rows() != 1)
				$db->display_error("User::addFollower()"); //Genera un errore ma ritorna comunque $this
		} else $db->display_connect_error("User::addFollower()");
		return $this->loadFollowers();
	}
}
?>