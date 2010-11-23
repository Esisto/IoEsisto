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
	
	/**
	 * @Override
	 */
	function setContent($content) {
		require_once("dataobject/Resource.php");
		if(!is_subclass_of($content,"Resouce") || $content->getType() != Resource::VIDEO)
			throw new Exception("ERRORE!!! Non stai inserendo un Video.");
				
		$this->content = $content;
		return $this;
	}
}
?>