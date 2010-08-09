<?php

class PostManager {
	
	static function addPost($title, $subtitle, $occh, $author, $tags, $categories, $type, $content, $visible) {
		$p = null;
		if($type == Post::$pTypes["NEWS"]) 
			$p = new News($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible);
		if($type == Post::$pTypes["PHOTOREPORTAGE"]) 
			$p = new PhotoReportage($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible);
		if($type == Post::$pTypes["VIDEOREPORTAGE"]) 
			$p = new VideoReportage($title, $subtitle, $occh, $author, $tags, $categories, $content, $visible);
		
		// TODO inserisci nel database
		
		return $p;
	}
	
	function editPost() {
		
	}
	function deletePost() {
		
	}
	function signalPost() {
		
	}
	function votePost() {
		
	}
	
	/**
	 *
	 * param author id dell'autore del commento
	 * param post variabile di tipo Post
	 * param comment testo del commento
	 * return post aggiornato
	 */
	static function addComment($author, $post, $comment) {
		$c = new Comment($author, $post->getID(), $comment);
		$post->addComment($c);
		return $post;	
	}
	function removeComment() {
		
	}
	function signalComment() {
		
	}
	function searchForLikelihood() {
		
	}
	function addToCollection() {
		
	}
	function subscribeToContest() {
		
	}
	
}

function filterText($t) {
	return $t; //TODO
}


?>