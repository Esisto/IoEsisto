<?php
require_once("post/PostManager.php");
require_once("settings.php");
require_once("strings/" . LANG . "strings.php");
require_once("file_manager.php");

class PostPage {
	static function showPost($post) {
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

	static function showForLikelihood() {
		// TODO
	}
	
	static function showCommentForm() {
	?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
	}

	static function submitCommentForm() {
		// TODO
	}

	static function showVoteForm() {
	?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
	}

	static function submitVoteForm() {
		// TODO
	}

	static function showEditForm() {
	?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
	}

	static function submitEditForm() {
		// TODO
	}

	static function showCollectionForm() {
	?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
	}

	static function submitCollectionForm() {
		// TODO
	}

	static function showContestForm() {
	?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
	}

	static function submitContestForm() {
		// TODO
	} 
}

?>
