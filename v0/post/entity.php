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
		protected $headline; 		// AAAAAARGH
		protected $author;			//
		protected $creationDate;	//
		protected $tags = array();			// array di oggetti TAG
		protected $categories = array();		// array di oggetti CATEGORY
		protected $comments = array();		// array di oggetti COMMENTO
		protected $votes = array();			// array di oggetto VOTO
		protected $content;		// testo del contenuto o indirizzo del video o foto o array di essi
		protected $visible;	// boolean
		protected $signals = array();			// array di oggetti SIGNAL
		
		function __construct($data) {
			$this->setTitle($data["title"]);
			$this->setSubtitle($data["subtitle"]);
			$this->setAuthor($data["author"]);
			$this->setHeadline($data["headline"]);
			$this->setTags($data["tags"]);
			$this->setCategories($data["categories"]);
			$this->setContent($data["content"]);
			$this->setVisible($data["visible"]);
			$this->setCreationDate(time());
		}
		
		function addComment($comment) {
			$this->comments[] = $comment;
			
			$this->save(SavingMode::$UPDATE);
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

		
		function setTitle($title) {
			$this->title = $title;
		}
		function setSubtitle($subtitle) {
			$this->subtitle = $subtitle;
		}
		function setHeadline($occh) {
			$this->headline = $occh;
		}
		function setAuthor($author) {
			$this->author = $author;
		}
		function setCreationDate($cDate) {
			$this->creationDate = $cDate;
		}
		function setTags($tags) {
			$this->tags = $tags;
		}
		function setCategories($categories) {
			$this->categories = $categories;
		}
		function setContent($content) {
			$this->content = $content;
		}
		function setVisible($visible) {
			$this->visible = $visible;
		}
		function setType($type) {
			$this->postType = $type;
		}
		
		
		
		function save($savingMode) {
			
		}
		
		function delete() {
			
		}
	}
	
	class News extends Post {
		
		function __construct($data) {
			parent::__construct($data);
			$this->postType = PostType::NEWS;
		}
	}
	
	class PhotoReportage extends Post {
		
		function __construct($data) {
			parent::__construct($data);
			$this->postType = PostType::$PHOTOREPORTAGE;
		}
	}
	
	class VideoReportage extends Post {
		
		function __construct($data) {
			parent::__construct($data);
			$this->postType = PostType::$VIDEOREPORTAGE;
		}
	}
	
	class Category {
		private $name;
	}

	class Tag {
		private $name;
	}
	
	class Comment {
		private $author;
		private $post;
		private $comment;
		private $signals;
		
		function __construct($author,$post,$comment){
			$this->setAuthor($author);
			$this->setComment($comment);
			$this->setPost($post);
		}
		
		function setAuthor($author){
			$this->author=$author;
		}
		
		function setPost($post){
			$this->post=$post;
		}
		
		function setComment($comment){
			$this->comment=$comment;
		}
		
		function save($savingMode) {
			
		}
		
		function delete() {
			
		}
	}
	
	class Vote {
		private $author;
		private $post;
		private $vote;
	}
?>
