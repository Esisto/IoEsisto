<?php
require_once("dataobject/Post.php");

class News extends Post {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setType(Post::NEWS);
	}
}
?>