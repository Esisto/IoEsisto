<?php
require_once("post/PostCommon.php");
require_once("post/PostManager.php");
require_once("post/Post.php");
require_once("post/collection/CollectionManager.php");
require_once("post/collection/Collection.php");
require_once("post/contest/ContestManager.php");
require_once("post/contest/Contest.php");

class Test {
	var $author_id;
	var $vote_value;
	var $comment_text;
	var $post_data;
	var $post_data2;
	var $post_data_all;
	var $collection_data_all;
	
	function __construct() {
		$this->author_id = 1;
		$this->vote_value = true;
		$this->comment_text = "COMMENTO!";
		$this->post_serious = array("title" => "Nasce PubliChi", "subtitle" => "Un nuovo sito dedicato alla libera informazione.", "headline" => "Giornalista fai da te? No PubliChi? Ahi Ahi Ahi!",
								 "author"=> $this->author_id, "categories" => "Novità, Web", "tags" => "Giornali, Notizie, Informazione",
								 "content" => "Nasce il sito che permette agli utenti di scrivere le notizie come se fossero dei giornalisti. PubliChi, il nome del sito, mette insieme la personalià di un blog (notizie, foto, video) con la comunicazione di un social network ma non è nessuna di queste due cose: nasce infatti come sito di editoria giornalistica libera. Stanchi di giornali e telegiornali che informano solo su quello che decidono loro, gli ideatori di PubliChi vogliono che siano gli utenti stessi ad informare il mondo. Con la diffusione degli smartphone si può dare in tempo reale la notizia di un incidente in autostrada, di un disagio in metropolitana, di ritardi alle stazioni. Che si stia mettendo male per i giornali seri e professionali?",
								 "visible" => true, "type" => PostType::$NEWS, "place" => "45.5239725, 9.1709894");
		$this->post_data = array("title" => "UN BEL TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "L'OCCHIELLO",
								 "author"=> $this->author_id, "tags" => "tag1, tag2, tag3",
								 "content" => "CONTENUTO NON FILTRATO",
								 "visible" => true, "type" => PostType::$NEWS);
		$this->post_data2 = array("title" => "un bel titolo", "subtitle" => "sottotitolo", "headline" => "l'occhiello",
								  "content" => "contenuto non filtrato", "visible" => false,
								  "categories" => "cat1, cat2, cat3", "tags" => "");
		$this->post_data_all = array("title" => "UN BEL TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "OCCHIELLO",
									 "author"=> $this->author_id, "tags" => "tag1, tag2, tag3",
									 "categories" => "cat1, cat2, cat3", "content" => "CONTENUTO NON FILTRATO",
									 "visible" => true, "type" => PostType::$NEWS);
		$this->collection_data_all = array("title" => "UN BEL TITOLO", "subtitle" => "SOTTOTITOLO", "headline" => "L'OCCHIELLO",
										   "author"=> $this->author_id, "tags" => "tag1, tag2, tag3",
										   "categories" => "cat1, cat2, cat3", "content" => null,
										   "visible" => true, "type" => PostType::$MAGAZINE);
		$this->contest_data_all = array("title" => "UN BEL TITOLO", "description" => "DESCRIZIONE", "rules" => "REGOLE",
										"start" => time(), "end" => time(),
										"prizes" => "PREMI", "subscriberType" => PostType::$NEWS);
	}
	function testPost() {
		
		$data = $this->post_data;
		$p1 = PostManager::createPost($data);
		
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
		
		$data = $this->post_data;
		$p = PostManager::createPost($data);
		$p1 = PostManager::commentPost($p, $this->author_id, $this->comment_text);
		
		if(count($p1->getComments()) == 0)
			return "<br />Comment test NOT PASSED: not added";
		$comm = $p1->getComments();
		if(isset($commentauthor) && $comm[0]->getAuthor() != $this->author_id)
			return "<br />Comment test NOT PASSED: author";
		if(isset($commenttext) && $comm[0]->getComment() != $this->comment_text)
			return "<br />Comment test NOT PASSED: comment";
		if($comm[0]->getPost() != $p1->getID())
			return "<br />Comment test NOT PASSED: post";
		
		
		return "<br />Comment test passed";
	}
	
