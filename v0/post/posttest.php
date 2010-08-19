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
		return "<br />Post test NOT PASSED: not created";
	if(isset($data["title"]))
		if($p1->getTitle() != $data["title"])
			return "<br />Post test NOT PASSED: title";
	if(isset($data["subtitle"]))
		if($p1->getSubtitle() != $data["subtitle"])
			return "<br />Post test NOT PASSED: subtitle";
	if(isset($data["headline"]))
		if($p1->getHeadline() != $data["headline"])
			return "<br />Post test NOT PASSED: headline";
	if(isset($data["author"]))
		if($p1->getAuthor() != $data["author"])
			return "<br />Post test NOT PASSED: author";
	if(isset($data["tags"]))
		if($p1->getTags() != $data["tags"])
			return "<br />Post test NOT PASSED: tags";
	if(isset($data["categories"]))
		if($p1->getCategories() != $data["categories"])
			return "<br />Post test NOT PASSED: categories";
	if(isset($data["content"]))
		if($p1->getContent() != $data["content"])
			return "<br />Post test NOT PASSED: content";
	if(isset($data["visible"]))
		if($p1->isVisible() != $data["visible"])
			return "<br />Post test NOT PASSED: visible";
	
	return "<br />Post test passed";
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
		return "<br />Comment test NOT PASSED: not added";
	$comm = $p1->getComments();
	if(isset($commentauthor) && $comm[0]->getAuthor() != $commentauthor)
		return "<br />Comment test NOT PASSED: author";
	if(isset($commenttext) && $comm[0]->getComment() != $commenttext)
		return "<br />Comment test NOT PASSED: comment";
	if($comm[0]->getPost() != $p1->getID())
		return "<br />Comment test NOT PASSED: post";
	
	
	return "<br />Comment test passed";
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
		return "<br />Vote test NOT PASSED: not added";
	$vote = $p1->getVotes();
	if(isset($voteauthor) && $vote[0]->getAuthor() != $voteauthor)
		return "<br />Vote test NOT PASSED: author";
	if(isset($votevalue) && $vote[0]->getVote() != $votevalue)
		return "<br />Vote test NOT PASSED: comment";
	if($vote[0]->getPost() != $p1->getID())
		return "<br />Vote test NOT PASSED: post";
	
	return "<br />Vote test passed";
}

function testEditPost() {
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "L'OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	$p = PostManager::addPost($data, PostType::$NEWS);
	$data1 = array("title" => "titolo", "subtitle" => "sottotitolo", "headline" => "l'occhiello",
				   "content" => "contenuto non filtrato", "visible" => false);
	$p1 = PostManager::editPost($p, $data1);
	
	$p = PostManager::loadPost($p1->getID());
	
	require_once("common.php");
	if(isset($data1["title"]))
		$data["title"] = $data1["title"];
	if(isset($d1) && $p->getTitle() != $data["title"])
		return "<br />Post test NOT PASSED: title";
	if(isset($data1["subtitle"]))
		$data["subtitle"] = $data1["subtitle"];
	if(isset($d2) && $p->getSubtitle() != $data["subtitle"])
		return "<br />Post test NOT PASSED: subtitle";
	if(isset($data1["headline"]))
		$data["headline"] = $data1["headline"];
	if(isset($d3) && $p->getHeadline() != $data["headline"])
		return "<br />Post test NOT PASSED: headline";
	if(isset($data1["author"]))
		$data["author"] = $data1["author"];
	if(isset($d4) && $p->getAuthor() != $data["author"])
		return "<br />Post test NOT PASSED: author";
	//if(isset($data1["tags"]))
	//	$data["tags"] = $data1["tags"];
	//if(isset($d5) && $p->getTags() != $data["tags"])
	//	return "<br />Post test NOT PASSED: tags";
	//if(isset($data1["categories"]))
	//	$data["categories"] = $data1["categories"];
	//if(isset($d6) && $p->getCategories() != $data["categories"])
	//	return "<br />Post test NOT PASSED: categories";
	if(isset($data1["content"]))
		$data["content"] = $data1["content"];
	if(isset($d7) && $p->getContent() != $data["content"])
		return "<br />Post test NOT PASSED: content";
	if(isset($data1["visible"]))
		$data["visible"] = $data1["visible"];
	if(isset($d8) && $p1->isVisible() != $data["visible"])
		return "<br />Post test NOT PASSED: visible";
	
	return "<br />EditPost test passed";
}

