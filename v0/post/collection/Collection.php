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
		
		function addPost($post){
			if(isset($this->content)&& is_array($this->content))
				$this->content[] = $post;
			else
				$this->setContent(array($post));
				
			$this->save(SavingMode::$UPDATE);
			
			return $this;
		}
		
		/**
		 * Do nothing. Use addPost() instead.
		 * @deprecated @Override
		 */
		function setContent($content) {
			return null;
		}
	}
	
	class Album extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->collectionType(CollectionType::$ALBUM);
		}
		
		/**
		 * @Override
		 * Controlla inoltre che il post da aggiungere sia un PostType::$PHOTOREPORTAGE.
		 */
		function addPost($photo){
			if($photo->getType()==PostType::$PHOTOREPORTAGE){
				parent::addPost($photo);
			}
			
			return $this;
		}
	}
	
	class Magazine extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->collectionType(CollectionType::$MAGAZINE);
		}
		
		/**
		 * @Override
		 * Controlla inoltre che il post da aggiungere sia un PostType::$NEWS.
		 */
		function addPost($news){
			if($news->getType()==PostType::$NEWS){
				parent::addPost($news);
			}
			
			return $this;
		}
	}
	
	class Playlist extends Collection {
		
		function __construct() {
			parent::__construct();
			$this->collectionType(CollectionType::$PLAYLIST);
		}
		
		/**
		 * @Override
		 * Controlla inoltre che il post da aggiungere sia un PostType::$VIDEOREPORTAGE.
		 */
		function addPost($video){
			if($video->getType()==PostType::$VIDEOREPORTAGE){
				parent::addPost($video);
			}
			
			return $this;
		}
	}

?>
