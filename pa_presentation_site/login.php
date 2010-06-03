<?php
require_once("classes/users.php");

if(isset($PHPSESSID)) {
	$err = "SESSION";
	$accesso = accesso();
} else {
	$err = "NIENTE";
	$accesso = 0;
}
$path = "http://" . $_SERVER["SERVER_NAME"] . $_GET["red"];	
if($accesso>0 && session_is_registered("nick")) {
	header("location: " . str_replace("|","&",$path));	
} else if($accesso>0)
	header("location: " . str_replace("|","&",$path));	
else
	$user = "";

if(!isset($_POST["nick"])) {

	if(isset($_GET['mex'])) {
		$messaggio = str_replace("K","&",stripslashes(html_entity_decode($_GET['mex'])));
		if(isset($_GET['ok']))
			$color = "#33CC66";
		else
			$color = "#CC0000";
	}
	if(isset($_GET['mes'])) {
		$messaggio = str_replace("K","&",stripslashes(html_entity_decode($_GET['mes'])));
		if(isset($_GET['ok']))
			$color = "#33CC66";
		else
			$color = "#CC0000";
	}
require_once("template.php");
IEpageHeader($title);

IEheader($user);
IEcontentHeader();
?>
				<div class="text_content">
            		<div class="text_title">Login</div>
<?php
	if(isset($mes)) {
?>
					<div style="margin:15px auto;width:80%;padding:10px;background-color:<?php echo $color; ?>;text-align:center;"><?php echo stripslashes($messaggio); ?></div>
<?php
	}
?>
            		<form method="post" action="login.php?red=<?php echo $_GET['red']; ?>">
            			<input name="nick" type="text">&nbsp;
            			<input name="pwd" type="password">&nbsp;
            			<input type="submit" value="Login">
            		</form>
            	</div>
            	
            	<div class="text_content">
            		<div class="text_title">Registrati</div>
<?php
	if(isset($mex)) {
?>
					<div style="margin:15px auto;width:80%;padding:10px;background-color:<?php echo $color; ?>;text-align:center;"><?php echo stripslashes($messaggio); ?></div>
<?php
	}
?>
            			Non sei registrato? Registrarti ti permette di creare articoli e lasciare commenti.
            		<div class="post">
            			<form enctype="multipart/form-data" name="form" method="post" action="registrati.php">
<script language="Javascript">
function controlla() {
	if(document.form.nick.value.length > 0 &&
		document.form.pwd.value.length >= 8 && document.form.pwd.value == document.form.pwd2.value &&
		document.form.mail.value != "" && document.form.mail.value == document.form.mail2.value) {
		document.form.submit.disabled = false;
		return true;
	} else {
		document.form.submit.disabled = true;
		return false;
	}
}
</script>
            				<input name="nick" type="text" /> Scrivi il tuo nickname<br />
            				<input name="pwd" onKeyPress="javascript:controlla()" type="password" /> Scrivi la password che vuoi utilizzare (almeno 8 caratteri)<br />
            				<input name="pwd2" onKeyPress="javascript:controlla();" type="password" /> Scrivi nuovamente la password<br />
            				<input name="mail" onKeyPress="javascript:controlla();" type="text" /> Scrivi la tua email<br />
            				<input name="mail2" onKeyPress="javascript:controlla();" type="text" /> Scrivi nuovamente la tua email<br />
            				<input type="file" name="img"> Immagine personale<br />
            				<input name="news" type="checkbox" /> Vuoi ricevere la newsletter?<br />
<!--            				<em>Sto lavorando per permettere di inviare una immagine, voglio inserire il chapka prima.</em><br />		-->
            				<input name="submit" type="submit" disabled="true" value="Registrati" />
            			</form>
            		</div>
				</div>
<?php
	IEfooter();
	IEsponsor();
	IEcontentFooter();
	IEpageFooter();
} else {
	$echo = "";
	0<login($_POST["nick"],$_POST["pwd"]) ? $echo = "connesso": $echo = "non connesso";
	echo $echo;
	$path = "http://" . $_SERVER["SERVER_NAME"] . $_GET['red'];
	
	header("location: " . str_replace("|","&",$path));
//	header("location: http://localhost:8888/ioesisto/pa_presentation_site/");
}
?>
