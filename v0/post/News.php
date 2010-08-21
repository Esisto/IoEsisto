<?php
require_once("post/Post.php");

class News extends Post {
	
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::$NEWS);
	}

}

?>