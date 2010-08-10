<?php

	class Collection extends Post {
		static $cTypes = array("ALBUM" => "album",
							   "MAGAZINE" => "magazine",
							   "PLAYLIST" => "playlist");
		protected $collectionType;
		
		function __construct() {
			$this->postType = Post::$pTypes["COLLECTION"];
		}
		
		
	}
	
	class Album extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->collectionType = Collection::$cTypes["ALBUM"];
		}
		
	}
	
	class Magazine extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->collectionType = Collection::$cTypes["MAGAZINE"];
		}
		
	}
	
	class Playlist extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->collectionType = Collection::$cTypes["PLAYLIST"];
		}
		
	}

?>
