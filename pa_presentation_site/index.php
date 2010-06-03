<?php error_reporting(E_ALL); 

require_once("classes/users.php");
require_once("template.php");
require_once("classes/posts.php");

$accesso = 0; $err1 = "NIENTE";
if(isset($PHPSESSID)) {
	$accesso = accesso(/*&$err1*/);
	$err1 = "SESSION " . $accesso;
}
if($accesso>0 && session_is_registered("nick"))
	$user = $_SESSION["nick"];
else if($accesso>0)
	$user = $nick;
else
	$user = "";

//echo $err1;

$news = ""; $comm = "";

$pagepath = $_SERVER["PHP_SELF"];


if(isset($_GET["news"]))
	$news = $_GET["news"];
if(isset($_GET["comm"]))
	$comm = $_GET["comm"];

$title = "IoEsisto.com";
if(isset($_GET["page"])) {
	$pagename = $_GET["page"];
	$pagepath .= "?page=" . $pagename;
} else
	$pagename = "index";
if(isset($_GET["sub"])) {
	$sub = $_GET["sub"];
	$pagepath .= "|sub=" . $sub;
} else
	$sub = "";
if(isset($_GET["comm"]))
	$comm = $_GET["comm"];
else
	$comm = "";
if(isset($_GET["post"]))
	$post = $_GET["post"];
else
	$post = "";

$content = getContent($pagename,$sub,&$err);
$title2 = createTitle($pagename,$news);
if($title2!="") $title .= $title2;

IEpageHeader($title);

IEheader($user);
IEcontentHeader();

$content = getContent($pagename,$sub,&$err);
$err = "";
$dati = getData($pagename,$content,&$err);

if($pagename == "index") {
	if(is_array($dati))	{
		$leftright = "right";
		foreach($dati as $nome => $post) {
			if($nome=="")
				continue;
			
			writePost($post,$leftright,$user,false);
			
			if($leftright == "right") $leftright = "left";
			else $leftright = "right";
		}
	}
} else if($pagename == "leggi" || $pagename == "modificapost" || $pagename == "modificacomm") {
	$modif = false;
	if($pagename == "modificapost") {
		$dati = $dati[0];
		$modif = true;
	} else if($pagename == "modificacomm") {
		$dati = $dati[0];
	}
	$leftright = "right";
	writePost($dati,$leftright,$user,$modif);
	$leftright = "left";
?>
            	<div class="text_content">
<?php          	
	if(is_array($dati->comments) && count($dati->comments)>0) {
		if($pagename == "modificacomm") {
			writeComment($dati->comments[$_GET["sub"]],$leftright,$user,true,$post);			
		} else {
			foreach($dati->comments as $date => $commento) {
				writeComment($commento,$leftright,$user,false,$post);
				if($leftright == "right") $leftright = "left";
				else $leftright = "right";
			}
		}
?>
				</div>
            	<div class="text_content">       	
<?php
	}
?>
            		<div class="text_title">Aggiungi un commento</div>
            		<form method="post" action="ncomm.php?red=<?php echo $pagepath;?>">
<?php
	if($user != null && $user != "") {
?>
						<input name="nick" type="hidden" value="<?php echo $user;?>" />
            			<input name="mail" type="text" /> Scrivi la tua mail (opzionale)<br />
						Hai effettuato il login. Il tuo nickname verrà direttamente associato al messaggio. Lascia il tuo commento:<br />
<?php
	} else {
?>
            			<input name="nick" type="text" /> Scrivi il tuo nickname<br />
            			<input name="mail" type="text" /> Scrivi la tua mail (opzionale)<br />
            			Effettua il login per aggiungere una immagine e non dover scrivere il nickname. Lascia il tuo commento:<br />
<?php
	}
?>
						<input name="post" type="hidden" value="<?php echo $sub;?>" />
            			<textarea name="comment" rows="5"></textarea><br />
            			<input type="submit" value="Invia" />
            		</form>
				</div>
<?php          	
	
}

IEfooter();
IEsponsor();
IEcontentFooter();
IEpageFooter();
?>