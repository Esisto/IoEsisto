<?php
require_once("settings.php");
require_once("strings/" . LANG . "strings.php");
require_once("file_manager.php");
require_once("post/Post.php");
require_once("post/PostManager.php");


class PostPage {
	static function showShortPost($post, $options = null) {
?>
	<div class="shortpost <?php echo $post->getType(); ?>" id="post<?php echo $post->getID(); ?>">
		<div class="pb_header">
			<div class="post_creationDate"><?php echo format_datetime($post->getCreationDate()); ?></div>
			<div class="post_categories"><?php
				$first = true;
				$cats = explode(",", $post->getCategories());
				foreach($cats as $cat) {
					if($first) $first = false;
					else echo ", ";
					echo '<a href="' . FileManager::appendToRootPath('Category/' . trim(Filter::decodeFilteredText($cat))) . '">' . trim(Filter::decodeFilteredText($cat)) . '</a>';
				}
			?></div>
			<div class="clear"></div>
		</div>
		<div class="post_header">
			<!--<div class="post_headline clear"><?php echo Filter::decodeFilteredText($post->getHeadline()); ?></div>-->
			<div class="post_title"><a href="<?php echo FileManager::appendToRootPath($post->getPermalink()); ?>"><?php echo Filter::decodeFilteredText($post->getTitle()); ?></a></div>
			<div class="post_subtitle"><?php echo Filter::decodeFilteredText($post->getSubtitle()); ?></div>
		</div>
		<div class="post_content clear">
			<?php
				if(!is_null($post->getPlace())) {
					require_once("maps/geolocate.php");
					MapManager::printInfoInElement($post->getPlace(), "post_place_" . $post->getID());
				}
			?>
			<span id="post_place_<?php echo $post->getID(); ?>" class="post_place"></span><?php
			if(is_array($post->getContent())) {
				$first = true;
				foreach($post->getContent() as $cont) {
					if($first) $first = false;
					else echo ", ";
					echo Filter::decodeFilteredText($cont);
				}
			} else
				echo Filter::decodeFilteredText($post->getContent());
			?><div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath("User/" . $post->getAuthorName()); ?>"><?php echo $post->getAuthorName(); ?></a></div>	
		</div>
		<div class="post_footer clear">
			<div class="post_vote">Voto: <?php echo $post->getAvgVote(); ?></div>
			<div class="post_tags">Tag: <?php
				$first = true;
				$tags = explode(",", $post->getTags());
				foreach($tags as $tag) {
					if($first) $first = false;
					else echo ", ";
					echo '<a href="' . FileManager::appendToRootPath('Tag/' . trim(Filter::decodeFilteredText($tag))) . '">' . trim(Filter::decodeFilteredText($tag)) . '</a>';
				}
			?></div>
			<!--<div class="post_modificationDate">Ultima modifica: <?php echo format_datetime($post->getModificationDate()); ?></div>-->
			<!--<div class="post_visible"><?php echo ($post->isVisible() ? "visible" : "not visible"); ?></div>-->
		</div>
		<!--<div class="post_comments clear">Commenti:<?php
			foreach($post->getComments() as $comm)
				echo "<p class='comment'>" . $comm . "</p>";
		?></div>-->
	</div>
<?php
	}
	
