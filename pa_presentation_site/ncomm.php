<?php
error_reporting(E_ALL);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Shadows</title>
</head>
<body>
<div id="content">

<?
require_once("classes/posts.php");
if(strpos($_POST["comment"],"<a")===false) {
	$comm = new comment();
	$comm->fillComment($_POST["nick"],$_POST["comment"]);
	$redirect = $_GET["red"];
	$id = $_POST["post"];
	
	
	if(file_exists("post/" . $id . ".txt")) {
		$post = unserialize(file_get_contents("post/" . $id . ".txt"));
		$post->addComment($comm);
		
		$f = fopen("post/" . $id . ".txt","w");
		fwrite($f,serialize($post));

		$mail_to = "samvise85.mpi@gmail.com";
		$mail_from = "do_not_reply@shadowmiller.altervista.org";
		$mail_subject = "Commento alla news: ";
		$mail_subject .= $post->title;
		$mail_body = "È stato lasciato un commento da " . $_POST["nick"] . " alla news <i>";
		$mail_body .= $id . "</i>";
		$mail_body .= "<p><a href='" . str_replace("|","&",$redirect) . "'>Leggi</a></p>";
		// Intestazioni HTML
		$mail_in_html = "MIME-Version: 1.0\r\n";
		$mail_in_html .= "Content-type: text/html; charset=UTF-8";
		$mail_in_html .= "From: <$mail_from>";
		// Processo di invio
		mail($mail_to, $mail_subject, $mail_body, $mail_in_html);
	}
?>
	<div id="header">
		<div id="title"><h1>Shadows</h1></div>
		<div id="sottotitolo"><h5>Commento inserito. <a href="<?php echo $redirect; ?>">Torna indietro</a>.</h5><br />
        <?php echo "../blog/newpost/" . $id . ".txt"; ?><br />
        
        </div>
	</div>
<?php
} else {
?>
	<div id="header">
		<div id="title"><h1>Shadows</h1></div>
		<div id="sottotitolo"><h5>Commento NON inserito. <a href="<?php echo $redirect; ?>">Torna indietro </a>.<br />
        <?php echo disk_free_space("./"); ?></h5></div>
	</div>
<?php
}
header("location: " . str_replace("|","&",$redirect));

echo $id;
?>
</div>
</body>
</html>