	function testVote() {
		
		$data = $this->post_data;
		$p = PostManager::createPost($data);
		$p1 = PostManager::votePost($this->author_id, $p, $this->vote_value);
		
		if(count($p1->getVotes()) == 0)
			return "<br />Vote test NOT PASSED: not added";
		$vote = $p1->getVotes();
		if(isset($this->author_id) && $vote[0]->getAuthor() != $this->author_id)
			return "<br />Vote test NOT PASSED: author";
		if(isset($this->vote_value) && $vote[0]->getVote() != $this->vote_value)
			return "<br />Vote test NOT PASSED: comment";
		if($vote[0]->getPost() != $p1->getID())
			return "<br />Vote test NOT PASSED: post";
		
		return "<br />Vote test passed";
	}
	
	function testEditPost() {
		
		$data = $this->post_data;
		$p = PostManager::createPost($data);
		$data1 = $this->post_data2;
		$p1 = PostManager::editPost($p, $data1);
		$p = PostManager::loadPost($p1->getID());
		echo "<br />" . $p1 . "<br />" . $p; //DEBUG
		
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
		if(isset($data1["tags"]))
			$data["tags"] = $data1["tags"];
		if(isset($d5) && $p->getTags() != $data["tags"])
			return "<br />Post test NOT PASSED: tags";
		if(isset($data1["categories"]))
			$data["categories"] = $data1["categories"];
		if(isset($d6) && $p->getCategories() != $data["categories"])
			return "<br />Post test NOT PASSED: categories";
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
		
		$data = $this->post_data_all;
		
		$p1 = PostManager::createPost($data);
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
		$data = Filter::filterArray($this->post_data_all);
		$p = PostManager::createPost($data);
		$p1 = PostManager::commentPost($p, $this->author_id, $this->comment_text);
		
		if(count($p1->getComments()) == 0)
			return "<br />Comment test NOT PASSED: not added";
		$comm = $p1->getComments();
		$com = PostManager::loadComment($comm[0]->getId());
		$post = PostManager::loadPost($p1->getId());
		if($post === false || count($post->getComments()) == 0)
			return "<br />Comment test NOT PASSED: not added";
		//echo $comm[0] . "<br />" . $com; //DEBUG
		//echo "<p>" . $p1 . "</p><p>" . $post . "</p>"; //DEBUG
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
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$p1 = PostManager::votePost($this->author_id, $p, $this->vote_value);
		$p1 = PostManager::commentPost($p, $this->author_id, $this->comment_text);
		
		if(count($p1->getComments()) == 0)
			return "<br />Post deleting test NOT PASSED: not added";
		$comm = $p1->getComments();
		$votes = $p1->getVotes();
		
		$com = PostManager::loadComment($comm[0]->getId());
		$vote = PostManager::loadVote($votes[0]->getAuthor(), $votes[0]->getPost());
		$post = PostManager::loadPost($p1->getId());
		//echo "<p>" . $post . "</p><p>" . $com . "</p><p>" . $vote . "</p>"; //DEBUG
		if($com !== false && $post !== false && $vote !== false) {
			$post = PostManager::deletePost($post);
			
			$post = PostManager::loadPost($post->getID());
			$com = PostManager::loadComment($com->getID());
			$vote = PostManager::loadVote($vote->getAuthor(), $vote->getPost());
			//echo "<p>" . $post . "</p><p>" . $com . "</p><p>" . $vote . "</p>"; //DEBUG
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
		
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$p1 = PostManager::commentPost($p, $this->author_id, $this->comment_text);
		
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
		
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$p1 = PostManager::votePost($this->author_id, $p, $this->vote_value);
		
		if(count($p1->getVotes()) == 0)
			return "<br />Vote saving test NOT PASSED: not added";
		$votes = $p1->getVotes();
		$vote = PostManager::loadVote($votes[0]->getAuthor(),$votes[0]->getPost());
		$post = PostManager::loadPost($p1->getId());
		//echo $votes[0] . "<br />" . $vote; //DEBUG
		//echo "<br />" . $p1 . "<br />" . $post; //DEBUG
		if($post === false || count($post->getVotes()) == 0)
			return "<br />Vote saving test NOT PASSED: not added";
		if($vote === false) return "<br />Vote saving test NOT PASSED: not created";
		
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
		
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$p1 = PostManager::votePost($this->author_id, $p, $this->vote_value);
		
		if(count($p1->getVotes()) == 0)
			return "<br />Vote deleting test NOT PASSED: not added";
		$votes = $p1->getVotes();
		
		$vote = PostManager::loadVote($votes[0]->getAuthor(),$votes[0]->getPost());
		$post = PostManager::loadPost($p1->getId());
		if($vote !== false && $post !== false) {
			$vote = PostManager::removeVote($vote);
			
			$post = PostManager::loadPost($post->getID());
			$vote = PostManager::loadVote($vote->getAuthor(), $vote->getPost());
			
			if($post !== false && $vote === false) {
				if(count($post->getVotes()) == 0)
					return "<br />Vote deleting test passed";
			}
		}
		return "<br />Vote deleting test NOT PASSED: not deleted";
	}

	function testCollection() {
		$data = $this->collection_data_all;
		$p1 = CollectionManager::addCollection($data);
		
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
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$data1 = $this->collection_data_all;
		//$data1["content"] = array($p);
		$c = CollectionManager::addCollection($data1);
		$c1 = CollectionManager::addPostToCollection($p, $c);
		
		$c = CollectionManager::loadCollection($c1->getID());
		//echo "<p>" . $c1 . "</p><p>" . $c . "</p>"; //DEBUG
		
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
		require_once("common.php");
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$data1 = $this->collection_data_all;
		//$data1["content"] = array($p);
		$p1 = CollectionManager::addCollection($data1);
		
		$post = CollectionManager::loadCollection($p1->getID());
		//echo "<p>" . $p1 . "</p><p>" . $post . "</p>"; //DEBUG
		
		if($post === false)
			return "<br />Collection saving test NOT PASSED: not created";
		if($p1->getTitle() != $post->getTitle())
			return "<br />Collection saving test NOT PASSED: title";
		if($p1->getSubtitle() != $post->getSubtitle())
			return "<br />Collection saving test NOT PASSED: subtitle";
		//echo Filter::filterText($p1->getHeadline()) . "-" . Filter::filterText($post->getHeadline()); //DEBUG
		if($p1->getHeadline() != Filter::filterText($post->getHeadline()))
			return "<br />Collection saving test NOT PASSED: headline";
		if($p1->getAuthor() != $post->getAuthor())
			return "<br />Collection saving test NOT PASSED: author";
		if(isset($row["tags"]))
			if($p1->getTags() != $row["tags"])
				return "<br />Post saving test NOT PASSED: tags";
		if(isset($row["categories"]))
			if($p1->getCategories() != $row["categories"])
				return "<br />Post saving test NOT PASSED: categories";
		//echo serialize($p1->getContent()) . "-" . $post->getContent(); //DEBUG
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
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$data1 = $this->collection_data_all;
		$p2 = CollectionManager::addCollection($data1);
		$p1 = CollectionManager::voteCollection($this->author_id, $p2, $this->vote_value);
		
		if(count($p1->getVotes()) == 0)
			return "<br />Vote saving test NOT PASSED: not added";
		$votes = $p1->getVotes();
		$vote = PostManager::loadVote($votes[0]->getAuthor(),$votes[0]->getPost());
		$post = CollectionManager::loadCollection($p1->getId());
		if(count($post->getVotes()) == 0)
			return "<br />Vote saving test NOT PASSED: not added";
		//echo $votes[0] . "<br />" . $vote; //DEBUG
		//echo "<br />" . $p1 . "<br />" . $post; //DEBUG
		if($vote === false) return "<br />Vote saving test NOT PASSED: not created";
		
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
	 * Crea un contest con il post nel contenuto;
	 * Carica il contest e lo confronta con quello salvato in memoria;
	 */
	function testSaveContest() {
		//$data = $this->post_data_all;
		//$p = PostManager::createPost($data);
		$data1 = $this->contest_data_all;
		$p1 = ContestManager::createContest($data1);
		
		$contest = ContestManager::loadContest($p1->getID());
		//echo "<p>" . $p1 . "</p><p>" . $contest . "</p>"; //DEBUG
		
		if($contest === false)
			return "<br />Contest saving test NOT PASSED: not created";
		if($p1->getTitle() != $contest->getTitle())
			return "<br />Contest saving test NOT PASSED: title";
		if($p1->getDescription() != $contest->getDescription())
			return "<br />Contest saving test NOT PASSED: description";
		if($p1->getRules() != $contest->getRules())
			return "<br />Contest saving test NOT PASSED: rules";
		if($p1->getPrizes() != $contest->getPrizes())
			return "<br />Contest saving test NOT PASSED: prizes";
		//echo serialize($p1->getContent()) . "-" . $post->getContent(); //DEBUG
		if($p1->getStart() != $contest->getStart())
			return "<br />Contest saving test NOT PASSED: start";
		if($p1->getEnd() != $contest->getEnd())
			return "<br />Contest saving test NOT PASSED: end";
		return "<br />Save Contest test passed";
	}
	
	/**
	 * Crea un post;
	 * Crea un contest con il post nel contenuto;
	 * Carica il contest e lo confronta con quello salvato in memoria;
	 */
	function testSubscribeToContest() {
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$data1 = $this->contest_data_all;
		$p1 = ContestManager::createContest($data1);
		$p2 = ContestManager::subscribePostToContest($p, $p1);
		
		$contest = ContestManager::loadContest($p2->getID());
		//echo "<p>" . $p2 . "</p><p>" . $contest . "</p>"; //DEBUG
		
		if($contest === false)
			return "<br />Contest subscribing test NOT PASSED: not created";
		if(count($p2->getSubscribers()) == 0)
			return "<br />Contest subscribing test NOT PASSED: not added";
		if(count($contest->getSubscribers()) == 0)
			return "<br />Contest subscribing test NOT PASSED: not loaded";
		return "<br />Contest subscribing test passed";
	}
	
	/**
	 * Crea un post;
	 * Crea un contest con il post nel contenuto;
	 * Carica il contest e lo confronta con quello salvato in memoria;
	 */
	function testUnsubscribeToContest() {
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$data1 = $this->contest_data_all;
		$p1 = ContestManager::createContest($data1);
		$p2 = ContestManager::subscribePostToContest($p, $p1);
		$contest = ContestManager::loadContest($p2->getID());
		//echo "<p>" . $p2 . "</p><p>" . $contest . "</p>"; //DEBUG
		
		if(count($p2->getSubscribers()) == 0)
			return "<br />Contest unsubscribing test NOT PASSED: not added";
		if(count($contest->getSubscribers()) == 0)
			return "<br />Contest unsubscribing test NOT PASSED: not loaded";
		
		$p1 = ContestManager::unsubscribePostFromContest($p, $p2);
		$contest2 = ContestManager::loadContest($p2->getID());
		//echo "<p>" . $p2 . "</p><p>" . $contest2 . "</p>"; //DEBUG
		
		if(count($contest2->getSubscribers()) != 0)
			return "<br />Contest subscribing test NOT PASSED: not removed";
		if(count($contest2->getSubscribers()) != 0)
			return "<br />Contest unsubscribing test NOT PASSED: loaded";
		return "<br />Contest unsubscribing test passed";
	}
	
	/**
	 * Crea un post;
	 * Crea un contest con il post nel contenuto;
	 * Carica il contest e lo confronta con quello salvato in memoria;
	 */
	function testDeleteContest() {
		$data = $this->post_data_all;
		$p = PostManager::createPost($data);
		$data1 = $this->contest_data_all;
		$p1 = ContestManager::createContest($data1);
		$p2 = ContestManager::subscribePostToContest($p, $p1);
		$contest = ContestManager::loadContest($p2->getID());
		//echo "<p>" . $p2 . "</p><p>" . $contest . "</p>"; //DEBUG
		
		if(count($p2->getSubscribers()) == 0)
			return "<br />Contest deleting test NOT PASSED: not added";
		if(count($contest->getSubscribers()) == 0)
			return "<br />Contest deleting test NOT PASSED: not loaded";
		
		$p1 = ContestManager::deleteContest($p2);
		$contest = ContestManager::loadContest($p1->getID());
		//echo "<p>" . $p2 . "</p><p>" . $contest . "</p>"; //DEBUG
		
		if($contest !== false)
			return "<br />Contest deleting test NOT PASSED: loaded";
		return "<br />Contest deleting test passed";
	}
	
	function testPermalink() {
		$data = Filter::filterArray($this->post_data_all);
		$p = PostManager::createPost($data);
		
		echo $p->getPermalink();
		echo "<br />" . $p->getFullPermalink();
	}
	
	function insertFirstNews() {
		$p = PostManager::createPost($this->post_serious);
		echo $p->getFullPermalink();
	}
}
?>