<?php
	class Post {
		static $pTypes = array("PHOTOREPORTAGE" => "photoreportage",
							"VIDEOREPORTAGE" => "videoreportage",
							"NEWS" => "news",
							"COLLECTION" => "collection");
		
		protected $ID;				// id recuperato dal database
		protected $postType;		//
		protected $title;			//
		protected $subtitle;		//
		protected $occhiello; 		// AAAAAARGH
		protected $author;			//
		protected $creationDate;	//
		protected $tags = array();			// array di oggetti TAG
		protected $categories = array();		// array di oggetti CATEGORY
		protected $comments = array();		// array di oggetti COMMENTO
		protected $votes = array();			// array di oggetto VOTO
		protected $contents;		// testo del contenuto o indirizzo del video o foto o array di essi
		protected $visible;	// boolean
		protected $signals = array();			// array di oggetti SIGNAL
		
		function __construct($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible) {
			$this->setTitle($title);
			$this->setSubtitle($subtitle);
			$this->setAuthor($author);
			$this->setOcchiello($occh);
			$this->setTags($tags);
			$this->setCategories($categories);
			$this->setContents($content);
			$this->setVisible($visible);
			$this->setCreationDate(time());
		}
		
		function addComment($comment) {
			$this->comments[$this->comments->lenght] = $comment;
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
		function getOcchiello() {
			return $this->occhiello;
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
		function getContents() {
			return $this->contents;
		}
		function getVisible() {
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
		function setOcchiello($occh) {
			$this->occhiello = $occh;
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
		function setContents($contents) {
			$this->contents = $contents;
		}
		function setVisible($visible) {
			$this->visible = $visible;
		}
		function setType($type) {
			$this->postType = $type;
		}
		
		
		
		function save() {
			
		}
	}
	
	class News extends Post {
		
		function __construct($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible) {
			parent::__construct($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible);
			$this->postType = Post::$pTypes["NEWS"];
		}
	}
	
	class PhotoReportage extends Post {
		
		function __construct($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible) {
			parent::__construct($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible);
			$this->postType = Post::$pTypes["PHOTOREPORTAGE"];
		}
	}
	
	class VideoReportage extends Post {
		
		function __construct($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible) {
			parent::__construct($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible);
			$this->postType = Post::$pTypes["VIDEOREPORTAGE"];
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
	}
	
	class Vote {
		private $author;
		private $post;
		private $vote;
	}
?>
