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
	
	static function voteCollection($author, $collection, $vote) {
		$v = new Vote($author, $collection->getID(), $vote);
		$v->save(SavingMode::$INSERT);
		
		$collection->addVote($v);
		return $collection;
	}
	
	
}

?>