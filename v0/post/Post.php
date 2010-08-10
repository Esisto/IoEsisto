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
		
		
		
		function save($savingMode) {
			
		}
		
		function delete() {
			
		}
	}
	
	class News extends Post {
		
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$NEWS);
		}
	}
	
	class PhotoReportage extends Post {
		
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$PHOTOREPORTAGE);
		}
	}
	
	class VideoReportage extends Post {
		
		function __construct($data) {
			parent::__construct($data);
			$this->setPostType(PostType::$VIDEOREPORTAGE);
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
			$this->author($author);
			$this->comment($comment);
			$this->post($post);
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
		
		function save($savingMode) {
			
		}
		
		function delete() {
			
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
		
		
		function save($savingMode) {
			
		}
		
		function delete() {
			
		}
	}
?>