	static function showPost($post, $options = null) {
?>
	<div class="post <?php echo $post->getType(); ?>" id="post<?php echo $post->getID(); ?>">
		<div class="pb_header"><?php 
			if(!isset($options["no_date"]) || !$options["no_date"]) {
			?>
			<div class="post_creationDate"><?php echo format_datetime($post->getCreationDate()); ?></div><?php
			}
			?>
			<div class="post_categories"><?php
				$first = true;
				$cats = explode(",", $post->getCategories());
				foreach($cats as $cat) {
					if($first) $first = false;
					else echo ", ";
					echo '<a href="' . FileManager::appendToRootPath('Category/' . trim(Filter::decodeFilteredText($cat))) . '">' . trim(Filter::decodeFilteredText($cat)) . '</a>';
				}
			?></div>
			<div class="clear"></div>
		</div>
		<div class="post_header">
			<div class="post_headline clear"><?php echo Filter::decodeFilteredText($post->getHeadline()); ?></div>
			<div class="post_title"><a href="<?php echo FileManager::appendToRootPath($post->getPermalink()); ?>"><?php echo Filter::decodeFilteredText($post->getTitle()); ?></a></div>
			<div class="post_subtitle"><?php echo Filter::decodeFilteredText($post->getSubtitle()); ?></div>
		</div>
		<div class="post_content clear">
			<?php
				if(!is_null($post->getPlace())) {
					require_once("maps/geolocate.php");
					MapManager::printInfoInElement($post->getPlace(), "post_place_" . $post->getID());
				}
			?>
			<span id="post_place_<?php echo $post->getID(); ?>" class="post_place"></span><?php
			if(is_array($post->getContent())) {
				$first = true;
				foreach($post->getContent() as $cont) {
					if($first) $first = false;
					else echo ", ";
					echo Filter::decodeFilteredText($cont);
				}
			} else
				echo Filter::decodeFilteredText($post->getContent());
			?><?php echo $post->getAuthorName(); ?>
		<div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath("User/" . $post->getAuthorName()); ?>"><?php echo $post->getAuthorName(); ?></a></div>	
		</div>
		<div class="post_footer clear">
			<div class="post_vote">
				<div class="vote_image"><a href="<?php echo $post->getFullPermalink() . "/Vote?vote=yes"; ?>">sì</a></div>
				<div class="vote_image"><a href="<?php echo $post->getFullPermalink() . "/Vote?vote=no"; ?>">no</a></div>
				Voto: <?php echo $post->getAvgVote(); ?>
			</div>
			<?php if(!is_null($post->getTags()) && trim($post->getTags()) != "") {?>
			<div class="post_tags">Tag: <?php
				$first = true;
				$tags = explode(",", $post->getTags());
				foreach($tags as $tag) {
					if($first) $first = false;
					else echo ", ";
					echo '<a href="' . FileManager::appendToRootPath('Tag/' . trim(Filter::decodeFilteredText($tag))) . '">' . trim(Filter::decodeFilteredText($tag)) . '</a>';
				}
			?></div>
			<?php }?>
			<div class="post_modificationDate">Ultima modifica: <?php echo format_datetime($post->getModificationDate()); ?></div>
			<!--<div class="post_visible"><?php echo ($post->isVisible() ? "visible" : "not visible"); ?></div>-->
		</div>
		<div class="post_comments clear">Commenti:<?php
			foreach($post->getComments() as $comm)
				echo "<p class='comment'>" . $comm . "</p>";
		?></div>
	</div>
<?php
	}

	/**
	 * @deprecated
	 */
	static function showForLikelihood() {}
	
	private static function showNewNewsForm($data = null, $error = null) {
		self::showEditNewsForm($data, $error, true);
	}
	
	private static function showNewPhotoReportageForm($data = null, $error = null) {
		self::showEditPhotoReportageForm($data, $error, true);
	}

	private static function showNewCollectionForm($data = null, $error = null) {
		self::showEditCollectionForm($data, $error, true);
	}
	
	private static function showNewAlbumForm($data = null, $error = null) {
		self::showEditAlbumForm($data, $error, true);
	}

	private static function showNewMagazineForm($data = null, $error = null) {
		self::showEditMagazineForm($data, $error, true);
	}
	
	private static function showNewVideoReportageForm($data = null, $error = null) {
		self::showEditVideoReportageForm($data, $error, true);
	}
	
	static function showNewPostForm($data = null, $error = null) {
		//TODO controlla sessione
		if(is_null($error) && count($_POST) > 0) {
			$data = array();
			if(isset($_POST["title"]) && trim($_POST["title"]) != "")
				$data["title"] = $_POST["title"];
			else
				$error = array("Inserire un titolo.");
			if(isset($_POST["type"]))
				$data["type"] = $_POST["type"];
			else
				$error[] = "Scegliere il tipo di post da pubblicare.";
			if(isset($_POST["content"]) && trim($_POST["content"]) != "")
				$data["content"] = $_POST["content"];
			else
				$error[] = "Inserire un contenuto.";
			
			if(is_null($error) || (is_array($error) && count($error) == 0)) {
				$data["author"] = 1; //FIXME TOGLIMI
				//$data["author"] = Session::getUser();
				$post = PostManager::createPost($data);
				if($post !== false) {
					echo '
			<div class="message">
				<a href="' . $post->getFullPermalink() . '">Visualizza</a>
			</div>';
				}
			} else {
				self::showNewPostForm($data, $error);
				return;
			}
		}
		//echo serialize(isset($_GET["type"])) . "<br/>"; //DEBUG
		if(isset($_GET["type"])) {
			switch($_GET["type"]) {
				case "Collection":
				case "PhotoReportage":
				case "VideoReportage":
				case "Album":
				case "Magazine":
				case "Playlist":
					call_user_func(array("PostPage","showNew" . $_GET["type"] . "Form"), $data, $error);
					break;
				case "News":
				default:
					self::showNewNewsForm($data, $error);
			}
		} else self::showNewNewsForm($data, $error);
	}
	
