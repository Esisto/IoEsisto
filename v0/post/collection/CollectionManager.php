<?php

/**
 * A differenza dello studio, CollectionManager non è sottoclasse di PostManager. ma è ancora in dubbio…
 * Gestisce le operazioni sulle Collection.
 *
 */
class CollectionManager {
	/**
	 * Aggiunge un post "collezione" al sistema.
	 *
	 * param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo della collection (string filtrata)
	 * subtitle: sottotitolo della collection (string filtrata)
	 * headline: occhiello della collection (string filtrata)
	 * author: id dell'autore (long)
	 * tags: array di oggetti Tag
	 * categories: array di oggetti Category
	 * content: array di post "semplici"
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 * param type: tipo di collection, deve essere incluso in CollectionType
	 * 
	 * return: la collection creata.
	 */
	static function addCollection($data, $type) {
		$c = null;
		if($type == CollectionType::$ALBUM)
			$c = new Album($data);
		else if($type == CollectionType::$MAGAZINE)
			$c = new Magazine($data);
		else if($type == CollectionType::$PLAYLIST)
			$c = new Playlist($data);
		else
			return null;
		
		$c->save(SavingMode::$INSERT);
		
		return $c;
	}
	
	/**
	 * Modifica un post "collezione".
	 * 
	 * param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo della collection (string filtrata)
	 * subtitle: sottotitolo della collection (string filtrata)
	 * headline: occhiello della collection (string filtrata)
	 * tags: array di oggetti Tag
	 * categories: array di oggetti Category
	 * content: array di id di Post "semplici".
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 *
	 * return: la collection modificata.
	 */
	static function editCollection($collection, $data) {
		if(isset($data["title"]))
			$collection->setTitle($data["title"]);
		if(isset($data["subtitle"]))
			$collection->setSubtitle($data["subtitle"]);
		if(isset($data["headline"]))
			$collection->setHeadline($data["headline"]);
		if(isset($data["tags"]))
			$collection->setTags($data["tags"]);
		if(isset($data["categories"]))
			$collection->setCategories($data["categories"]);
		if(isset($data["content"]))
			$collection->setContent($data["content"]);
		if(isset($data["visible"]))
			$collection->setVisible($data["visible"]);
			
		$collection->save(SavingMode::$UPDATE);
		
		return $collection;
	}
	
	/**
	 * Elimina la collection dal sistema.
	 *
	 * param post: la collection da eliminare.
	 * return: la collection eliminata.
	 */
	static function deleteCollection($collection) {
		$collection->delete();
		
		return $collection;
	}
	
	static function signalCollection() {
		
	}
	
	/**
	 * Aggiunge un voto ad una Collection.
	 * 
	 * param author: l'autore del commento.
	 * param collection: la collezione in cui aggiungere il voto.
	 * param vote: il voto (boolean).
	 * return: la collezione aggiornata.
	 */
	static function voteCollection($author, $collection, $vote) {
		$v = new Vote($author, $collection->getID(), $vote);
		$v->save(SavingMode::$INSERT);
		
		return $collection->addVote($v);
	}
	
	/**
	 * Rimuove un voto dal sistema.
	 * 
	 * param vote: il voto da rimuovere.
	 * return: il voto rimosso.
	 */
	static function removeVote($vote) {
		$vote->delete();
		
		return $vote;
	}
	
	/**
	 * Aggiunge un commento ad una Collection.
	 * 
	 * param author: l'autore del commento.
	 * param collection: la collezione in cui aggiungere il voto.
	 * param comment: il testo del commento.
	 * return: la collezione aggiornata.
	 */
	static function commentCollection($author, $collection, $comment) {
		$c = new Comment($author, $collection->getID(), $comment);
		$c->save(SavingMode::$INSERT);
		
		return $collection->addComment($c);
	}
	
	/**
	 * Rimuove un commento dal sistema.
	 * 
	 * param comment: il commento da rimuovere.
	 * return: il commento rimosso.
	 */
	static function removeComment($comment) {
		$comment->delete();
		
		return $comment;
	}
	
	/**
	 * Aggiunge un Post ad una Collection.
	 * 
	 * param post: il post da aggiungere.
	 * param collection: la collezione in cui aggiungere il post.
	 * return: la collezione aggiornata.
	 */
	static function addPostToCollection($post, $collection) {
		return $collection->addPost($post);
	}
}

?>