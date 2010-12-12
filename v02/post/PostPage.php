<?php
require_once("settings.php");
require_once("strings/" . LANG . "strings.php");
require_once("file_manager.php");
require_once("post/Post.php");
require_once("post/PostManager.php");
require_once("post/resourceManager.php");
require_once("post/collection/CollectionManager.php");


class PostPage {
	const NO_DATE = "no_date";
	const SHORT = "short";
	const SHORTEST = "shortest";
	const NO_COMMENTS = "no_comments";
	const NO_TAGS = "no_tags";
	const SMALL_TITLE = "small_title";
	const NO_MODIF_DATE = "no_modif_date";
	const NO_CATEGORIES = "no_categories";
	
	static function showShortPost($post, $options = null) {
?>
	<div class="shortpost <?php echo $post->getType(); ?>" id="post<?php echo $post->getID(); ?>">
		<div class="pb_header">
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
			<div class="post_title small_title"><a href="<?php echo FileManager::appendToRootPath($post->getPermalink()); ?>"><?php echo Filter::decodeFilteredText($post->getTitle()); ?></a></div>
		</div>
		<div class="post_content clear">
			<span id="post_place_<?php echo $post->getID(); ?>" class="post_place"></span><?php
			if(is_array($post->getContent())) {
				$first = true;
				foreach($post->getContent() as $cont) {
					if($first) $first = false;
					else echo ", ";
					echo Filter::decodeFilteredText($cont);
				}
			} else
				echo substr(Filter::decodeFilteredText($post->getContent()), 0, 200) . (strlen(Filter::decodeFilteredText($post->getContent())) < 200 ? "" : "...");
			if(!is_null($post->getPlace())) {
				require_once("maps/geolocate.php");
				MapManager::printInfoInElement($post->getPlace(), "post_place_" . $post->getID());
			}
			?><div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath("User/" . $post->getAuthorName()); ?>"><?php echo $post->getAuthorName(); ?></a></div>	
		</div>
	</div>
<?php
	}
	