/**
 * Crea un post;
 * Carica il post dal database e lo confronta con quello salvato in memoria;
 */
function testSavePost() {
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => true);
	
	$p1 = PostManager::addPost($data, PostType::$NEWS);
	$post = PostManager::loadPost($p1->getID());
	//echo $p1 . "<br />" . $post; //DEBUG
	
	if($post === false)
		return "<br />Post saving test NOT PASSED: not created";
	if($p1->getTitle() != $post->getTitle())
		return "<br />Post saving test NOT PASSED: title";
	if($p1->getSubtitle() != $post->getSubtitle())
		return "<br />Post saving test NOT PASSED: subtitle";
	if($p1->getHeadline() != $post->getHeadline())
		return "<br />Post saving test NOT PASSED: headline";
	if($p1->getAuthor() != $post->getAuthor())
		return "<br />Post saving test NOT PASSED: author";
	//if(isset($row["tags"]))
	//	if($p1->getTags() != $row["tags"])
	//		return "<br />Post saving test NOT PASSED: tags";
	//if(isset($row["categories"]))
	//	if($p1->getCategories() != $row["categories"])
	//		return "<br />Post saving test NOT PASSED: categories";
	if($p1->getContent() != $post->getContent())
		return "<br />Post saving test NOT PASSED: content";
	if($p1->isVisible() != $post->isVisible())
		return "<br />Post saving test NOT PASSED: visible";
	$first = false;
	
	if($first) return "<br />Post saving test NOT PASSED: not created";
	return "<br />Save Post test passed";
}

/**
 * Crea un post;
 * Associa al post un commento (lo crea);
 * Carica il commento dal database e lo confronta con quello salvato in memoria;
 */
function testSaveComment() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => false);
	$commentauthor = 2;
	$commenttext = "PROVA!";
	$p = PostManager::addPost($data, PostType::$NEWS);
	$p1 = PostManager::commentPost($p, $commentauthor, $commenttext);
	
	if(count($p1->getComments()) == 0)
		return "<br />Comment test NOT PASSED: not added";
	$comm = $p1->getComments();
	$com = PostManager::loadComment($comm[0]->getId());
	$post = PostManager::loadPost($p1->getId());
	if($post === false || count($post->getComments()) == 0)
		return "<br />Comment test NOT PASSED: not added";
	//echo $comm[0] . "<br />" . $com; //DEBUG
	//echo "<br />" . $p1 . "<br />" . $post; //DEBUG
	if($com === false) return "<br />Comment saving test NOT PASSED: not created";
	
	if($comm[0]->getAuthor() != $com->getAuthor())
		return "<br />Comment saving test NOT PASSED: author";
	if($comm[0]->getComment() != $com->getComment())
		return "<br />Comment saving test NOT PASSED: comment";
	if($comm[0]->getPost() != $com->getPost())
		return "<br />Comment saving test NOT PASSED: post";
	if($comm[0]->getCreationDate() != $com->getCreationDate())
		return "<br />Comment saving test NOT PASSED: creationDate";
	if($comm[0]->getID() != $com->getID())
		return "<br />Comment saving test NOT PASSED: ID";
	
	return "<br />Comment saving test passed";
}

/**
 * Crea un post;
 * Associa al post un voto ed un commento (li crea);
 * Cancella il post (di conseguenza anche il voto e il commento, per CASCADE);
 */
