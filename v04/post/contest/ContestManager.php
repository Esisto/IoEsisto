<?php
require_once("post/contest/Contest.php");

class ContestManager {	
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