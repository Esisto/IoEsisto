<?php
require_once 'session.php';

class AuthorizationManager {
	const CREATE = "create";
	const EDIT = "edit";
	
	static function canUserDo($operation, $object) {
		
		return true; //TODO
	}
}

?>