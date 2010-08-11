<?php
require_once("CollectionManager.php");
require_once("Collection.php");
//require_once("../PostManager.php");

function testCollection() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p1 = CollectionManager::addCollection($data, CollectionType::$ALBUM);
	//$data1 = array("title" => "TITOLO1", "subtitle" => "SOTTOTITOLO1", "headline" => "OCCHIELLO1",
	//			   "content" => "CONTENUTO NON FILTRATO1", "visible" => false);
	//$p1 = PostManager::editPost($p, $data1);
	
	if($p1 == null)
		return "Collection test NOT PASSED: not added";
	if($p1->getTitle() != $data["title"])
		return "Collection test NOT PASSED: title";
	if($p1->getSubtitle() != $data["subtitle"])
		return "Collection test NOT PASSED: subtitle";
	if($p1->getHeadline() != $data["headline"])
		return "Collection test NOT PASSED: headline";
	if($p1->getAuthor() != $data["author"])
		return "Collection test NOT PASSED: author";
	if($p1->getTags() != $data["tags"])
		return "Collection test NOT PASSED: tags";
	if($p1->getCategories() != $data["categories"])
		return "Collection test NOT PASSED: categories";
	if(isset($data["content"])) {
		if(is_array($data["content"]))
			if($p1->getContent() != $data["content"])
				return "Collection test NOT PASSED: content";
		else
			if($p1->getContent() != array($data["content"]))
				return "Collection test NOT PASSED: content";
	}
	if($p1->isVisible() != $data["visible"])
		return "Collection test NOT PASSED: visible";
	
	return "Collection test passed";
}

function testAddPostToCollection() {
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$c = CollectionManager::addCollection($data, CollectionType::$ALBUM);
	$p = PostManager::addPost($data, PostType::$PHOTOREPORTAGE);
	
	$c1 = CollectionManager::addPostToCollection($p, $c);
	
	if($c1 == null)
		return "AddToCollection test NOT PASSED: not created";
	if(count($c1->getContent()) == 0)
		return "AddToCollection test NOT PASSED: not added";
	
	return "AddToCollection test passed";
}

?>