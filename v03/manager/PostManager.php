<?php
require_once("dataobject/Post.php");
require_once("dataobject/VideoReportage.php");
require_once("dataobject/News.php");
require_once("manager/CollectionManager.php");
require_once("dao/PostDao.php");
require_once("filter.php");

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
		if(isset($data["ID"])) unset($data["ID"]);
		$data = Filter::filterArray($data);
		
		if(!isset($data[Post::TYPE]))
		   throw new Exception("Il post da creare è di un tipo sconosciuto.");
		$p = false;
		switch ($data[Post::TYPE]) {
			case Post::NEWS:
				$p = new News($data);
			case Post::VIDEOREP:
				if(!$p)
					$p = new VideoReportage($data);

				$postdao = new PostDao();
				$post = $postdao->save($p);
				return $post;
			case Post::COLLECTION:
			case Post::ALBUM:
			case Post::MAGAZINE:
			case Post::PHOTOREP:
				return CollectionManager::createCollection($data);
		}
		throw new Exception("Il post da creare è di un tipo sconosciuto.");
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
		if(isset($data["ID"])) unset($data["ID"]);
		$data = Filter::filterArray($data);
		
		$p->edit($data);
		$postdao = new PostDao();
		$post = $postdao->update($p, Session::getUser());
		return $post;
	}
	
	/**
	 * Elimina il post dal sistema.
	 *
	 * @param post: il post da eliminare.
	 * @return: il post eliminato.
	 */
	static function deletePost($post) {
		$postdao = new PostDao();
		return $postdao->delete($post);
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
		$report = Filter::filterText($report);
		
		require_once("dao/ReportDao.php");
		require_once("dataobject/Report.php");
		$r = new Report($author, $post, $report);
		$reportDao = new ReportDao();
		return $reportDao->save($r);
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
		$report = Filter::filterText($report);
		
		require_once("dao/ReportDao.php");
		require_once("dataobject/Report.php");
		$r = new Report($author, $comment, $report);
		$reportDao = new ReportDao();
		return $reportDao->save($r);
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
		require_once("dao/VoteDao.php");
		$votedao = new VoteDao();
		
		$votedao->save($post, $author, $vote);
		$v = $votedao->getVote($post);
		return $post->setVote($v);
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
		$comment = Filter::filterText($comment);
		
		require_once("dataobject/Comment.php");
		require_once("dao/CommentDao.php");
		$c = new Comment(array("author" => $author, "post" => $post->getID(), "comment" => $comment));
		$commentdao = new CommentDao();
		$comm = $commentdao->save($c);
		
		$post->addComment($comm);
		return $post;
	}
	
	/**
	 * Elimina il commento dal sistema.
	 *
	 * @param comment: il commento da eliminare.
	 * @return: il commento eliminato.
	 */
	static function removeComment($comment) {
		require_once("dao/CommentDao.php");
		$commentdao = new CommentDao();
		$comm = $commentdao->delete($comment);
		return $comm;
	}
	
	static function searchForLikelihood($post) {
		require_once 'manager/SearchManager.php';
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
		require_once("manager/ContestManager.php");
		return ContestManager::subscribePostToContest($post, $contest);
	}
	
	static function loadComment($id) {
		require_once("dao/CommentDao.php");
		$commentdao = new CommentDao();
		
		return $commentdao->load($id);
	}
	
	static function loadPost($id) {
		$postdao = new PostDao();
		
		return $postdao->load($id);
	}
	
	static function loadPostByPermalink($permalink) {
		$postdao = new PostDao();
		return $postdao->loadByPermalink($permalink);
	}
	
	/**
	 * Aggiorna il permalink. È un'azione da non eseguire in automatico ma solo se l'utente lo richiede.
	 * Perché se qualcuno ha usato il suo permalink precedente, i link non funzioneranno più.
	 * @deprecated
	 */
	static function updatePermalinkForPost($post) {
		return $post->setPermalink($post->getPermalink(true), true);
	}
	
	static function postExists($post) {
		$postdao = new PostDao();
		return $postdao->exists($post);
	}
}
?>