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
		//require_once("dataobject/Resource.php");
		//foreach ($content as $resource)
		//	if(is_subclass_of($resource, "Resource") || $resource->getType() != Resource::PHOTO)
		//		throw new Exception("ERRORE!!! Non stai inserendo delle Foto.");

		$this->content = serialize($content);
		return $this;
	}
}
?>