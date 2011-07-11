<?php
require_once("dataobject/Post.php");

class VideoReportage extends Post {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		$this->setType(Post::VIDEOREP);
	}
	
}
?>