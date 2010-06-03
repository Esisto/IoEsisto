<?php

function IEpageHeader($title) {
	$s = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
	<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>' . $title . '</title>
		<link rel="stylesheet" type="text/css" media="screen,print" href="files/styles.css" />
	</head>
	<body>
		<div id="body_content">';


	echo $s;
}

function IEheader($user) {
	$s = '
			<div id="header_layer">
			    <div id="title"><a href="./" title="Home"><img src="files/logo.png" alt="Home" /></a></div>
				
				<div id="navigation">';
	if($user=="")
		$s .= '
					<form action="login.php?red=/ioesisto/" method="post">
						<input type="text" name="nick" /> <input type="password" name="pwd" /> <input type="submit" value="Login" />
					</form>
					<a href="login.php">Crea nuovo articolo</a>';
	else
		$s .= '
					<a href="crea.php">Crea nuovo articolo</a> - <a href="logout.php">Logout</a>';
	$s .= '
				</div>
			</div>';
	
	echo $s;
}

function IEcontentHeader() {
	$s = '
			<div id="body_layer">
				<div id="body_main_content">';
	
	echo $s;
}

function IEfooter() {
	$s =  '
					<div id="footer_layer">
						This alpha was created by Samvise85 for IoEsisto.com
						<!-- Start of StatCounter Code -->
						<script type="text/javascript">
							var sc_project=5920402; 
							var sc_invisible=1; 
							var sc_security="451d1119"; 
						</script>
						<script type="text/javascript" src="http://www.statcounter.com/counter/counter.js"></script>
					</div>
				</div>';

	echo $s;	
}

function IEsponsor() {
	$s = '
					<div class="insertion_block">
						Inserzioni
						<div class="insertion"></div>
						<div class="insertion"></div>
						<div class="insertion"></div>
						<div class="insertion"></div>
					</div>';
	
	echo $s;
}

function IEcontentFooter() {
	$s = '
			</div>
		</div>';
	
	echo $s;
}

function IEpageFooter() {
	$s = '
	</body>
</html>';
	
	echo $s;
}


function writePost($post,$leftright,$user,$modif) {
?>
            	<div class="text_content">
            		<div class="post_title"><?php echo $post->title; ?></div>
					<div class="post">
<?php
	if($modif) {
		$red = $_SERVER["PHP_SELF"] . "?page=leggi|sub=" . $post->creationData;
?>
					<form action="modifica.php?post=<?php echo $post->creationData; ?>&red=<?php echo $red; ?>" method="post">
<?php
	}
	if(file_exists("login/" . $post->author . ".txt") && file_exists("login/abilitati/" . $post->author . ".txt"))
		$autore = unserialize(file_get_contents("login/" . $post->author . ".txt"));
	if(file_exists("admin/login/" . $post->author . ".txt"))
		$autore = unserialize(file_get_contents("admin/login/" . $post->author . ".txt"));
	
	if(!$modif && isset($autore) && file_exists("img/login/$autore->nick.gif")) {
?>
            			<div class="profile_image float_<? echo $leftright; ?>">
            			    <div class="top"><div class="left"></div><div class="center"></div><div class="right"></div></div>
            				<div class="middle">
            					<div class="left"></div>
            					<div class="center"><img src="img/login/<?php echo $autore->nick; ?>.gif" alt="<?php echo $autore->nick; ?>" /></div>
            					<div class="right"></div>
            				</div>
            				<div class="bottom"><div class="left"></div><div class="center"></div><div class="right"></div></div>
						</div>
<?php
	}
	if($modif) {
?>
						<input name="ntitolo" type="text" value="<?php echo $post->title; ?>" /> Nuovo titolo<br />
						Testo del post:<br />
            			<textarea name="npost" rows="5"><?php echo stripslashes($post->text); ?></textarea><br />
            			<input type="submit" value="Modifica" />
<?php
	} else {
?>
            			<?php echo html_entity_decode(stripslashes($post->text));?><br />
	            		<div style="clear:both;"></div>
            			<div class="post_footer">
<?php
	}
	if(!$modif) {
		if(($a=count($post->comments))==1)
			$ncomm = "1 commento";
		else
			$ncomm = $a . " commenti";
?>

            				<div class="blue_postit comments_postit"><a href="./?page=leggi&sub=<?php echo $post->creationData;?>"><?php echo $ncomm; ?></a></div>
<?php
		if($user!=="") {
			if(file_exists("admin/login/" . $user . ".txt")) {
?>
							<div style="margin-right:10px;" class="blue_postit comments_postit"><a href="./?page=cancellapost&sub=<php echo $post->creationData;?>">Cancella</a></div>&nbsp;
							<div style="margin-right:10px;" class="blue_postit comments_postit"><a href="./?page=modificapost&sub=<php echo $post->creationData;?>">Modifica</a></div>&nbsp;
	            			<div style="clear:both;"></div>
<?php
			}
		}
?>
            				Scritto da <?php echo $post->author; ?> il <?php echo date("j M Y @ g:i A", $post->creationData); ?>.
<?php
	} else {
?>
						</form>
<?php
	}
?>
            			</div>
            			<div style="clear:both;"></div>
            		</div>
            	</div>	
<?php
}