	static function showPost($post, $options = null) {
		if(isset($options[self::SHORTEST]) && $options[self::SHORTEST]) {
			self::showShortPost($post);
			return;
		}
?>
	<div class="post <?php echo $post->getType(); ?>" id="post<?php echo $post->getID(); ?>">
		<div class="pb_header"><?php 
			if(!isset($options[self::NO_DATE]) || !$options[self::NO_DATE]) {
			?>
			<div class="post_creationDate"><?php echo format_datetime($post->getCreationDate()); ?></div><?php
			}
			if(!isset($options[self::NO_CATEGORIES]) || !$options[self::NO_CATEGORIES]) {
			?>
			<div class="post_categories"><?php
				$first = true;
				$cats = explode(",", $post->getCategories());
				foreach($cats as $cat) {
					if($first) $first = false;
					else echo ", ";
					echo '<a href="' . FileManager::appendToRootPath('Category/' . trim(Filter::decodeFilteredText($cat))) . '">' . trim(Filter::decodeFilteredText($cat)) . '</a>';
				}
			?></div><?php
			} ?>
			<div class="clear"></div>
		</div>
		<div class="post_header">
			<div class="post_headline clear"><?php echo Filter::decodeFilteredText($post->getHeadline()); ?></div><?php
			$title_class = "post_title";
			if(isset($options[self::SMALL_TITLE]) && $options[self::SMALL_TITLE])
				$title_class.= " small_title"; ?>
			<div class="<?php echo $title_class; ?>"><a href="<?php echo FileManager::appendToRootPath($post->getPermalink()); ?>"><?php echo Filter::decodeFilteredText($post->getTitle()); ?></a></div>
			<div class="post_subtitle"><?php echo Filter::decodeFilteredText($post->getSubtitle()); ?></div>
		</div>
		<div class="post_content clear">
			<span id="post_place_<?php echo $post->getID(); ?>" class="post_place"></span><?php
			if(is_array($post->getContent())) {
				$first = true;
				foreach($post->getContent() as $cont) {
					if($first) $first = false;
					else echo " ";
					//echo Filter::decodeFilteredText($cont);
					//$photo=Filter::decodeFilteredText($cont);
					$path ="/IoEsisto/v02/" . $cont->getPath();
					echo "<a href='$path'><img src='" . $path . "' width='100' height='50'></a>";
				}
			} else
				echo Filter::decodeFilteredText($post->getContent());
			if(!is_null($post->getPlace())) {
				require_once("maps/geolocate.php");
				MapManager::printInfoInElement($post->getPlace(), "post_place_" . $post->getID());
			}
			?>
		<div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath("User/" . $post->getAuthorName()); ?>"><?php echo $post->getAuthorName(); ?></a></div>	
		</div>
		<div class="post_footer clear">
			<div class="post_vote">
				<div class="vote_image"><a href="<?php echo $post->getFullPermalink() . "/Vote?vote=yes"; ?>">s&igrave;</a></div>
				<div class="vote_image"><a href="<?php echo $post->getFullPermalink() . "/Vote?vote=no"; ?>">no</a></div>
				Voto: <?php echo $post->getAvgVote(); ?>
			</div>
			<?php 
			if(!isset($options[self::NO_TAGS]) || !$options[self::NO_TAGS]) {
				if(!is_null($post->getTags()) && trim($post->getTags()) != "") {?>
			<div class="post_tags">Tag: <?php
					$first = true;
					$tags = explode(",", $post->getTags());
					foreach($tags as $tag) {
						if($first) $first = false;
						else echo ", ";
						echo '<a href="' . FileManager::appendToRootPath('Tag/' . trim(Filter::decodeFilteredText($tag))) . '">' . trim(Filter::decodeFilteredText($tag)) . '</a>';
					}
			?></div><?php
				}
			}
			if(!isset($options[self::NO_MODIF_DATE]) || !$options[self::NO_MODIF_DATE]) {
			?>
			<div class="post_modificationDate">Ultima modifica: <?php echo format_datetime($post->getModificationDate()); ?></div><?php
			} ?>
		</div><?php 
		if(!isset($options[self::NO_COMMENTS]) || !$options[self::NO_COMMENTS]) {
			self::showComments($post);
		} ?>
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
		$user = Session::getUser();
		//TODO controlla sessione
		if(is_null($error) && count($_POST) > 0) {
			$data = array();
			if(isset($_POST["title"]) && trim($_POST["title"]) != "")
				$data["title"] = $_POST["title"];
			else
				$error = array("Inserire un titolo.");
			if(isset($_POST["type"])){
				$data["type"] = $_POST["type"];
				/*DEBUG*/echo $data["type"];
			}else
				$error[] = "Scegliere il tipo di post da pubblicare.";
				
			
			if($data["type"] == "news"){
				if(isset($_POST["content"]) && trim($_POST["content"]) != ""){
					$data["content"] = $_POST["content"];
				}else
					$error[] = "Inserire un contenuto.";
			} else if($data["type"] == "photoreportage"){
				/*DEBUG*/echo "</br>caricamento immagini</br>";
				$photo = array();
				for($i=0,$numphoto=0,$notvalid=0;$i<10;$i++){
					if(trim($_FILES["upfile$i"]["name"]) != ""){
						if($_FILES["upfile$i"]["type"] == "image/gif" || $_FILES["upfile$i"]["type"] == "image/jpeg" || $_FILES["upfile$i"]["type"] == "image/png"){
							$photo[]= resourceManager::uploadPhoto(trim($_FILES["upfile$i"]["name"]),$user->getNickname(),$_FILES["upfile$i"]["tmp_name"],$_FILES["upfile$i"]["type"]);
							$numphoto++;
							/*DEBUG*/echo "caricata immagine upfile". $i ."</br> numphoto: " . $numphoto;
						}else
							$notvalid++;
					}
				}
				//se ha caricato file in formato non valido do errore
				if($notvalid!=0)
					$error[]="Devi inserire un formato valido: .jpeg .jpg .gif oppure .png";
				//se non sono state caricate foto do errore
				if($numphoto>0)
					$data["content"] = $photo;
				else
					$error[]="Devi inserire almeno un'immagine";	
				/*DEBUG*/echo "</br>FINE caricamento immagini</br> immagini caricate: ". count($photo);
				
			} else if($data["type"] == "videoreportage"){
				/*DEBUG*/echo "</br>caricamento video</br>";
			}
			
	
			
			if(isset($_POST["cat"]) && is_array($_POST["cat"]) && count($_POST["cat"]) > 0) {
				$cat = ""; $first = true;
				foreach($_POST["cat"] as $k => $c) {
					if($first) $first = false;
					else $cat.= ", ";
					$cat.= $c;
				}
				$data["categories"] = $cat;
			}
			
			if(isset($_POST["place"]) && trim($_POST["place"]) != "")
				$data["place"] = $_POST["place"];
			if(isset($_POST["headline"]) && trim($_POST["headline"]) != "")
				$data["headline"] = $_POST["headline"];
			if(isset($_POST["subtitle"]) && trim($_POST["subtitle"]) != "")
				$data["subtitle"] = $_POST["subtitle"];
			if(isset($_POST["tags"]) && trim($_POST["tags"]) != "")
				$data["tags"] = $_POST["tags"];
				
			/*DEBUG*/echo "</br>controllo errori";
			if(is_null($error) || (is_array($error) && count($error) == 0)) {
				/*DEBUG*/echo "</br>NO errori";
				$data["author"] = $user->getID();
				//se photoreportage creo una collection
				if($data["type"]=="news" || $data["type"]=="videoreportage" ){
					/*DEBUG*/echo "</br>PostManager::createPost ". $data["type"] ."</br></br>";
					$post = PostManager::createPost($data);
				}else if ($data["type"]=="photoreportage"){
					/*DEBUG*/echo "</br>CollectionManager::createCollection ". $data["type"] ."</br>";
					$post = CollectionManager::createCollection($data);
				}
				/*DEBUG*/ var_dump($post);
				if($post !== false) {
					echo '
			<div class="message">
				Notizia salvata: <a href="' . $post->getFullPermalink() . '">Visualizza</a>
			</div>';
				}
			} else {
				/*DEBUG*/echo "</br>SI errori</br>";
				self::showNewPostForm($data, $error);
				return;
			}
		}
		//echo serialize(isset($_GET["type"])) . "<br/>"; //DEBUG
		if(isset($_GET["type"])) {
			switch($_GET["type"]) {
				case "Collection":
				case "photoreportage":
				case "videoreportage":
				case "Album":
				case "Magazine":
				case "Playlist":
					call_user_func(array("PostPage","showNew" . $_GET["type"] . "Form"), $data, $error);
					break;
				case "News":
				default:
					self::showNewNewsForm($data, $error);
			}
		} else
			self::showNewNewsForm($data, $error);
	}
	
