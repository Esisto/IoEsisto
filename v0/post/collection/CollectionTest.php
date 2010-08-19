<?php
require_once("CollectionManager.php");
require_once("Collection.php");

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
		return "<br />Collection test NOT PASSED: not added";
	if($p1->getTitle() != $data["title"])
		return "<br />Collection test NOT PASSED: title";
	if($p1->getSubtitle() != $data["subtitle"])
		return "<br />Collection test NOT PASSED: subtitle";
	if($p1->getHeadline() != $data["headline"])
		return "<br />Collection test NOT PASSED: headline";
	if($p1->getAuthor() != $data["author"])
		return "<br />Collection test NOT PASSED: author";
	if($p1->getTags() != $data["tags"])
		return "<br />Collection test NOT PASSED: tags";
	if($p1->getCategories() != $data["categories"])
		return "<br />Collection test NOT PASSED: categories";
	if(isset($data["content"])) {
		if(is_array($data["content"]))
			if($p1->getContent() != $data["content"])
				return "<br />Collection test NOT PASSED: content";
		else
			if($p1->getContent() != array($data["content"]))
				return "<br />Collection test NOT PASSED: content";
	}
	if($p1->isVisible() != $data["visible"])
		return "<br />Collection test NOT PASSED: visible";
	
	return "<br />Collection test passed";
}


function testAddPostToCollection() {
	require_once("post/PostManager.php");
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => false);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$data1 = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "L'OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"),
				  "visible" => true);
	$c = CollectionManager::addCollection($data1, CollectionType::$MAGAZINE);
	$c1 = CollectionManager::addPostToCollection($p, $c);
	
	$c = CollectionManager::loadCollection($c1->getID());
	echo $c1 . "<br />" . $c; //DEBUG
	if($c1 == null)
		return "<br />AddToCollection test NOT PASSED: not created";
	if(count($c->getContent()) == 0)
		return "<br />AddToCollection test NOT PASSED: not added";
	
	return "<br />AddToCollection test passed";
}

/**
 * Crea un post;
 * Crea una collezione con il post nel contenuto;
 * Carica la collezione e la confronta con quello salvato in memoria;
 */
function testSaveCollection() {
	require_once("post/PostManager.php");
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => false);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$data1 = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => $p->getID(),
				  "visible" => true);
	
	$p1 = CollectionManager::addCollection($data1, CollectionType::$MAGAZINE);
	$post = CollectionManager::loadCollection($p1->getID());
	//echo "<br />" . $p1 . "<br />" . $post; //DEBUG
	
	if($post === false)
		return "<br />Collection saving test NOT PASSED: not created";
	if($p1->getTitle() != $post->getTitle())
		return "<br />Collection saving test NOT PASSED: title";
	if($p1->getSubtitle() != $post->getSubtitle())
		return "<br />Collection saving test NOT PASSED: subtitle";
	if($p1->getHeadline() != $post->getHeadline())
		return "<br />Collection saving test NOT PASSED: headline";
	if($p1->getAuthor() != $post->getAuthor())
		return "<br />Collection saving test NOT PASSED: author";
	//if(isset($row["tags"]))
	//	if($p1->getTags() != $row["tags"])
	//		return "<br />Post saving test NOT PASSED: tags";
	//if(isset($row["categories"]))
	//	if($p1->getCategories() != $row["categories"])
	//		return "<br />Post saving test NOT PASSED: categories";
	if($p1->getContent() != $post->getContent())
		return "<br />Collection saving test NOT PASSED: content";
	if($p1->isVisible() != $post->isVisible())
		return "<br />Collection saving test NOT PASSED: visible";
	$first = false;
	
	if($first) return "<br />Collection saving test NOT PASSED: not created";
	return "<br />Save Collection test passed";
}

/**
 * Crea un post;
 * Crea una collezione con il post nel contenuto;
 * Associa alla collezione un voto (lo crea);
 * Carica il voto dal database e lo confronta con quello salvato in memoria;
 */
function testSaveVoteOnCollection() {
	require_once("post/PostManager.php");
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "<a href='ahahah.php'>",
				  "visible" => false);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$data1 = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"),
				  "visible" => true);
	$voteauthor = 2;
	$votevalue = true;
	$p2 = CollectionManager::addCollection($data1, CollectionType::$MAGAZINE);
	$p1 = CollectionManager::voteCollection($voteauthor, $p2, $votevalue);
	
	if(count($p1->getVotes()) == 0)
		return "<br />Vote saving test NOT PASSED: not added";
	$votes = $p1->getVotes();
	$vote = PostManager::loadVote($votes[0]->getAuthor(),$votes[0]->getPost());
	$post = CollectionManager::loadCollection($p1->getId());
	if(count($post->getVotes()) == 0)
		return "<br />Vote saving test NOT PASSED: not added";
	//echo $votes[0] . "<br />" . $vote; //DEBUG
	//echo "<br />" . $p1 . "<br />" . $post; //DEBUG
	if($com === false) return "<br />Vote saving test NOT PASSED: not created";
	
	if($votes[0]->getAuthor() != $vote->getAuthor())
		return "<br />Vote saving test NOT PASSED: author";
	if($votes[0]->getVote() != $vote->getVote())
		return "<br />Vote saving test NOT PASSED: comment";
	if($votes[0]->getPost() != $vote->getPost())
		return "<br />Vote saving test NOT PASSED: post";
	if($votes[0]->getCreationDate() != $vote->getCreationDate())
		return "<br />Vote saving test NOT PASSED: creationDate";
	
	return "<br />Vote saving test passed";
}
?>