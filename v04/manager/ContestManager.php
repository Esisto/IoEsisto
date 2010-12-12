<?php
require_once("dataobject/Contest.php");
require_once("dao/ContestDao.php");
require_once 'filter.php';

class ContestManager {	
	static function subscribePostToContest($post, $contest) {
		$contestdao = new ContestDao();
		return $contestdao->subscribePost($post, $contest);
	}
	
	static function unsubscribePostFromContest($post, $contest) {
		$contestdao = new ContestDao();
		return $contestdao->unsubscribePost($post, $contest);
	}

	static function loadContest($id) {
		$contestdao = new ContestDao();
		return $contestdao->load($id);
	}
	
	static function createContest($data) {
		$data = Filter::filterArray($data);
		$cont = new Contest($data);
		$contestdao = new ContestDao();
		return $contestdao->save($cont);
	}
	
	static function editContest($contest, $data) {
		$data = Filter::filterArray($data);
		$contest->edit($data);
		$contestdao = new ContestDao();
		return $contestdao->update($contest, Session::getUser());
	}
}

?>