<?php
require_once("post/contest/Contest.php");

class ContestManager {
	
	static function createContest($data) {
		require_once("common.php");
		$data = Filter::filterArray($data);
		
		$c = new Contest($data);
		$c->save();
		return $c;		
	}
	
	static function editContest($contest, $data) {
		require_once("common.php");
		$data = Filter::filterArray($data);
		
		if(isset($data["title"]))
			$contest->setTitle($data["title"]);
		if(isset($data["description"]))
			$contest->setDescription($data["description"]);
		if(isset($data["rules"]))
			$contest->setRules($data["rules"]);
		if(isset($data["prizes"]))
			$contest->setPrizes($data["prizes"]);
		if(isset($data["start"]))
			$contest->setStart($data["start"]);
		if(isset($data["end"]))
			$contest->setEnd($data["end"]);
			
		$contest->update();
		
		return $contest;
	}
	
	static function deleteContest($contest) {
		return $contest->delete();
	}

	static function subscribePostToContest($post, $contest) {
		return $contest->subscribePost($post);
	}
	
	static function unsubscribePostFromContest($post, $contest) {
		return $contest->unsubscribePost($post);
	}

	static function loadContest($id) {
		return Contest::loadFromDatabase($id);
	}
	
}

?>