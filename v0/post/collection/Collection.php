<?php
	class CollectionType {
		static $ALBUM = "album";
		static $MAGAZINE = "magazine";
		static $PLAYLIST = "playlist";
	}

	class Collection extends Post {
		protected $collectionType;
		
		function __construct($data) {
			parent::__construct($data);
			if(isset($data["content"]) && !is_array($data["content"]))
				$this->setContent(array($data["content"]));
			$this->setPostType(PostType::$COLLECTION);
		}
		
		function getCollectionType() {
			return $this->collectionType;
		}
		
		function setCollectionType($type) {
			$this->collectionType = $type;
		}
		
		function addPost($post){
			if(isset($this->content)&& is_array($this->content))
				$this->content[] = $post;
			else
				$this->setContent(array($post));
				
			$this->save(SavingMode::$UPDATE);
		}
		
		function setContent($content) {
			
		}
	}
	
	class Album extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->setCollectionType(CollectionType::$ALBUM);
		}
		
		function addPost($photo){
			if($photo->getType()==PostType::$PHOTOREPORTAGE){
				parent::addPost($photo);
			}
		}
	}
	
	class Magazine extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->setCollectionType(CollectionType::$MAGAZINE);
		}
		
		function addPost($news){
			if($news->getType()==PostType::$NEWS){
				parent::addPost($news);
			}
		}
	}
	
	class Playlist extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->setCollectionType(CollectionType::$PLAYLIST);
		}
		
		function addPost($video){
			if($video->getType()==PostType::$VIDEOREPORTAGE){
				parent::addPost($video);
			}
		}
	}

?>
