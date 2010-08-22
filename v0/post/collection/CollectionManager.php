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
	 * return: la collection creata o FALSE se c'è un errose
	 */
	static function addCollection($data) {
		require_once("common.php");
		$data = Filter::filterArray($data);
		
		require_once("post/PostCommon.php");
		if(!isset($data["type"]))
		   return false;
		$c = false;
		if($data["type"] == PostType::$ALBUM) {
			require_once("post/collection/Album.php");
			$c = new Album($data);
		} else if($data["type"] == PostType::$MAGAZINE) {
			require_once("post/collection/Magazine.php");
			$c = new Magazine($data);
		} else if($data["type"] == PostType::$PLAYLIST) {
			require_once("post/collection/Playlist.php");
			$c = new Playlist($data);
		} else if($data["type"] == PostType::$PHOTOREPORTAGE) {
			require_once("post/collection/PhotoReportage.php");
			$c = new PhotoReportage($data);
		} else
			return false;
		
		$c->save();
		
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
		require_once("common.php");
		$data = Filter::filterArray($data);
		
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
			
		$collection->update();
		
		return $collection;
	}
	
	/**
	 * Elimina la collection dal sistema.
	 *
	 * param post: la collection da eliminare.
	 * return: la collection eliminata.
	 */
	static function deleteCollection($collection) {
		require_once("post/PostManager.php");
		return PostManager::deletePost($collection);
	}
	
	/**
	 * Aggiunge un report alla collection selezionato e lo salva nel database.
	 *
	 * param $author: id dell'autore del commento
	 * param $post: variabile di tipo Collection
	 * param $report: testo del report
	 * return: post aggiornato.
	 */
	static function reportCollection($author, $collection, $report) {
		require_once("post/PostManager.php");
		return PostManager::reportPost($author, $collection, $report);
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
		require_once("post/PostManager.php");
		return PostManager::votePost($author,$collection,$vote);
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
		require_once("post/PostManager.php");
		return PostManager::commentPost($collection,$author,$comment);
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
	
	/**
	 *
	 *
	 */
	static function loadCollection($id) {
		require_once("post/PostManager.php");
		return Collection::loadFromDatabase($id);
	}

}

?>