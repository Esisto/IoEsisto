<?php
error_reporting(E_ALL);
require_once("classes/users.php");

if(isset($PHPSESSID)) {
	$err = "SESSION";
	$accesso = accesso();
} else {
	$err = "NIENTE";
	$accesso = 0;
}
$path = "http://" . $_SERVER["SERVER_NAME"] . "/ioesisto/login.php?red=" . $_SERVER["PHP_SELF"];	
if($accesso>0 && session_is_registered("nick"))
	$user = $_SESSION["nick"];	
else if($accesso>0)
	$user = $_SESSION["nick"];	
else {
	header("location: " . str_replace("|","&",$path));
}

if(!isset($_POST["titolo"])) {

require_once("template.php");
IEpageHeader("IoEsisto.com");

IEheader($user);
IEcontentHeader();
?>
            	<div class="text_content">
            		<div class="text_title">Crea una news</div>
            		<div class="post">
            			<form method="post" action="crea.php">
            				<input name="titolo" type="text"> Titolo<br />
							<textarea name="testo" cols="15" rows="10"></textarea>
            				<input type="submit" value="Crea">
            			</form>
            		</div>
            	</div>
<?php

IEfooter();
IEsponsor();
IEcontentFooter();
IEpageFooter();

} else {
	require_once("classes/posts.php");
	$dir = "post/";
	$p = new post();
	$text = $_POST['testo'];
	$titolo = $_POST['titolo'];
	
	$text = stripcslashes($text);
	$p->fillPost($titolo, $user,nl2br($text));
	if(!file_exists("post/" . $p->creationData . ".txt")) {
		$file = fopen("post/" . $p->creationData . ".txt", "w");
		
		fwrite($file, serialize($p));
	} else {
		echo "NON SALVATO<br />";
	}
		echo serialize($p);

			$p2 = unserialize(file_get_contents("post/" . $p->creationData . ".txt"));
			$test = stripslashes($p2->text);
			echo "<p>Testo scritto: $test</p>";
			echo "<a href='http://" . $_SERVER["SERVER_NAME"] . "/ioesisto/'>Torna.</a>";
}
?>