function writeComment($commento,$leftright,$user,$modif,$post) {
?>
					<div class="post">
<?php
	if(!$modif) {
		if(file_exists("login/" . $commento->author . ".txt") && file_exists("login/abilitati/" . $commento->author . ".txt"))
			$autore = unserialize(file_get_contents("login/" . $commento->author . ".txt"));
		if(file_exists("admin/login/" . $commento->author . ".txt"))
			$autore = unserialize(file_get_contents("admin/login/" . $commento->author . ".txt"));
		
		if(isset($autore) && file_exists("img/login/$autore->nick.gif")) {
?>
            			<div class="profile_image float_<? echo $leftright; ?>">
            			    <div class="top"><div class="left"></div><div class="center"></div><div class="right"></div></div>
            				<div class="middle">
            					<div class="left"></div>
            					<div class="center"><img src="img/login/<?php echo $autore->nick; ?>.gif" alt="<?php echo $autore->nick; ?>" /></div>
            					<div class="right"></div>
            				</div>
            				<div class="bottom"><div class="left"></div><div class="center"></div><div class="right"></div></div>
						</div>
<?php
		}
?>
            			<? echo html_entity_decode(stripslashes($commento->text)); ?>
            			<div class="post_footer">
<?php
		if($user!=="") {
			if(file_exists("admin/login/" . $user . ".txt") || $user==$commento->author) {	
?>
							<div style="clear:left;" class="blue_postit comments_postit"><a href="./?page=cancellacomm&sub=<php echo $post->creationData;?>&comm=<php echo $commento->creationData;?>">Cancella</a></div>&nbsp;
							<div style="margin-right:10px;" class="blue_postit comments_postit"><a href="./?page=modificacomm&sub=<php echo $post->creationData;?>&comm=<php echo $commento->creationData;?>">Modifica</a></div>&nbsp;
							<div style="clear:both;"></div>
<?php
			}
		}
?>
            			Scritto da <? echo $commento->author; ?> il <? echo date("j M Y @ h:i A",$commento->creationData); ?>.</div>
            			<div style="clear:both;"></div>
<?php
	} else {
		$red = $_SERVER["PHP_SELF"] . "?page=leggi|sub=" . $post->creationData;
?>
						<form action="modifica.php?post=<?php echo $post->creationData; ?>&comm=<?php echo $commento->creationData; ?>&red=<?php echo $red; ?>" method="post">
							Testo del commento:<br />
            				<textarea name="npost" rows="5"><?php echo stripslashes($commento->text); ?></textarea><br />
            				<input type="submit" value="Modifica" />	
            			</form>
<?php
	}
?>
            		</div>
<?php
}

function createTitle($pagename,$subtitle) {
	if($pagename=="crea")
		$title = "Crea post";
	if($pagename=="archivio" && $subtitle=="")
		$title = "Archivio";
	else if($pagename=="archivio")
		$title = $subtitle;
	if($pagename!=="Crea" && $pagename!=="archivio")
		$title = $subtitle;

	//aggiungere le altre pagine...
	
	return $title;
}
?>