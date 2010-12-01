<?php
require_once("post/Post.php");

/**
 * Gestisce i post di ogni tipo (non le Collection).
 *
 */
class PostManager {
	/**
	 * Aggiunge un post "semplice" al sistema.
	 *
	 * @param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo del post (string filtrata)
	 * subtitle: sottotitolo del post (string filtrata)
	 * headline: occhiello del post (string filtrata)
	 * author: id dell'autore (long)
	 * tags: array di oggetti Tag
	 * categories: array di oggetti Category
	 * content: il testo di un articolo (string filtrata), l'indirizzo del videoreportage o l'elenco di indirizzi delle foto di un fotoreportage
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 * @param type: tipo di post, deve essere incluso in PostType
	 * 
	 * @return: l'articolo creato.
	 */
	static function createPost($data) {
		require_once("common.php");
		if(isset($data["ID"])) unset($data["ID"]);
		$data = Filter::filterArray($data);
		
		require_once("post/PostCommon.php");
		if(!isset($data["type"]))
		   return false;
		$p = false;
		if($data["type"] == PostType::NEWS) {
			$p = new News($data);
		} else if($data["type"]  == PostType::VIDEOREPORTAGE) {
			$p = new VideoReportage($data);
		} else
			return false;
		
		$p->save();
		
		return $p;
	}
	
	/**
	 * Modifica un post "semplice".
	 * 
	 * @param data: array associativo contenente i dati.
	 * Le chiavi ricercate dal sistema per questo array sono:
	 * title: titolo del post (string filtrata)
	 * subtitle: sottotitolo del post (string filtrata)
	 * headline: occhiello del post (string filtrata)
	 * tags: array di oggetti Tag
	 * categories: array di oggetti Category
	 * content: il testo di un articolo (filtrato), l'indirizzo del videoreportage o l'elenco di indirizzi di foto di un fotoreportage
	 * visibile: indica la visibilità dell'articolo se non visibile è da considerare come una bozza (boolean)
	 *
	 * @return: l'articolo modificato.
	 */
	static function editPost($post, $data) {
		require_once("common.php");
		if(isset($data["ID"])) unset($data["ID"]);
		$data = Filter::filterArray($data);
		
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
			
		$post->update();
		
		return $post;
	}
	
	/**
	 * Elimina il post dal sistema.
	 *
	 * @param post: il post da eliminare.
	 * @return: il post eliminato.
	 */
	static function deletePost($post) {
		return $post->delete();
	}
	
	/**
	 * Aggiunge un report al post selezionato e lo salva nel database.
	 *
	 * @param $author: id dell'autore del commento
	 * @param $post: variabile di tipo Post
	 * @param $report: testo del report
	 * @return: post aggiornato.
	 */
	static function reportPost($author, $post, $report) {
		require_once("common.php");
		$report = Filter::filterText($report);
		
		require_once("post/PostCommon.php");
		$r = new Report($author, $post->getID(), $report);
		$r->save();
		
		return $post->addReport($r);
	}
	
	/**
	 * Aggiunge un report al commento selezionato e lo salva nel database.
	 *
	 * @param $author: id dell'autore del commento
	 * @param $comment: variabile di tipo Comment
	 * @param $report: testo del report
	 * @return: commento aggiornato.
	 */
	static function reportComment($author, $comment, $report) {
		//TODO da implementare
		return false;
	}
	
	/**
	 * Aggiunge un voto al post selezionato e lo salva nel database.
	 *
	 * @param author: id dell'autore del voto
	 * @param post: variabile di tipo Post
	 * @param comment: il voto
	 * @return: post aggiornato.
	 */
	static function votePost($author, $post, $vote) {
		require_once("post/PostCommon.php");
		$v = self::loadVote($author, $post->getID());
		if($v !== false)
			self::removeVote($v);
		//else
		//	echo $author . "-" . $post->getID();
			
		$v = new Vote($author, $post->getID(), $vote);
		$v->save();
		$post->addVote($v);
		return $post;
	}
	
	/**
	 * Aggiunge un commento al post selezionato e lo salva nel database.
	 *
	 * @param author: id dell'autore del commento
	 * @param post: variabile di tipo Post
	 * @param comment: testo del commento
	 * @return post: aggiornato.
	 */
	static function commentPost($post, $author, $comment) {
		require_once("common.php");
		$comment = Filter::filterText($comment);
		
		require_once("post/PostCommon.php");
		$c = new Comment(array("author" => $author, "post" => $post->getID(), "comment" => $comment));
		$c->save();
		
		$post->addComment($c);
		return $post;
	}
	
	/**
	 * Elimina il commento dal sistema.
	 *
	 * @param comment: il commento da eliminare.
	 * @return: il commento eliminato.
	 */
	static function removeComment($comment) {
		require_once("post/PostCommon.php");
		return $comment->delete();
	}
	
	static function searchForLikelihood($post) {
		return SearchManager::searchForLikelihood($post);
	}
	
	/**
	 * Iscrive un Post ad un Contest.
	 *
	 * @param post: il post da iscrivere.
	 * @param contest: il contest a cui iscrivere il post
	 * @return: il contest aggiornato.
	 */
	static function subscribePostToContest($post, $contest) {
		require_once("post/contest/ContestManager.php");
		return ContestManager::subscribePostToContest($post, $contest);
	}
	
	static function removeVote($vote) {
		require_once("post/PostCommon.php");
		return $vote->delete();
	}
	
	static function loadComment($id) {
		require_once("post/PostCommon.php");
		return Comment::loadFromDatabase($id);
		
	}
	
	static function loadVote($author, $post) {
		require_once("post/PostCommon.php");
		return Vote::loadFromDatabase($author, $post);
	}
	
	static function loadPost($id) {
		return Post::loadFromDatabase($id);
	}
	
	/**
	 * Aggiorna il permalink. È un'azione da non eseguire in automatico ma solo se l'utente lo richiede.
	 * Perché se qualcuno ha usato il suo permalink precedente, i link non funzioneranno più.
	 */
	static function updatePermalinkForPost($post) {
		return $post->setPermalink($post->getPermalink(true), true);
	}
	
	static function loadPostByPermalink($permalink) {
		return Post::loadByPermalink($permalink);
	}
	
	static function postExists($post) {
		return Post::exists($post);
	}
}
?>