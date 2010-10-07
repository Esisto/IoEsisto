<?php
require_once("post/PostManager.php");
require_once("settings.php");
require_once("strings/" . LANG . "strings.php");
require_once("file_manager.php");

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
			?><div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath($post->getAuthorName()); ?>"><?php echo $post->getAuthorName(); ?></a></div>	
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
			?><div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath($post->getAuthorName()); ?>"><?php echo $post->getAuthorName(); ?></a></div>	
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
	
	static function showNewNewsForm() {
		//TODO
	}
	
	static function showNewPhotoReportageForm() {
		//TODO
	}

	static function showNewCollectionForm() {
		//TODO
	}
	
	static function showNewAlbumForm() {
		//TODO
	}

	static function showNewMagazineForm() {
		//TODO
	}
	
	static function showNewVideoReportageForm() {
		//TODO
	}
	
	static function showNewPostForm() {
		// TODO controlla sessione
		if(isset($_POST["title"])) {
			$data = array();
			if(isset($a))
				$data[fgd] = $a;
				
			
			$post = PostManager::addPost($data);
			?>
			<a href="<? echo $post->getFullPermalink(); ?>">Visualizza</a>
			<?
		} else {
			
		?>
        <form name="" action="" method="post"> <!-- TODO -->
			<p>Titolo:<br /><input name="titolo" /></p>
			<?
			if(isset($_GET["type"])) {
				if($_GET["type"] == "news") {
					?>
					<input type="hidden" value="news" name="type" />
					<?
				}
			} else {
				?>
			<p><select name="type">
				<option value="news">News</option>
				</select></p>
				
				<?
			}
			?>
            <input type="submit" value="" />
        </form>
        <?php
		}
	}
	
	static function showCommentForm() {
		//TODO
		?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
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

	static function showEditNewsForm() {
		//TODO
	}
	
	static function showEditPhotoReportageForm() {
		//TODO
	}

	static function showEditCollectionForm() {
		//TODO
	}
	
	static function showEditAlbumForm() {
		//TODO
	}

	static function showEditMagazineForm() {
		//TODO
	}
	
	static function showEditVideoReportageForm() {
		//TODO
	}
	
	static function showContestDetails($contest) {
		//TODO
	}
}

?>
