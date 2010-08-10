<?php
require_once("PostManager.php");
require_once("Post.php");
require_once("collection/Collection.php");

function testPost() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p1 = PostManager::addPost($data, PostType::$NEWS);
	
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
	if($p1->getContent() != $data["content"])
		return "Post test NOT PASSED: content";
	if($p1->isVisible() != $data["visible"])
		return "Post test NOT PASSED: visible";
	
	return "Post test PASSED";
}

function testComment() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$p1 = PostManager::addComment($p, 2, "PROVA!");
	
	if(count($p1->getComments()) == 0)
		return "Comment test NOT PASSED: not added";
	$comm = $p1->getComments();
	if($comm[0]->getAuthor() != 2)
		return "Comment test NOT PASSED: author";
	if($comm[0]->getComment() != "PROVA!")
		return "Comment test NOT PASSED: comment";
	if($comm[0]->getPost() != $p1->getID())
		return "Comment test NOT PASSED: post";
	
	
	return "Post test PASSED";
}

function testVote() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$p1 = PostManager::addVote(4,$p,TRUE);
	
	if(count($p1->getVotes()) == 0)
		return "Comment test NOT PASSED: not added";
	$vote = $p1->getVotes();
	if($vote[0]->getAuthor() != 4)
		return "Comment test NOT PASSED: author";
	if($vote[0]->getVote() != true)
		return "Comment test NOT PASSED: comment";
	if($vote[0]->getPost() != $p1->getID())
		return "Comment test NOT PASSED: post";
	
	return "Post test PASSED";
}

function testEditPost() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$data1 = array("title" => "TITOLO1", "subtitle" => "SOTTOTITOLO1", "headline" => "OCCHIELLO1",
				   "content" => "CONTENUTO NON FILTRATO1", "visible" => false);
	$p1 = PostManager::editPost($p, $data1);
	
	if($p1->getTitle() != $data1["title"])
		return "Post test NOT PASSED: title";
	if($p1->getSubtitle() != $data1["subtitle"])
		return "Post test NOT PASSED: subtitle";
	if($p1->getHeadline() != $data1["headline"])
		return "Post test NOT PASSED: headline";
	if($p1->getAuthor() != $data["author"])
		return "Post test NOT PASSED: author";
	if($p1->getTags() != $data["tags"])
		return "Post test NOT PASSED: tags";
	if($p1->getCategories() != $data["categories"])
		return "Post test NOT PASSED: categories";
	if($p1->getContent() != $data1["content"])
		return "Post test NOT PASSED: content";
	var_dump($p1);
	if($p1->isVisible() != $data1["visible"])
		return "Post test NOT PASSED: visible";
	
	return "Post test PASSED";
}

function testCollection() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$data1 = array("title" => "TITOLO1", "subtitle" => "SOTTOTITOLO1", "headline" => "OCCHIELLO1",
				   "content" => "CONTENUTO NON FILTRATO1", "visible" => false);
	$p1 = PostManager::editPost($p, $data1);
	
	if($p1->getTitle() != $data1["title"])
		return "Post test NOT PASSED: title";
	if($p1->getSubtitle() != $data1["subtitle"])
		return "Post test NOT PASSED: subtitle";
	if($p1->getHeadline() != $data1["headline"])
		return "Post test NOT PASSED: headline";
	if($p1->getAuthor() != $data["author"])
		return "Post test NOT PASSED: author";
	if($p1->getTags() != $data["tags"])
		return "Post test NOT PASSED: tags";
	if($p1->getCategories() != $data["categories"])
		return "Post test NOT PASSED: categories";
	if($p1->getContent() != $data1["content"])
		return "Post test NOT PASSED: content";
	if($p1->isVisible() != $data1["visible"])
		return "Post test NOT PASSED: visible";
	
	return "Post test PASSED";
}


?>