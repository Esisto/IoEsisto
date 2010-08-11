<?php
	class CollectionType {
		static $ALBUM = "album";
		static $MAGAZINE = "magazine";
		static $PLAYLIST = "playlist";
	}

	class Collection extends Post {
		protected $collectionType;
		
		/**
		 * @Override
		 */
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
			if(isset($this->content) && is_array($this->content)) {
				$this->content[] = $post;
			} else {
				parent::setContent(array($post));
			}
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
		
		/**
		 * @Override
		 * Salva la collezione nel database.
		 * 
		 * param savingMode: uno dei valori della classe SavingMode.
		 * se INSERT: crea una nuova tupla in Post.
		 * se UPDATE: confronta il Post con quello presente nel database e aggiorna le differenze.
		 */
		function save($savingMode) {
			if($savingMode == SavingMode::$INSERT) {
				
				// TODO
				return true;
			} else if($savingMode == SavingMode::$UPDATE) {
				
				// TODO
				return true;	
			}
			
			return false;
		}
		
		/**
		 * @Override
		 */
		function __toString() {
			$s = "Post (ID = " . $this->ID .
				 " | postType = " . $this->postType .
				 " | title = " . $this->title .
				 " | subtitle = " . $this->subtitle .
				 " | headline = " . $this->headline .
				 " | author = " . $this->author .
				 " | creationDate = " . date("d/m/Y G:i", $this->creationDate) .
				 " | tags = (";
			for($i=0; $i<count($this->tags); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->tags[$i];
			}
			$s.= ") | categories = (";
			for($i=0; $i<count($this->categories); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->categories[$i];
			}
			$s.= ") | comments = (";
			for($i=0; $i<count($this->comments); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->comments[$i];
			}
			$s.= ") | votes = (";
			for($i=0; $i<count($this->votes); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->votes[$i];
			}
			$s.= ") | content = (";
			for($i=0; $i<count($this->content); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->content[$i];
			}
			$s.= ") | visible = " . $this->visible .
				 " | signals = (";
			for($i=0; $i<count($this->signals); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->signals[$i];
			}
			$s.= "))";
			return $s;
		}
	}
	
	class Album extends Collection {
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->collectionType = collectionType::$ALBUM;
		}
		
		/**
		 * @Override
		 * Controlla inoltre che il post da aggiungere sia un PostType::$PHOTOREPORTAGE.
		 */
		function addPost($photo){
			if($photo->getType()==PostType::$PHOTOREPORTAGE){
				return parent::addPost($photo);
			}
			return false;
		}
	}
	
	class Magazine extends Collection {
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->collectionType = CollectionType::$MAGAZINE;
		}
		
		/**
		 * @Override
		 * Controlla inoltre che il post da aggiungere sia un PostType::$NEWS.
		 */
		function addPost($news){
			if($news->getType()==PostType::$NEWS){
				return parent::addPost($news);
			}
			
			return false;
		}
	}
	
	class Playlist extends Collection {
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->collectionType = CollectionType::$PLAYLIST;
		}
		
		/**
		 * @Override
		 * Controlla inoltre che il post da aggiungere sia un PostType::$VIDEOREPORTAGE.
		 */
		function addPost($video){
			if($video->getType()==PostType::$VIDEOREPORTAGE){
				return parent::addPost($video);
			}
			
			return false;
		}
	}

?>
