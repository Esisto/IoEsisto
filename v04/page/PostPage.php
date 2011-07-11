<?php
require_once("settings.php");
require_once("strings/" . LANG . "strings.php");
require_once("manager/FileManager.php");
require_once("dataobject/Post.php");
require_once("dao/PostDao.php");
require_once("manager/PostManager.php");
require_once("manager/CollectionManager.php");
require_once("manager/ResourceManager.php");
require_once("manager/youtubeManager.php");
require_once("session.php");
require_once("page.php");
require_once("logger.php");

class PostPage {
	const NO_DATE = "no_date";
	const SHORT = "short";
	const SHORTEST = "shortest";
	const FLASH = "flash";
	const VIDEO = "video";
	const MOST_RECENT_MENU = "most_recent_top";
	const MOST_RECENT_CONTENT = "most_recent_bottom";
	const SEQUENTIAL = "sequential";
	const NO_COMMENTS = "no_comments";
	const NO_TAGS = "no_tags";
	const SMALL_TITLE = "small_title";
	const FIRST = "first";
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
				foreach($post->getContent() as $rsID) {
					if($first) $first = false;
					else echo " ";
					//echo Filter::decodeFilteredText($cont);
					$cont = ResourceManager::loadResource($rsID);
					$path =	FileManager::appendToRootPath($cont->getPath());
					$description = $cont->getDescription();
					echo "<a href='$path'><img src='" . $path . "' width='100' height='50' alt='" . $description . "' title='" . $description . "'></a>";

				}
			} else {
				if($post->getType() == "videoreportage"){
					echo youtubeManager::getVideoPlayer($post->getContent());
				}else{ //type=news
					echo substr(Filter::decodeFilteredText($post->getContent()), 0, 200) . (strlen(Filter::decodeFilteredText($post->getContent())) < 200 ? "" : "...");
					$rs_id = PostManager::getPostResource($post->getID());
					if($rs_id){
						$photo= ResourceManager::loadResource($rs_id);
						echo "<img src='" . FileManager::appendToRootPath($photo->getPath()) . "' />";
					}
				}
			}
			if(!is_null($post->getPlace())) {
				require_once("manager/MapManager.php");
				MapManager::printInfoInElement($post->getPlace(), "post_place_" . $post->getID());
			}
			$postdao = new PostDao();
			?><div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath("User/" . $postdao->getAuthorName($post)); ?>"><?php echo $postdao->getAuthorName($post); ?></a></div>	
		</div>
	</div>
