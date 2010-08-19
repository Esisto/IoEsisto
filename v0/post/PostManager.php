<?php
require_once("post/Post.php");

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
		require_once("common.php");
		$data = Filter::filterArray($data);
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
		require_once("common.php");
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
		return $post->delete();
	}
	
	/**
	 * Aggiunge un report al post selezionato e lo salva nel database.
	 *
	 * param $author: id dell'autore del commento
	 * param $post: variabile di tipo Post
	 * param $report: testo del report
	 * return: post aggiornato.
	 */
	static function reportPost($author, $post, $report) {
		require_once("common.php");
		$report = Filter::filterText($report);
		
		$r = new Report($author, $post->getID(), $report);
		$r->save(SavingMode::$INSERT);
		
		return $post->addReport($r);
	}
	
	/**
	 * Aggiunge un report al commento selezionato e lo salva nel database.
	 *
	 * param $author: id dell'autore del commento
	 * param $comment: variabile di tipo Comment
	 * param $report: testo del report
	 * return: commento aggiornato.
	 */
	static function reportComment($author, $comment, $report) {
		//TODO Not iplemented
		return false;
	}
	
	/**
	 * Aggiunge un voto al post selezionato e lo salva nel database.
	 *
	 * param author: id dell'autore del voto
	 * param post: variabile di tipo Post
	 * param comment: il voto
	 * return: post aggiornato.
	 */
	static function votePost($author, $post, $vote) {
		$v = new Vote($author, $post->getID(), $vote);
		$v->save(SavingMode::$INSERT);
		
		$post->addVote($v);
		
		return $post;
	}
	
	/**
	 * Aggiunge un commento al post selezionato e lo salva nel database.
	 *
	 * param author: id dell'autore del commento
	 * param post: variabile di tipo Post
	 * param comment: testo del commento
	 * return post: aggiornato.
	 */
	static function commentPost($post, $author, $comment) {
		require_once("common.php");
		$comment = Filter::filterText($comment);
		
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
		return $comment->delete();
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
		return $vote->delete();
	}
	
	static function loadComment($id) {
		require_once("query.php");
		
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Comment");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("cm_ID"),Operator::$UGUALE,$id)),
													 array()));
		$col = false;
		if($rs !== false) {
			while($row = mysql_fetch_assoc($rs)) {
				$col = new Comment(intval($row["cm_author"]), intval($row["cm_post"]), $row["cm_comment"]);
				$col->setID($row["cm_ID"])->setCreationDate(time($row["cm_creationDate"]));
				break;
			}
		}
		if(!$col)
			$GLOBALS["query_error"] = "NOT FOUND";
		return $col;
	}
	
	static function loadVote($author, $post) {
		require_once("query.php");
		
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Vote");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("vt_author"),Operator::$UGUALE,$author),
														   new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$post)),
													 array()));
		$vote = false;
		if($rs !== false) {
			while($row = mysql_fetch_assoc($rs)) {
				$vote = new Vote(intval($row["vt_author"]), intval($row["vt_post"]), $row["vt_vote"] > 0);
				$vote->setCreationDate(time($row["cm_creationDate"]));
				break;
			}
		}
		if(!$vote)
			$GLOBALS["query_error"] = "NOT FOUND";
		return $vote;
	}
	
	static function loadPost($id) {
		require_once("query.php");
		$q = new Query();
		$table = $q->getDBSchema()->getTable("Post");
		$rs = $q->execute($s = $q->generateSelectStm(array($table),
													 array(),
													 array(new WhereConstraint($table->getColumn("ps_ID"),Operator::$UGUALE,$id)),
													 array()));
		$p = false;
		if($rs !== false) {
			//echo serialize(mysql_fetch_assoc($rs)); //DEBUG
			while($row = mysql_fetch_assoc($rs)) {
				$data = array("title" => $row["ps_title"], "subtitle" => $row["ps_subtitle"],
							  "headline" => $row["ps_headline"], "author"=> intval($row["ps_author"]),
							  "content" => $row["ps_content"], "visible" => $row["ps_visible"] == 1,
							  "place" => intval($row["ps_place"]));
				//echo $row["ps_type"]; //DEBUG
				if($row["ps_type"] == PostType::$NEWS) 
					$p = new News($data);
				else if($row["ps_type"] == PostType::$PHOTOREPORTAGE) {
					$data["content"] = unserialize($row["ps_content"]);
					$p = new PhotoReportage($data);
				}
				else if($row["ps_type"] == PostType::$VIDEOREPORTAGE) 
					$p = new VideoReportage($data);
				else if($row["ps_type"] == PostType::$COLLECTION ||
						$row["ps_type"] == CollectionType::$ALBUM ||
						$row["ps_type"] == CollectionType::$MAGAZINE ||
						$row["ps_type"] == CollectionType::$PLAYLIST) {
					require_once("post/collection/CollectionManager.php");
					echo ($p =CollectionManager::loadCollection($id));
					return $p;
				}
				$p->setID($row["ps_ID"])->setCreationDate(time($row["ps_creationDate"]));
				if(is_null($row["ps_creationDate"]) || !is_numeric($row["ps_creationDate"]) || $row["ps_creationDate"] == 0)
					$p->setModificationDate(time($row["ps_creationDate"]));
				break;
			}
		}
		if($p !== false) {
			$table = $q->getDBSchema()->getTable("Comment");
			$rs = $q->execute($s = $q->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn("cm_post"),Operator::$UGUALE,$id)),
														 array()));
			if($rs !== false) {
				$comm = array();
				while($row = mysql_fetch_assoc($rs)) {
					$com = new Comment(intval($row["cm_author"]), intval($row["cm_post"]), $row["cm_comment"]);
					$com->setID($row["cm_ID"])->setCreationDate(time($row["cm_creationDate"]));
					$comm[] = $com;
				}
				$p->setComments($comm);
			}
			$table = $q->getDBSchema()->getTable("Vote");
			$rs = $q->execute($s = $q->generateSelectStm(array($table),
														 array(),
														 array(new WhereConstraint($table->getColumn("vt_post"),Operator::$UGUALE,$id)),
														 array()));
			if($rs !== false) {
				$votes = array();
				while($row = mysql_fetch_assoc($rs)) {
					$vote = new Vote(intval($row["vt_author"]), intval($row["vt_post"]), $row["vt_vote"] > 0);
					$vote->setCreationDate(time($row["vt_creationDate"]));
					$votes[] = $vote;
				}
				$p->setVotes($votes);
			}
		}
		if(!$p)
			$GLOBALS["query_error"] = "NOT FOUND";
		return $p;
	}
}
?>