<?php
require_once("control.php");
require_once("entity.php");
require_once("raccolta/entity.php");

function testPost() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p1 = PostManager::addPost($data, Post::$pTypes["NEWS"]);
	
	if($p1->getTitle() != $data["title"])
		return "Post test NOT PASSED: title";
	if($p1->getSubtitle() != $data["subtitle"])
		return "Post test NOT PASSED: subtitle";
	if($p1->getHeadline() != $data["headline"])
		return "Post test NOT PASSED: headline";
	if($p1->getAuthor() != $data["author"])
		return "Post test NOT PASSED: author";
	if($p1->getTags() != $data["tags"])
		return "Post test NOT PASSED: tags";
	if($p1->getCategories() != $data["categories"])
		return "Post test NOT PASSED: categories";
	if($p1->getContents() != $data["content"])
		return "Post test NOT PASSED: content";
	if($p1->getVisible() != $data["visible"])
		return "Post test NOT PASSED: visible";
	
	return "Post test PASSED";
}

function testComment() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p = PostManager::addPost($data, Post::$pTypes["NEWS"]);
	$p1 = PostManager::addComment($p, 2, "PROVA!");
	
	if(count($p1->getComments()) > 0)
		return "Comment test NOT PASSED: not added";
	$comm = $p1->getComments();
	if($comm[0]->getAuthor() != 2)
		return "Comment test NOT PASSED: not added";
	if(count($p1->getComments()) > 0)
		return "Comment test NOT PASSED: not added";
	if(count($p1->getComments()) > 0)
		return "Comment test NOT PASSED: not added";
	
	
	return "Post test PASSED";
}
?>