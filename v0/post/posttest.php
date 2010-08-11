<?php
require_once("PostManager.php");
require_once("Post.php");

function testPost() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p1 = PostManager::addPost($data, PostType::$NEWS);
	
	if($p1 == null)
		return "Post test NOT PASSED: not created";
	if(isset($data["title"]))
		if($p1->getTitle() != $data["title"])
			return "Post test NOT PASSED: title";
	if(isset($data["subtitle"]))
		if($p1->getSubtitle() != $data["subtitle"])
			return "Post test NOT PASSED: subtitle";
	if(isset($data["headline"]))
		if($p1->getHeadline() != $data["headline"])
			return "Post test NOT PASSED: headline";
	if(isset($data["author"]))
		if($p1->getAuthor() != $data["author"])
			return "Post test NOT PASSED: author";
	if(isset($data["tags"]))
		if($p1->getTags() != $data["tags"])
			return "Post test NOT PASSED: tags";
	if(isset($data["categories"]))
		if($p1->getCategories() != $data["categories"])
			return "Post test NOT PASSED: categories";
	if(isset($data["content"]))
		if($p1->getContent() != $data["content"])
			return "Post test NOT PASSED: content";
	if(isset($data["visible"]))
		if($p1->isVisible() != $data["visible"])
			return "Post test NOT PASSED: visible";
	
	return "Post test passed";
}

function testComment() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$commentauthor = 2;
	$commenttext = "PROVA!";
	$p = PostManager::addPost($data, PostType::$NEWS);
	$p1 = PostManager::commentPost($p, $commentauthor, $commenttext);
	
	if(count($p1->getComments()) == 0)
		return "Comment test NOT PASSED: not added";
	$comm = $p1->getComments();
	if(isset($commentauthor) && $comm[0]->getAuthor() != $commentauthor)
		return "Comment test NOT PASSED: author";
	if(isset($commenttext) && $comm[0]->getComment() != $commenttext)
		return "Comment test NOT PASSED: comment";
	if($comm[0]->getPost() != $p1->getID())
		return "Comment test NOT PASSED: post";
	
	
	return "Comment test passed";
}

function testVote() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 5, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$voteauthor = 2;
	$votevalue = true;
	$p1 = PostManager::votePost($voteauthor, $p, $votevalue);
	
	if(count($p1->getVotes()) == 0)
		return "Vote test NOT PASSED: not added";
	$vote = $p1->getVotes();
	if(isset($voteauthor) && $vote[0]->getAuthor() != $voteauthor)
		return "Vote test NOT PASSED: author";
	if(isset($votevalue) && $vote[0]->getVote() != $votevalue)
		return "Vote test NOT PASSED: comment";
	if($vote[0]->getPost() != $p1->getID())
		return "Vote test NOT PASSED: post";
	
	return "Vote test passed";
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
	
	
	if(isset($data1["title"]))
		$d1 = $data1["title"];
	else if(isset($data["title"]))
		$d1 = $data["title"];
	if(isset($d1) && $p1->getTitle() != $d1)
		return "Post test NOT PASSED: title";
	if(isset($data1["subtitle"]))
		$d2 = $data1["subtitle"];
	else if(isset($data["subtitle"]))
		$d2 = $data["subtitle"];
	if(isset($d2) && $p1->getSubtitle() != $d2)
		return "Post test NOT PASSED: subtitle";
	if(isset($data1["headline"]))
		$d3 = $data1["headline"];
	else if(isset($data["headline"]))
		$d3 = $data["headline"];
	if(isset($d3) && $p1->getHeadline() != $d3)
		return "Post test NOT PASSED: headline";
	if(isset($data1["author"]))
		$d4 = $data1["author"];
	else if(isset($data["author"]))
		$d4 = $data["author"];
	if(isset($d4) && $p1->getAuthor() != $d4)
		return "Post test NOT PASSED: author";
	if(isset($data1["tags"]))
		$d5 = $data1["tags"];
	else if(isset($data["tags"]))
		$d5 = $data["tags"];
	if(isset($d5) && $p1->getTags() != $d5)
		return "Post test NOT PASSED: tags";
	if(isset($data1["categories"]))
		$d6 = $data1["categories"];
	else if(isset($data["categories"]))
		$d6 = $data["categories"];
	if(isset($d6) && $p1->getCategories() != $d6)
		return "Post test NOT PASSED: categories";
	if(isset($data1["content"]))
		$d7 = $data1["content"];
	else if(isset($data["content"]))
		$d7 = $data["content"];
	if(isset($d7) && $p1->getContent() != $d7)
		return "Post test NOT PASSED: content";
	if(isset($data1["visible"]))
		$d8 = $data1["visible"];
	else if(isset($data["visible"]))
		$d8 = $data["visible"];
	if(isset($d8) && $p1->isVisible() != $d8)
		return "Post test NOT PASSED: visible";
	
	return "EditPost test passed";
}

?>