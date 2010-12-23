<?php
require_once("dataobject/Collection.php");
require_once("dataobject/PhotoReportage.php");
require_once("dataobject/Album.php");
require_once("dataobject/Magazine.php");
require_once("manager/PostManager.php");
require_once("dao/PostDao.php");
require_once("filter.php");

/**
 * A differenza dello studio, CollectionManager non è sottoclasse di PostManager. ma è ancora in dubbio…
 * Gestisce le operazioni sulle Collection.
 *
 */
class CollectionManager {
	/**
	 * Aggiunge un post "collezione" al sistema.
	 *
	 * @param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo della collection (string filtrata)
	 * subtitle: sottotitolo della collection (string filtrata)
	 * headline: occhiello della collection (string filtrata)
	 * author: id dell'autore (long)
	 * tags: array di oggetti Tag
	 * categories: array di oggetti Category
	 * content: array di post "semplici"
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 * @param type: tipo di collection, deve essere incluso in CollectionType
	 * 
	 * @return: la collection creata o FALSE se c'è un errose
	 */
	static function createCollection($data) {
		if(isset($data["ID"])) unset($data["ID"]);
		$data = Filter::filterArray($data);
		
		if(!isset($data[Post::TYPE]))
		   throw new Exception("Il post da creare è di un tipo sconosciuto.");
		$p = false;
		switch ($data[Post::TYPE]) {
			case Post::NEWS:
			case Post::VIDEOREP:
				return PostManager::createPost($data);
				break;
				
			case Post::COLLECTION:
				if(!$p)
					$p = new Collection($data);
			case Post::ALBUM:
				if(!$p)
					$p = new Album($data);
			case Post::MAGAZINE:
				if(!$p)
					$p = new Magazine($data);
			case Post::PHOTOREP:
				if(!$p) 
					$p = new PhotoReportage($data);

				$postdao = new PostDao();
				$post = $postdao->save($p);
				return $post;
		}
		throw new Exception("Il post da creare è di un tipo sconosciuto.");
	}
	
	/**
	 * Modifica un post "collezione".
	 * 
	 * @param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo della collection (string filtrata)
	 * subtitle: sottotitolo della collection (string filtrata)
	 * headline: occhiello della collection (string filtrata)
	 * tags: array di oggetti Tag
	 * categories: array di oggetti Category
	 * content: array di id di Post "semplici".
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 *
	 * @return: la collection modificata.
	 */
	static function editCollection($collection, $data) {
		return PostManager::editPost($collection, $data);
	}
	
	/**
	 * Elimina la collection dal sistema.
	 *
	 * @param post: la collection da eliminare.
	 * @return: la collection eliminata.
	 */
	static function deleteCollection($collection) {
		return PostManager::deletePost($collection);
	}
	
	/**
	 * Aggiunge un report alla collection selezionato e lo salva nel database.
	 *
	 * @param $author: id dell'autore del commento
	 * @param $post: variabile di tipo Collection
	 * @param $report: testo del report
	 * @return: post aggiornato.
	 */
	static function reportCollection($author, $collection, $report) {
		return PostManager::reportPost($author, $collection, $report);
	}
	
	/**
	 * Aggiunge un voto ad una Collection.
	 * 
	 * @param author: l'autore del commento.
	 * @param collection: la collezione in cui aggiungere il voto.
	 * @param vote: il voto (boolean).
	 * @return: la collezione aggiornata.
	 */
	static function voteCollection($author, $collection, $vote) {
		return PostManager::votePost($author,$collection,$vote);
	}
	
	/**
	 * Aggiunge un commento ad una Collection.
	 * 
	 * @param author: l'autore del commento.
	 * @param collection: la collezione in cui aggiungere il voto.
	 * @param comment: il testo del commento.
	 * @return: la collezione aggiornata.
	 */
	static function commentCollection($author, $collection, $comment) {
		return PostManager::commentPost($collection,$author,$comment);
	}
	
	/**
	 * Rimuove un commento dal sistema.
	 * 
	 * @param comment: il commento da rimuovere.
	 * @return: il commento rimosso.
	 */
	static function removeComment($comment) {
		return PostManager::removeComment($comment);
	}
	
	/**
	 * Aggiunge un Post ad una Collection.
	 * 
	 * @param post: il post da aggiungere.
	 * @param collection: la collezione in cui aggiungere il post.
	 * @return: la collezione aggiornata.
	 */
	static function addPostToCollection($post, $collection) {
		$collection->addPost($post);
		$postdao = new PostDao();
		return $postdao->update($post, Session::getUser());
	}
	
	static function loadCollection($id) {
		return PostManager::loadPost($id);
	}

	static function loadCollectionByPermalink($permalink) {
		return PostManager::loadPostByPermalink($permalink);
	}
}

?>