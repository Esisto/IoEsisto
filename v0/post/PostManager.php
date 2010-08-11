<?php

/**
 * Gestisce i post di ogni tipo (non i Collection).
 *
 */
class PostManager {
	/**
	 * Aggiunge un post "semplice" al sistema.
	 *
	 * param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo del post (string filtrata)
	 * subtitle: sottotitolo del post (string filtrata)
	 * headline: occhiello del post (string filtrata)
	 * author: id dell'autore (long)
	 * tags: array di oggetti Tag
	 * categories: array di oggetti Category
	 * content: il testo di un articolo (string filtrata), l'indirizzo del videoreportage o l'elenco di indirizzi delle foto di un fotoreportage
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 * param type: tipo di post, deve essere incluso in PostType
	 * 
	 * return: l'articolo creato.
	 */
	static function addPost($data, $type) {
		$p = null;
		if($type == PostType::$NEWS) 
			$p = new News($data);
		else if($type == PostType::$PHOTOREPORTAGE) 
			$p = new PhotoReportage($data);
		else if($type == PostType::$VIDEOREPORTAGE) 
			$p = new VideoReportage($data);
		else
			return null;
		
		$p->save(SavingMode::$INSERT);
		
		return $p;
	}
	
	/**
	 * Modifica un post "semplice".
	 * 
	 * param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo del post (string filtrata)
	 * subtitle: sottotitolo del post (string filtrata)
	 * headline: occhiello del post (string filtrata)
	 * tags: array di oggetti Tag
	 * categories: array di oggetti Category
	 * content: il testo di un articolo (filtrato), l'indirizzo del videoreportage o l'elenco di indirizzi di foto di un fotoreportage
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 *
	 * return: l'articolo modificato.
	 */
	static function editPost($post, $data) {
		if(isset($data["title"]))
			$post->setTitle($data["title"]);
		if(isset($data["subtitle"]))
			$post->setSubtitle($data["subtitle"]);
		if(isset($data["headline"]))
			$post->setHeadline($data["headline"]);
		if(isset($data["tags"]))
			$post->setTags($data["tags"]);
		if(isset($data["categories"]))
			$post->setCategories($data["categories"]);
		if(isset($data["content"]))
			$post->setContent($data["content"]);
		if(isset($data["visible"]))
			$post->setVisible($data["visible"]);
			
		$post->save(SavingMode::$UPDATE);
		
		return $post;
	}
	
	/**
	 * Elimina il post dal sistema.
	 *
	 * param post: il post da eliminare.
	 * return: il post eliminato.
	 */
	static function deletePost($post) {
		$post->delete();
		
		return $post;
	}
	static function signalPost() {
		
	}
	static function votePost($author, $post, $vote) {
		$v = new Vote($author, $post->getID(), $vote);
		$v->save(SavingMode::$INSERT);
		
		$post->addVote($v);
		
		return $post;
	}
	
	/**
	 * Aggiunge un commento al post selezionato e lo salva nel database.
	 *
	 * param author id dell'autore del commento
	 * param post variabile di tipo Post
	 * param comment testo del commento
	 * return post aggiornato.
	 */
	static function commentPost($post, $author, $comment) {
		$c = new Comment($author, $post->getID(), $comment);
		$c->save(SavingMode::$INSERT);
		$post->addComment($c);
		
		return $post;
	}
	
	/**
	 * Elimina il commento dal sistema.
	 *
	 * param comment: il commento da eliminare.
	 * return: il commento eliminato.
	 */
	static function removeComment($comment) {
		$comment->delete();
		
		return $comment;
	}
	
	static function signalComment() {
		
	}
	
	static function searchForLikelihood() {
		
	}
	
	/**
	 * Iscrive un Post ad un Contest.
	 * TODO FORSE DA METTERE IN ContestManager.
	 *
	 * param post: il post da iscrivere.
	 * param contest: il contest a cui iscrivere il post
	 * return: il contest aggiornato.
	 */
	static function subscribePostToContest($post, $contest) {
		$contest->subscribePost($post);
		$contest->save(SavingMode::$UPDATE);
		
		return $contest;
	}
	
	static function removeVote($vote) {
		$vote->delete();
		
		return $vote;
	}
}
?>