<?php

interface Dao {
	
	static function load($id, $options);
	
	static function save($object);
	
	static function update($object);
}

?>