function testDeletePost() {
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => false);
	$commentauthor = 2;
	$commenttext = "PROVA!";
	$voteauthor = 2;
	$votevalue = true;
	$p = PostManager::addPost($data, PostType::$NEWS);
	$p1 = PostManager::votePost($voteauthor, $p, $votevalue);
	$p1 = PostManager::commentPost($p, $commentauthor, $commenttext);
	
	if(count($p1->getComments()) == 0)
		return "<br />Post deleting test NOT PASSED: not added";
	$comm = $p1->getComments();
	$votes = $p1->getVotes();
	
	$com = PostManager::loadComment($comm[0]->getId());
	$vote = PostManager::loadVote($votes[0]->getAuthor(), $votes[0]->getPost());
	$post = PostManager::loadPost($p1->getId());
	if($com !== false && $post !== false && $vote !== false) {
		$post = PostManager::deletePost($post);
		
		$post = PostManager::loadPost($post->getID());
		$com = PostManager::loadComment($com->getID());
		$vote = PostManager::loadVote($vote->getAuthor(), $vote->getPost());
		if($post === false && $com === false && $vote === false)
			return "<br />Post deleting test passed";
	}
	
	return "<br />Post deleting test NOT PASSED: not deleted";
}

/**
 * Crea un post;
 * Associa al post un commento (lo crea);
 * Cancella il commento dal database;
 * Carica il post e cerca di caricare il commento;
 * Controlla che il post non abbia quel commento e di non essere riuscito a caricare il commento;
 */
function testDeleteComment() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => false);
	$commentauthor = 2;
	$commenttext = "PROVA!";
	$p = PostManager::addPost($data, PostType::$NEWS);
	$p1 = PostManager::commentPost($p, $commentauthor, $commenttext);
	
	if(count($p1->getComments()) == 0)
		return "<br />Post deleting test NOT PASSED: not added";
	$comm = $p1->getComments();
	
	$com = PostManager::loadComment($comm[0]->getId());
	$post = PostManager::loadPost($p1->getId());
	if($com !== false && $post !== false) {
		$com = PostManager::removeComment($com);
		
		$post = PostManager::loadPost($post->getID());
		$com = PostManager::loadComment($com->getID());
		
		if($post !== false && $com === false) {
			if(count($post->getComments()) == 0)
				return "<br />Comment deleting test passed";
		}
	}
	
	return "<br />Comment deleting test NOT PASSED: not deleted";
}

/**
 * Crea un post;
 * Associa al post un voto (lo crea);
 * Carica il voto dal database e lo confronta con quello salvato in memoria;
 */
function testSaveVote() {
	
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => false);
	$voteauthor = 2;
	$votevalue = true;
	$p = PostManager::addPost($data, PostType::$NEWS);
	$p1 = PostManager::votePost($voteauthor, $p, $votevalue);
	
	if(count($p1->getVotes()) == 0)
		return "<br />Vote saving test NOT PASSED: not added";
	$votes = $p1->getVotes();
	$vote = PostManager::loadVote($votes[0]->getAuthor(),$votes[0]->getPost());
	$post = PostManager::loadPost($p1->getId());
	if($post === false || count($post->getVotes()) == 0)
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

/**
 * Crea un post;
 * Associa al post un voto (lo crea);
 * Cancella il voto dal database;
 * Carica il post e cerca di caricare il voto;
 * Controlla che il post non abbia quel voto e di non essere riuscito a caricare il voto;
 */
function testDeleteVote() {
	$data = array("title" => "TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
				  "author"=> 2, "tags" => array("tag1", "tag2", "tag3"),
				  "categories" => array("cat1", "cat2", "cat3"), "content" => "CONTENUTO NON FILTRATO",
				  "visible" => false);
	$voteauthor = 2;
	$votevalue = true;
	$p = PostManager::addPost($data, PostType::$NEWS);
	$p1 = PostManager::votePost($voteauthor, $p, $votevalue);
	
	if(count($p1->getVotes()) == 0)
		return "<br />Vote deleting test NOT PASSED: not added";
	$votes = $p1->getVotes();
	
	$vote = PostManager::loadVote($votes[0]->getAuthor(),$votes[0]->getPost());
	$post = PostManager::loadPost($p1->getId());
	if($com !== false && $post !== false) {
		$vote = PostManager::removeVote($vote);
		
		$post = PostManager::loadPost($post->getID());
		$vote = PostManager::loadComment($vote->getAuthor(), $vote->getPost());
		
		if($post !== false && $vote === false) {
			if(count($post->getVotes()) == 0)
				return "<br />Vote deleting test passed";
		}
	}
	return "<br />Vote deleting test NOT PASSED: not deleted";
}
?>