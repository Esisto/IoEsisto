<?php
require_once("post/Post.php");

class VideoReportage extends Post {
	/**
	 * @Override
	 */
	function __construct($data) {
		parent::__construct($data);
		require_once("post/PostCommon.php");
		$this->setType(PostType::$VIDEOREPORTAGE);
	}
	
	/**
	 * @Override
	 */
	function setContent($content) {
		require_once("common.php");
		if($content->getType() == ResourceType::$VIDEO)
			$this->content = $content;
		return $this;
	}
}

?>