<?php
	}
	
	static function showFlashPost($post, $options = null) {
		?>		
		<a class="flashNewsLink" href="<?php echo FileManager::appendToRootPath($post->getPermalink()); ?>">
			<?php
			if(isset($options[self::FIRST]) && $options[self::FIRST])
				echo "<div class='flashNewsFirst'>";
			else
				echo "<div class='flashNews'>";
			
			$rs_id = PostManager::getPostResource($post->getID());
			if($rs_id){
				?><div class="flashNewsImg left">
					<img src="<?php $photo= ResourceManager::loadResource($rs_id); $path=FileManager::appendToRootPath($photo->getPath()); echo $path; ?>">
				</div> 
				<div class="flashNewsContent left">
				<div class="flashNewsTitle left"><h2><?php echo Filter::decodeFilteredText($post->getTitle()); ?></h2></div>
				<div class="flashNewsArticles left"><p><?php echo substr(Filter::decodeFilteredText($post->getContent()), 0, 50) . (strlen(Filter::decodeFilteredText($post->getContent())) < 50 ? "" : " ..."); ?></p></div>
				<?php
			}else{
				?><div class="flashNewsContentTextOnly left">
				<div class="flashNewsTitle left"><h2><?php echo Filter::decodeFilteredText($post->getTitle()); ?></h2></div>
				<div class="flashNewsArticles left"><p><?php echo substr(Filter::decodeFilteredText($post->getContent()), 0, 270) . (strlen(Filter::decodeFilteredText($post->getContent())) < 270 ? "" : " ..."); ?></p></div>
				<?php
			} ?>
				<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</a>
		<?php
	}
	
	static function showVideoPost($post, $options = null) {
		?>	
		<div id="videoNewsTitle" class="left"><h2><?php echo Filter::decodeFilteredText($post->getTitle()); ?></h2></div>
		    
		    <div id="videoNewsYouTube" class="left"><?php
			require_once("manager/youtubeManager.php");
			echo youtubeManager::getVideoPlayer($post->getContent(),280);
		    ?></div>	
			
		    <div id="videoNewsAuthor" class="left"><p><?php $postdao = new PostDao(); echo Filter::decodeFilteredText($postdao->getAuthorName($post)); ?></p></div>
		<?php
	}
	
	static function showMostRecentMenu($post, $options = null) {
		if(isset($options[self::FIRST]) && $options[self::FIRST]) { ?>
			<li id="liArticle<?php echo $options[self::SEQUENTIAL]; ?>" class="firstMenuMostRecentList left"><div>
				<div id="leftArticle<?php echo $options[self::SEQUENTIAL]; ?>" class="firstMenuMostRecentTabLeft firstMenuMostRecentTabLeft left"></div>
				<div id="centerArticle<?php echo $options[self::SEQUENTIAL]; ?>" class="menuMostRecentTabCenter menuMostRecentTabCenter left"><a href="#article<?php echo $options[self::SEQUENTIAL]; ?>"><p><?php $cat = explode(",",$post->getCategories()); echo Filter::decodeFilteredText($cat[0]); ?></p></a></div>
				<div id="rightArticle<?php echo $options[self::SEQUENTIAL]; ?>" class="menuMostRecentTabRight menuMostRecentTabRight left"></div>
				<div class="clear"></div>
			</div></li>
		<?php } else { ?>
			<li id="liArticle<?php echo $options[self::SEQUENTIAL]; ?>" class="menuMostRecentList left"><div>
				<div id="leftArticle<?php echo $options[self::SEQUENTIAL]; ?>" class="menuMostRecentTabLeft left"></div>
				<div id="centerArticle<?php echo $options[self::SEQUENTIAL]; ?>" class="menuMostRecentTabCenter left"><a href="#article<?php echo $options[self::SEQUENTIAL]; ?>"><?php $cat = explode(",",$post->getCategories()); echo Filter::decodeFilteredText($cat[0]); ?></a></div>
				<div id="rightArticle<?php echo $options[self::SEQUENTIAL]; ?>" class="menuMostRecentTabRight left"></div><div class="clear"></div>
			</div></li>
		<?php }
	}
	
	static function showMostRecentContent($post, $options = null) {
		?>
		<div class="articlesBlock left" id="article<?php echo $options[self::SEQUENTIAL]; ?>">
			<h2 id="mostRecentTitle"><a href="<?php echo FileManager::appendToRootPath($post->getPermalink()); ?>"><?php echo Filter::decodeFilteredText($post->getTitle()); ?></a></h2>
			<?php $rs_id = PostManager::getPostResource($post->getID());
			if($rs_id) { ?>
				<div> 
					<div id="mostRecentArticlesTextAndImgLeft" class="left">
						<div id="mostRecentSubTitle"><?php echo Filter::decodeFilteredText($post->getSubtitle()); ?></div>
						<div id="mostRecentArticlesText"><?php echo substr(Filter::decodeFilteredText($post->getContent()), 0, 835) . (strlen(Filter::decodeFilteredText($post->getContent())) < 835 ? "" : " ..."); ?></div>
					</div>
					<div id="mostRecentArticlesTextAndImgAndRight" class="left">
						<img id="mostRecentArticlesImg" src="<?php $photo= ResourceManager::loadResource($rs_id); $path=FileManager::appendToRootPath($photo->getPath()); echo $path; ?>"/>
						<img id="mostRecentArticlesImgAngle" src="<?php echo FileManager::appendToRootPath("files/default/img/mostRecentImgAngle.png"); ?>"/> 
					</div>
					<div class="clear"></div>
				</div>
			<?php } else {  ?>
				<div class="mostRecentExtend">
					<div id="mostRecentSubTitle"><?php echo Filter::decodeFilteredText($post->getSubtitle()); ?></div>
					<div id="mostRecentArticlesText"><?php echo substr(Filter::decodeFilteredText($post->getContent()), 0, 100) . (strlen(Filter::decodeFilteredText($post->getContent())) < 100 ? "" : " ..."); ?></div>
				</div>
			<?php }	 ?>
		</div>
		<?php
	}
	
	static function showPost($post, $options = null) {
		if(isset($options[self::SHORTEST]) && $options[self::SHORTEST]) {
			self::showShortPost($post);
			return;
		}else if(isset($options[self::FLASH]) && $options[self::FLASH]) {
			self::showFlashPost($post, $options);
			return;
		}else if(isset($options[self::VIDEO]) && $options[self::VIDEO]) {
			self::showVideoPost($post);
			return;
		}else if(isset($options[self::MOST_RECENT_MENU]) && $options[self::MOST_RECENT_MENU]) {
			self::showMostRecentMenu($post, $options);
			return;
		}else if(isset($options[self::MOST_RECENT_CONTENT]) && $options[self::MOST_RECENT_CONTENT]) {
			self::showMostRecentContent($post, $options);
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
				foreach($post->getContent() as $rsID) {
					if($first) $first = false;
					else echo " ";
					//echo Filter::decodeFilteredText($cont);
					$cont = ResourceManager::loadResource($rsID);
					$path =	FileManager::appendToRootPath($cont->getPath());
					$description = $cont->getDescription();
					echo "<a href='$path'><img src='" . $path . "' width='100' height='50' alt='" . $description . "' title='" . $description . "'></a>";
				}
			} else{
				if($post->getType() == "videoreportage"){
					echo youtubeManager::getVideoPlayer($post->getContent());
				}else{ //type=news
					echo Filter::decodeFilteredText($post->getContent());
					$rs_id = PostManager::getPostResource($post->getID());
					if($rs_id){
						$photo= ResourceManager::loadResource($rs_id);
						echo "<img src='" . FileManager::appendToRootPath($photo->getPath()) . "' />";
					}
				}
			}
			if(!is_null($post->getPlace())) {
				require_once("manager/MapManager.php");
				MapManager::printInfoInElement($post->getPlace(), "post_place_" . $post->getID());
			}
			$postdao = new PostDao();
			?>
			<div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath("User/" . $postdao->getAuthorName($post)); ?>"><?php echo $postdao->getAuthorName($post); ?></a></div>	
		</div>
		<div class="post_footer clear">
			<div class="post_vote">
				<div class="vote_image"><a href="<?php echo FileManager::appendToRootPath($post->getPermalink() . "/Vote?vote=yes"); ?>">s&igrave;</a></div>
				<div class="vote_image"><a href="<?php echo FileManager::appendToRootPath($post->getPermalink() . "/Vote?vote=no"); ?>">no</a></div>
				Voto: <?php echo $post->getVote(); ?>
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
		require_once 'manager/AuthorizationManager.php';
		if(!isset($_GET["type"]))
			$_GET["type"] = Post::NEWS;
		if(!AuthorizationManager::canUserDo(AuthorizationManager::CREATE, $_GET["type"]))
			return; //TODO redirect verso pagina di errore.
		
		$logger = Logger::getLogger();
		$logger->debug("ResourceDao", $_GET["type"]);
		
		if(isset($_GET["phase"]) && $_GET["phase"]==3){
			if ($_GET["type"]=="photoreportage" && isset($_POST["numResources"])) {
					$data = array();
					for($i=0;$i<$_POST["numResources"];$i++){
						$resourceID = $_POST["resourceID".$i];
						if(isset($_POST[$resourceID]) && $_POST[$resourceID] != ''){ //$_POST[$resourceID] is the description
							$data['description'] = $_POST[$resourceID];
							$rsUpdated = ResourceManager::editResource($resourceID,$data);
						}
					}
					Page::redirect("Edit");
			}
		}else if(is_null($error) && count($_POST) > 0) {
			$data = array();
			if(isset($_POST["title"]) && trim($_POST["title"]) != "")
				$data["title"] = $_POST["title"];
			else
				$error = array("Inserire un titolo.");
			if(isset($_POST["type"]))
				$data["type"] = $_POST["type"];
			else
				$error[] = "Scegliere il tipo di post da pubblicare.";
				
			if($data["type"] == "news"){
				//carico il testo del post
				if(isset($_POST["content"]) && trim($_POST["content"]) != ""){
					$data["content"] = $_POST["content"];
				}else
					$error[] = "Inserire un contenuto.";
					
				//check if ther's not valid files
				$notvalid = 0;
				if(trim($_FILES["upfile"]["name"]) != ""){
					if($_FILES["upfile"]["type"] == "image/gif" || $_FILES["upfile"]["type"] == "image/jpeg" || $_FILES["upfile"]["type"] == "image/png") ;
					else $notvalid++;
				}
				if($notvalid == 0){
					if(trim($_FILES["upfile"]["name"]) != ""){
						if($_FILES["upfile"]["type"] == "image/gif" || $_FILES["upfile"]["type"] == "image/jpeg" || $_FILES["upfile"]["type"] == "image/png"){
							$fname = ResourceManager::editFileName($_FILES["upfile"]["name"]);
							$newsPhoto = ResourceManager::uploadPhoto($fname,$user->getNickname(),$user->getID(),$_FILES["upfile"]["tmp_name"],$_FILES["upfile"]["type"]);
							//prelevo l'id della risorsa appena salvata
							$data["photo"] = $newsPhoto->getID();
						}
					}
				}else
					$error[]="Devi inserire un formato valido: .jpeg .jpg .gif oppure .png";
					
			}else if($data["type"] == "photoreportage"){
				$photo = array();
				//check if ther's not valid files
				for($i=0,$notvalid=0;$i<10;$i++){
					if(trim($_FILES["upfile$i"]["name"]) != ""){
						if($_FILES["upfile$i"]["type"] == "image/gif" || $_FILES["upfile$i"]["type"] == "image/jpeg" || $_FILES["upfile$i"]["type"] == "image/png") ;
						else $notvalid++;
					}
				}
				if($notvalid == 0){
					for($i=0,$numphoto=0;$i<10;$i++){
						if(trim($_FILES["upfile$i"]["name"]) != ""){
							if($_FILES["upfile$i"]["type"] == "image/gif" || $_FILES["upfile$i"]["type"] == "image/jpeg" || $_FILES["upfile$i"]["type"] == "image/png"){
								$fname = ResourceManager::editFileName($_FILES["upfile$i"]["name"]);
								$photo[]= ResourceManager::uploadPhoto($fname,$user->getNickname(),$user->getID(),$_FILES["upfile$i"]["tmp_name"],$_FILES["upfile$i"]["type"]);
								$numphoto++;
							}
						}
					}
					if($numphoto>0)
						$data["content"] = $photo;
					else
						$error[]="Devi inserire almeno un'immagine";	
				}else
					$error[]="Devi inserire un formato valido: .jpeg .jpg .gif oppure .png";
					
			}else if($data["type"] == "videoreportage"){
				if(isset($_POST["userUrl"]) && $_POST["userUrl"] != ''){
					$rs = ResourceManager::createResource($user->getID(),youtubeManager::getVideoID($_POST["userUrl"]),'video');
					$data['content']=youtubeManager::getVideoID($_POST["userUrl"]);
				}
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
			//if(isset($_POST["headline"]) && trim($_POST["headline"]) != "")
			//	$data["headline"] = $_POST["headline"];
			if(isset($_POST["subtitle"]) && trim($_POST["subtitle"]) != "")
				$data["subtitle"] = $_POST["subtitle"];
			if(isset($_POST["tags"]) && trim($_POST["tags"]) != "")
				$data["tags"] = $_POST["tags"];
				
			if(is_null($error) || (is_array($error) && count($error) == 0)) {
				$data["author"] = $user->getID();
				//se photoreportage creo una collection
				if($data["type"]=="news"){
					$post = PostManager::createPost($data);
					//se vi è una foto aggiorno la tabella PostResource
					if(isset($data['photo']) && $data['photo'] != ""){
						PostManager::setPostResource($post->getID(),$data['photo']);
					}
				}else if($data["type"]=="videoreportage"){
					$post = PostManager::createPost($data);
				}else if ($data["type"]=="photoreportage" && $_GET["phase"]==2){
					//save only the resource ID not the whole object
					foreach($data["content"] as &$resource){
						$resource = $resource->getID();
					}
					$post = CollectionManager::createCollection($data);					
				}else{
					$post=false;
				}
				if($post !== false) {
					echo '
			<div class="message">
				Notizia salvata: <a href="' . FileManager::appendToRootPath($post->getPermalink()) . '">Visualizza</a>
			</div>';
				}
			} else {
				self::showNewPostForm($data, $error);
				return;
			}
		}
		//echo serialize(isset($_GET["type"])) . "<br/>"; //DEBUG
		switch($_GET["type"]) {
			case Post::COLLECTION:
			case Post::PHOTOREP:
			case Post::VIDEOREP:
			case Post::ALBUM:
			case Post::MAGAZINE:
			case Post::PLAYLIST:
				call_user_func(array("PostPage","showNew" . $_GET["type"] . "Form"), $data, $error);
				break;
			case "videoreportage3":
				break;
			case Post::NEWS:
			default:
				self::showNewNewsForm($data, $error);
		}
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

	static function showEditPostForm($post, $data = null, $error = null) {
		$user = Session::getUser();
		if($user->getID() != $post->getAuthor()) return;
		if(!Page::canUserDo($user)) return; //TODO redirect verso pagina di errore.
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
				if($post !== false) {
					echo '
			<div class="message">
				Notizia salvata: <a href="' . FileManager::appendToRootPath($post->getPermalink()) . '">Visualizza</a>
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
					call_user_func(array("PostPage","showEdit" . $_GET["type"] . "Form"), $data, $error);
					break;
				case "news":
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
		<form name="<?php echo $name; ?>Post" action="?type=news" method="post" enctype="multipart/form-data">
			<!--<p class="post_headline"><label>Occhiello:</label><br />
				<input class="post_headline" name="headline" value="<?php echo $post->getHeadline(); ?>"/></p>-->
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
				<fieldset><legend>upload immagine</legend><?php
					//se è presente la foto la visualizzo
					if($post->getID() != ""){
						$rs_id = PostManager::getPostResource($post->getID());
						if($rs_id){
							$articlePhoto = ResourceManager::loadResource($rs_id);
							echo "<img src='". FileManager::appendToRootPath($articlePhoto->getPath()) . "' /></br>";
						}
					}?>
					<input type='file' name='upfile' />
				</fieldset>
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
		require_once 'manager/MapManager.php';
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
		if(!isset($_GET["phase"]) || count($error) != 0){?>
		<form name="<?php echo $name; ?>Post" action="?type=photoreportage&phase=2" method="post" enctype="multipart/form-data">
			<!--<p class="post_headline"><label>Occhiello:</label><br />
				<input class="post_headline" name="headline" value="<?php echo $post->getHeadline(); ?>"/></p>-->
			<p class="title"><label>Titolo:</label><br/>
				<input class="post_title" name="title" value="<?php echo $post->getTitle(); ?>"/></p>
			<p class="post_subtitle"><label>Sottotilolo:</label><br />
				<input class="post_subtitle" name="subtitle" value="<?php echo $post->getSubtitle(); ?>"/></p>
			<p class="content">
				<label>Contenuto:</label><br/>
				<fieldset><legend>upload immagini</legend>
				<?php for($i=0;$i<10;$i++){
					echo "<input type=\"file\"name=\"upfile$i\"></br>";
				}?>
				</fieldset>
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
			<p class="submit"><input type="submit" value="Prosegui" /> 
				<input type="button" onclick="javascript:save();" value="Salva come bozza"/></p>
			<script type="text/javascript">
				function save() {
					document.<?php echo $name; ?>Post.visible.value = "no";
					document.<?php echo $name; ?>Post.submit();
				}
			</script>
			<?php 
			require_once 'manager/MapManager.php';
			MapManager::setCenterToMap($post->getPlace(), "map_canvas");
			?>
		</form>
		<?php }else if(count($error) == 0){ ?>
			<fieldset><legend>Inserisci le descrizioni alle tue foto! </legend>
				<form name="<?php echo $name; ?>Post" action="?type=photoreportage&phase=3" method="post" enctype="multipart/form-data">
					<?php for($i=0;$i<count($post->getContent());$i++){
						$rs_array=$post->getContent();
						$resource = ResourceManager::loadResource($rs_array[$i]);
						$path = FileManager::appendToRootPath($resource->getPath());
						$index = $resource->getID(); ?>
						<img src="<?php  echo $path; ?>" width="200" height="100"/>
						<textarea name="<?php echo $index ?>" rows="5" cols="40"></textarea> <!--textarea name is the ID of the corresponding resource-->
						<input type="hidden" name="<?php echo 'resourceID'.$i; ?>" value="<?php echo $index ?>">
					<?php }	?>
					<input type="hidden" name="numResources" value="<?php echo count($post->getContent()); ?>"/>
					<input type="submit" value="Prosegui" /> 
				</form>
			</fieldset>
		<?php }
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
		//TODO da implementare
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
		//TODO da implementare
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
		//TODO da implementare
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
		if(!isset($_GET["phase"]) || count($error) != 0){
		?>
		<form name="<?php echo $name; ?>Post" action="?type=videoreportage" method="post">
			<!--<p class="post_headline"><label>Occhiello:</label><br />
				<input class="post_headline" name="headline" value="<?php echo $post->getHeadline(); ?>"/></p>-->
			<p class="title"><label>Titolo:</label><br/>
				<input class="post_title" name="title" value="<?php echo $post->getTitle(); ?>"/></p>
			<p class="post_subtitle"><label>Sottotilolo:</label><br />
				<input class="post_subtitle" name="subtitle" value="<?php echo $post->getSubtitle(); ?>"/></p>
			<p class="content"><?php
				if($post->getContent() != "")
					echo youtubeManager::getVideoPlayer($post->getContent());
				?> <fieldset><legend>Video:</legend>
						<label>Inserisci l'URL del video: </label><input type="text" name="userUrl" value="<?php echo youtubeManager::getUrl($post->getContent()); ?>">
				</fieldset>
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
			<input type="hidden" name="phase" value="<?php if(isset($_POST["phase"])==2) echo '2'; else echo '1';?>">
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
			require_once 'manager/MapManager.php';
			MapManager::setCenterToMap($post->getPlace(), "map_canvas");
			?>
		</form>
		<?php }
	}
	
	static function showContestDetails($contest) {
		//TODO da implementare
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
				require_once 'manager/CategoryManager.php';
				$cat = CategoryManager::loadAllCategories();
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
			$check = array_search($node->getName(), $checked) !== false;
			if($check) array_diff($checked, array($node->getName()));
		}
		echo '<li><input type="checkbox" name="cat[' . ++$counter . ']" value="' . $node->getName() .'" ' . ($check ? "checked " : "") . '/> <label>' . $node->getName() . '</label></li>';
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
	
	static function showNoPostWarning() {
		?>
	<div class="post nopost" id="nopost">
		<div class="post_header">
			<div class="post_headline clear">What are you waiting?</div>
			<div class="post_title">No Post in this site yet!</div>
			<div class="post_subtitle">Write a new post right now!</div>
		</div>
		<div class="post_content clear">
			<p><span id="post_place_publichi" class="post_place">PUBLICHI</span> -
			Wait! PubliChi is empty? What happened?</p>
			<p>You just installed it? So, what are you waiting? Write a <a href="<?php echo FileManager::appendToRootPath("Post/New"); ?>">new post</a> and start a brand new site!!</p>
			<p>Come on! This screen is soo boring!!!</p>
			<div class="post_authorname"><a href="<?php echo FileManager::appendToRootPath("User/ioesisto"); ?>">IoEsisto</a></div>	
		</div>
	</div>
		<?php
	}
}

?>
