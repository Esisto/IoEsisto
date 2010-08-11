<?php
	class PostType {
		static $PHOTOREPORTAGE = "photoreportage";
		static $VIDEOREPORTAGE = "videoreportage";
		static $NEWS = "news";
		static $COLLECTION = "collection";
	}
	
	class SavingMode {
		static $INSERT = "insert";
		static $UPDATE = "update";
	}
	
	class Post {
		protected $ID;				// id recuperato dal database
		protected $postType;		//
		protected $title;			//
		protected $subtitle;		//
		protected $headline; 		// occhiello
		protected $author;			//
		protected $creationDate;	//
		protected $tags = array();			// array di oggetti TAG
		protected $categories = array();		// array di oggetti CATEGORY
		protected $comments = array();		// array di oggetti COMMENTO
		protected $votes = array();			// array di oggetto VOTO
		protected $content;		// testo del contenuto o indirizzo del video o foto o array di essi
		protected $visible;		// boolean
		protected $signals = array();			// array di oggetti SIGNAL
		
		function __construct($data) {
			if(isset($data["title"]))
				$this->setTitle($data["title"]);
			if(isset($data["subtitle"]))
				$this->setSubtitle($data["subtitle"]);
			if(isset($data["author"]))
				$this->setAuthor($data["author"]);
			if(isset($data["headline"]))
				$this->setHeadline($data["headline"]);
			if(isset($data["tags"]))
				$this->setTags($data["tags"]);
			if(isset($data["categories"]))
				$this->setCategories($data["categories"]);
			if(isset($data["content"]))
				$this->setContent($data["content"]);
			if(isset($data["visible"]))
				$this->setVisible($data["visible"]);
			$this->setCreationDate(time());
		}
		
		function addComment($comment) {
			$this->comments[] = $comment;
			
			$this->save(SavingMode::$UPDATE);
			return $this;
		}
		
		function addVote($vote) {
			$this->votes[] = $vote;
			
			$this->save(SavingMode::$UPDATE);
			return $this;
		}
		
		function getID() {
			return $this->ID;
		}
		function getTitle() {
			return $this->title;
		}
		function getSubtitle() {
			return $this->subtitle;
		}
		function getHeadline() {
			return $this->headline;
		}
		function getAuthor() {
			return $this->author;
		}
		function getCreationDate() {
			return $this->creationDate;
		}
		function getTags() {
			return $this->tags;
		}
		function getCategories() {
			return $this->categories;
		}
		function getContent() {
			return $this->content;
		}
		function isVisible() {
			return $this->visible;
		}
		function getType() {
			return $this->postType;
		}
		function getComments() {
			return $this->comments;
		}
		function getVotes() {
			return $this->votes;
		}

		
		function setTitle($title) {
			$this->title = $title;
			return $this;
		}
		function setSubtitle($subtitle) {
			$this->subtitle = $subtitle;
			return $this;
		}
		function setHeadline($occh) {
			$this->headline = $occh;
			return $this;
		}
		function setAuthor($author) {
			$this->author = $author;
			return $this;
		}
		function setCreationDate($cDate) {
			$this->creationDate = $cDate;
			return $this;
		}
		function setTags($tags) {
			$this->tags = $tags;
			return $this;
		}
		function setCategories($categories) {
			$this->categories = $categories;
			return $this;
		}
		function setContent($content) {
			$this->content = $content;
			return $this;
		}
		function setVisible($visible) {
			$this->visible = $visible;
			return $this;
		}
		function setPostType($type) {
			$this->postType = $type;
			return $this;
		}
		
		/**
		 * Salva il post nel database.
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
		
		function delete() {
			
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
			$s.= ") | content = " . $this->content .
				 " | visible = " . $this->visible .
				 " | signals = (";
			for($i=0; $i<count($this->signals); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->signals[$i];
			}
			$s.= "))";
			return $s;
		}
	}
	
	class News extends Post {
		
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$NEWS);
		}
	}
	
	class PhotoReportage extends Post {
		
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$PHOTOREPORTAGE);
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
		
		/**
		 * @Override
		 * Salva il fotoreportage nel database.
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
	}
	
	class VideoReportage extends Post {
		/**
		 * @Override
		 */
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$VIDEOREPORTAGE);
		}
	}
	
	class Category {
		private $name;
		
		/**
		 * @Override
		 */
		function __toString() {
			return $name;
		}
	}

	class Tag {
		private $name;
		
		/**
		 * @Override
		 */
		function __toString() {
			return $name;
		}
	}
	
	class Comment {
		private $author;
		private $post;
		private $comment;
		private $signals;
		
		function __construct($author,$post,$comment){
			$this->author = $author;
			$this->comment = $comment;
			$this->post = $post;
		}
		
		function getAuthor() {
			return $this->author;
		}
		
		function getPost() {
			return $this->post;
		}
		
		function getComment() {
			return $this->comment;
		}
		
		/**
		 * Salva il commento nel database.
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
		
		function delete() {
			
		}
		
		/**
		 * @Override
		 */
		function __toString() {
			$s = "Comment (author = " . $this->author .
				 " | post = " . $this->comment .
				 " | comment = " . $this->comment .
				 " | signals = (";
			for($i=0; $i<count($this->signals); $i++) {
				if($i>0) $s.= ", ";
				$s.= $this->signals[$i];
			}
			$s.= "))";
			return $s;
		}
	}
	
	class Vote {
		private $author;
		private $post;
		private $vote;
		
		function __construct($author,$post,$vote){
			$this->author = $author;
			$this->post = $post;
			$this->vote = $vote;
		}
		
		function getAuthor() {
			return $this->author;
		}
		
		function getPost() {
			return $this->post;
		}
		
		function getVote() {
			return $this->vote;
		}
		
		/**
		 * Salva il voto nel database.
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
		
		function delete() {
			
		}
		
		/**
		 * @Override
		 */
		function __toString() {
			$s = "Comment (author = " . $this->author .
				 " | post = " . $this->comment .
				 " | comment = " . $this->comment .
				 ")";
			return $s;
		}
	}
?>