	static function showCommentForm($user, $post, $error = null) {
		if ($user == Session::getUser($user)){ //controllo se l'untente è loggato
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
		?>
        <form name="" action="" method="get"> <!-- TODO -->
            <input type="submit" value="">
        </form>
        <?php
	}

	static function showEditPostForm($post, $data = null, $error = null) {
		$user = Session::getUser();
		if($user->getID() != $post->getAuthor()) return;
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
			if(isset($_POST["cat"]) && is_array($_POST["cat"]) && count($_POST["cat"]) > 0) {
				$cat = ""; $first = true;
				foreach($_POST["cat"] as $k => $c) {
					if($first) $first = false;
					else $cat.= ", ";
					$cat.= $c;
				}
				$data["categories"] = $cat;
			}
			if(isset($_POST["place"]) && trim($_POST["place"]) != "")
				$data["place"] = $_POST["place"];
			if(isset($_POST["headline"]) && trim($_POST["headline"]) != "")
				$data["headline"] = $_POST["headline"];
			if(isset($_POST["subtitle"]) && trim($_POST["subtitle"]) != "")
				$data["subtitle"] = $_POST["subtitle"];
			if(isset($_POST["tags"]) && trim($_POST["tags"]) != "")
				$data["tags"] = $_POST["tags"];
				
			if(is_null($error) || (is_array($error) && count($error) == 0)) {
				$data["author"] = $user->getID();
				$post = PostManager::createPost($data);
				/*DEBUG*/ echo $post;
				if($post !== false) {
					echo '
			<div class="message">
				Notizia salvata: <a href="' . $post->getFullPermalink() . '">Visualizza</a>
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
				case "photoreportage":
				case "videoreportage":
				case "Album":
				case "Magazine":
				case "Playlist":
					call_user_func(array("PostPage","showEdit" . $_GET["type"] . "Form"), $data, $error);
					break;
				case "News":
				default:
					self::showEditNewsForm($data, $error);
			}
		} else self::showEditNewsForm($data, $error);
	}

	private static function showEditNewsForm($post, $error, $new = false) {
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
		<form name="<?php echo $name; ?>Post" action="?type=News" method="post">
			<p class="post_headline"><label>Occhiello:</label><br />
				<input class="post_headline" name="headline" value="<?php echo $post->getHeadline(); ?>"/></p>
			<p class="title"><label>Titolo:</label><br/>
				<input class="post_title" name="title" value="<?php echo $post->getTitle(); ?>"/></p>
			<p class="post_subtitle"><label>Sottotilolo:</label><br />
				<input class="post_subtitle" name="subtitle" value="<?php echo $post->getSubtitle(); ?>"/></p>
			<p class="content"><label>Contenuto:</label><br/>
			<textarea name="content" id="post_content"><?php echo $post->getContent(); ?></textarea>
				<!-- sostituisco textarea standard con ckeditor -->
				<script type="text/javascript">
					CKEDITOR.replace( 'post_content', { toolbar : 'edited'});
				</script>
			</p>
			<p class="tags"><label>Tags:</label> 
				<input class="tags" id="post_tags_input" name="tags" value="<?php echo $post->getTags(); ?>"/></p>
			<p class="categories"><label>Categorie:</label><br/><?php
				$cat = array();
				if(trim($post->getCategories()) != "")
					$cat = explode(", ", Filter::decodeFilteredText($post->getCategories()));
				self::showCategoryTree($cat); ?>
			</p>
            <p class="<?php echo trim($post->getPlace()) == "" ? "hidden" : ""; ?>"><label id="place_label">Posizione: <?php echo $post->getPlace(); ?></label></p>
            	<input id="post_place" name="place" type="hidden" value="<?php echo $post->getPlace(); ?>" />
            <input name="visible" type="hidden" value="true" />
            <input name="type" type="hidden" value="news" />
           	<p class="submit"><input type="submit" value="Pubblica" /> 
            	<input type="button" onclick="javascript:save();" value="Salva come bozza"/></p>
             <script type="text/javascript">
            	function save() {
					document.<?php echo $name; ?>Post.visible.value = false;
					document.<?php echo $name; ?>Post.submit();
            	}
            </script>
		<?php 
		require_once 'maps/geolocate.php';
		MapManager::setCenterToMap($post->getPlace(), "map_canvas");
		?>
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
		<div class="title"><?php echo $caption; ?> Photoreportage</div>
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
		<form name="<?php echo $name; ?>Post" action="?type=photoreportage" method="post" enctype="multipart/form-data">
			<p class="post_headline"><label>Occhiello:</label><br />
				<input class="post_headline" name="headline" value="<?php echo $post->getHeadline(); ?>"/></p>
			<p class="title"><label>Titolo:</label><br/>
				<input class="post_title" name="title" value="<?php echo $post->getTitle(); ?>"/></p>
			<p class="post_subtitle"><label>Sottotilolo:</label><br />
				<input class="post_subtitle" name="subtitle" value="<?php echo $post->getSubtitle(); ?>"/></p>
			<p class="content"><label>Contenuto:</label><br/>
				<fieldset><legend>upload immagini</legend>
				<?php for($i=0;$i<10;$i++){
					echo "<input type=\"file\"name=\"upfile$i\"></br>";
				}?>
				</fieldset></p>
			</p>
			<p class="tags"><label>Tags:</label> 
				<input class="tags" id="post_tags_input" name="tags" value="<?php echo $post->getTags(); ?>"/></p>
			<p class="categories"><label>Categorie:</label><br/><?php
				$cat = array();
				if(trim($post->getCategories()) != "")
					$cat = explode(", ", Filter::decodeFilteredText($post->getCategories()));
				self::showCategoryTree($cat); ?>
			</p>
			<p class="<?php echo trim($post->getPlace()) == "" ? "hidden" : ""; ?>"><label id="place_label">Posizione: <?php echo $post->getPlace(); ?></label></p>
			<input id="post_place" name="place" type="hidden" value="<?php echo $post->getPlace(); ?>" />
			<input name="visible" type="hidden" value="true" />
			<input name="type" type="hidden" value="photoreportage" />
			<p class="submit"><input type="submit" value="Pubblica" /> 
				<input type="button" onclick="javascript:save();" value="Salva come bozza"/></p>
			<script type="text/javascript">
				function save() {
					document.<?php echo $name; ?>Post.visible.value = false;
					document.<?php echo $name; ?>Post.submit();
				}
			</script>
			<?php 
			require_once 'maps/geolocate.php';
			MapManager::setCenterToMap($post->getPlace(), "map_canvas");
			?>
		</form>
        <?php
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
		<form name="<?php echo $name; ?>Post" action="?type=videoreportage" method="post">
			<p class="post_headline"><label>Occhiello:</label><br />
				<input class="post_headline" name="headline" value="<?php echo $post->getHeadline(); ?>"/></p>
			<p class="title"><label>Titolo:</label><br/>
				<input class="post_title" name="title" value="<?php echo $post->getTitle(); ?>"/></p>
			<p class="post_subtitle"><label>Sottotilolo:</label><br />
				<input class="post_subtitle" name="subtitle" value="<?php echo $post->getSubtitle(); ?>"/></p>
			<p class="content"><label>Contenuto:</label><br/>
				<!-- TODO --> <fieldset><legend>upload video</legend>	</fieldset></p>
			</p>
			<p class="tags"><label>Tags:</label> 
				<input class="tags" id="post_tags_input" name="tags" value="<?php echo $post->getTags(); ?>"/></p>
			<p class="categories"><label>Categorie:</label><br/><?php
				$cat = array();
				if(trim($post->getCategories()) != "")
					$cat = explode(", ", Filter::decodeFilteredText($post->getCategories()));
				self::showCategoryTree($cat); ?>
			</p>
			<p class="<?php echo trim($post->getPlace()) == "" ? "hidden" : ""; ?>"><label id="place_label">Posizione: <?php echo $post->getPlace(); ?></label></p>
			<input id="post_place" name="place" type="hidden" value="<?php echo $post->getPlace(); ?>" />
			<input name="visible" type="hidden" value="true" />
			<input name="type" type="hidden" value="videoreportage" />
			<p class="submit"><input type="submit" value="Pubblica" /> 
				<input type="button" onclick="javascript:save();" value="Salva come bozza"/></p>
			<script type="text/javascript">
				function save() {
					document.<?php echo $name; ?>Post.visible.value = false;
					document.<?php echo $name; ?>Post.submit();
				}
			</script>
			<?php 
			require_once 'maps/geolocate.php';
			MapManager::setCenterToMap($post->getPlace(), "map_canvas");
			?>
		</form>
	<?php
	}
	
	static function showContestDetails($contest) {
		//TODO
	}
	
	static function showComments($post, $limit = 0, $js = false) {
		?><div class="post_comments clear">Commenti:<?php
		$num = 0;
		foreach($post->getComments() as $comm) {
			if($num++ >= $limit) break;
			echo "<p class='comment'>" . $comm . "</p>";
		}
		?></div>
		<?php
	}
	
	private static function showCategoryTree($checked = array()) {
		?>
				<div class="category_tree">
				<?php
				require_once 'post/PostCommon.php';
				$cat = CategoryManager::getCategories();
				$i = 0; ?>
					<ul class="category_tree_level_0"><?php
				foreach($cat as $valore)
					$i = self::writeCategoryNode($valore, $i, 1, $checked);
					?>
					</ul><?php
				?>
				</div>
		<?php
	}
	
	private static function writeCategoryNode($node, $counter, $level, $checked = array()) {
		$check = false;
		if(count($checked) > 0) {
			$check = array_search($node->name, $checked) !== false;
			if($check) array_diff($checked, array($node->name));
		}
		echo '<li><input type="checkbox" name="cat[' . ++$counter . ']" value="' . $node->name .'" ' . ($check ? "checked " : "") . '/> <label>' . $node->name . '</label></li>';
		if(is_array($children = $node->getChildren())) { ?>
					<ul class="category_tree_level_<?php echo $level++; ?>"><?php
			foreach($children as $child) {
				$counter = self::writeCategoryNode($child, $counter, $level, $checked);
			}
					?>
					</ul><?php
		}
		return $counter;
	}
}

?>