	static function showCommentForm($user, $post, $error = null) {
		if ($user == Session::getUser($user) && is_a($user, "user") ){ //controllo se l'untente è loggato
			if($error==null && count($_POST) > 0){  
				if(isset($_POST["comment"]))
					$comment = $_POST["comment"];
				else
					$error = "devi inserire un commento!";
				
				if(isset($error)) {
					self::showCommentForm($error);
				}else{
					PostManager::commentPost($post, $user->getID(), $comment);
				}
			}else {			
				$POST_data = count($_POST) > 0;
				if( $error != null)
					foreach ($error as $valore) {
						echo "$valore<br>";
					}?>
				<form name="addComment" action="" method="post">
					<textarea name="comment">
					<?php if($POST_data) echo $_POST["comment"]; ?>
					</textarea>
					<input type="submit" value="comment">
				</form>
				<?php
			}
		} else { /*user not loggedIn show error*/
			?> <a href="">login</a> o <a href="">registrati</a> per commentare  <!-- TODO: link alla form di login e di registrazione --> <?php
		}
	}

	/**
	 * @deprecated
	 */
	static function showVoteForm() {
		//TODO
		?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
	}

	static function showEditPostForm() {
		?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
	}

	private static function showEditNewsForm($post, $error, $new = false) {
		//TODO
		$name = "Edit";
		$caption = "Modifica";
		if($new) {
			$post = new Post($post);
			$name = "New";
			$caption = "Nuova";
		}
		?>
		<div class="title"><?php echo $caption; ?> Notizia</div>
		<?php
		if(is_array($error)) {
		?>
		<div class="error"><?php
			foreach($error as $err) {?>
			<p><?php echo $err; ?></p>
			<?php 
			}
		?></div>
		<?php
		}
		?>
		<form name="<?php echo $name; ?>Post" action="?type=News" method="post"> <!-- TODO -->
			<p>Titolo:<br /><input name="title" value="<?php echo $post->getTitle(); ?>"/></p>
			<p>Contenuto:<br/>
				<textarea name="content"><?php echo $post->getContent(); ?></textarea>
			</p>
            <p><input type="submit" value="Salva" /></p>
            <input name="type" type="hidden" value="news" />
        </form>
        <?php
	}
	
	private static function showEditPhotoReportageForm($post, $error, $new = false) {
		$name = "Edit";
		$caption = "Modifica";
		if($new) {
			$post = new Post($post);
			$name = "New";
			$caption = "Nuovo";
		}
		?>
		<div class="title"><?php echo $caption; ?> Fotoreportage</div>
		<?php
		//TODO
	}

	private static function showEditCollectionForm($post, $error, $new = false) {
		$name = "Edit";
		$caption = "Modifica";
		if($new) {
			$post = new Post($post);
			$name = "New";
			$caption = "Nuova";
		}
		?>
		<div class="title"><?php echo $caption; ?> Collezione</div>
		<?php
		//TODO
	}
	
	private static function showEditAlbumForm($post, $error, $new = false) {
		$name = "Edit";
		$caption = "Modifica";
		if($new) {
			$post = new Post($post);
			$name = "New";
			$caption = "Nuovo";
		}
		?>
		<div class="title"><?php echo $caption; ?> Album</div>
		<?php
		//TODO
	}

	private static function showEditMagazineForm($post, $error, $new = false) {
		$name = "Edit";
		$caption = "Modifica";
		if($new) {
			$post = new Post($post);
			$name = "New";
			$caption = "Nuova";
		}
		?>
		<div class="title"><?php echo $caption; ?> Rivista</div>
		<?php
		//TODO
	}
	
	private static function showEditVideoReportageForm($post, $error, $new = false) {
		$name = "Edit";
		$caption = "Modifica";
		if($new) {
			$post = new Post($post);
			$name = "New";
			$caption = "Nuovo";
		}
		?>
		<div class="title"><?php echo $caption; ?> Videoreportage</div>
		<?php
		//TODO
	}
	
	static function showContestDetails($contest) {
		//TODO
	}
}

?>
