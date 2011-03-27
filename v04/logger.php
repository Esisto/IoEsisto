<?php

class Logger {
	private static $fileName = "log_";
	
	private static $instance;
	private $name;
	private $fileDescriptor;
	private $isWriting = false;
	
	static function getLogger() {
		if(self::$instance == null) self::$instance = new Logger("logger");
		return self::$instance;
	}
	
	private function __construct($name) {
		$this->name = $name;
		
		$this->fileDescriptor = fopen("log/" . self::$fileName . date("Ymd", time()), "a+");
	}
	
	function debug($clazz, $str) {
		while($isWriting);
		
		$isWriting = true;
		
		fwrite($this->fileDescriptor, date("Y-m-d H:i:s", time()) . " DEBUG-" . $clazz . ": " . $str);
		
		$isWriting = false;
	}
	
	function info($clazz, $str) {
		while($isWriting);
		
		$isWriting = true;
		
		fwrite($this->fileDescriptor, date("Y-m-d H:i:s", time()) . " INFO-" . $clazz . ": " . $str);
		
		$isWriting = false;
	}
	
	function error($clazz, $str) {
		while($isWriting);
		
		$isWriting = true;
		
		fwrite($this->fileDescriptor, date("Y-m-d H:i:s", time()) . " ERROR-" . $clazz . ": " . $str);
		
		$isWriting = false;
	}
	
	function warn($clazz, $str) {
		while($isWriting);
		
		$isWriting = true;
		
		fwrite($this->fileDescriptor, date("Y-m-d H:i:s", time()) . " WARN-" . $clazz . ": " . $str);
		
		$isWriting = false;
	}
}

?>