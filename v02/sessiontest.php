<?php
	ini_set("display_errors", "On");
	error_reporting(E_ALL ^ E_WARNING);
	require_once("settings.php");	
	require_once(USER_DIR . "/UserManager.php");
	session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Login</title>
	</head>
	<body>
	<?php	
	 	if ( !Session::getUser() )
		{
			if ( isset( $_POST['login'] ) )
			{	
				UserManager::login();
				header('Location: http://127.0.0.1/IoEsistoProgetto/v02/sessiontest.php');
			} else {
	?>
	<form action="sessiontest.php" method="post">
		<fieldset>	
			<legend>Login</legend>
							
			<label for="email">Nickname</label><br />
			<input type="text" size="30" id="email" name="nickname" value="iocoesisto" /><br />
				
			<label for="password">Password:</label><br />
			<input type="password" size="30" id="password" name="password" value=
			<?php
				$password = "ma fatti i cazzi tuoi" . sha1("ma fatti i cazzi tuoi");			
				echo '"'. $password . '"';  
			?> /><br />
				
			<input type="submit" value="Log in" id="login" name="login" />
			<input type="reset" value="Reset" id="reset" name="reset" />
 		</fieldset>
	</form>
	<?php
			}
		}
		else
		{
			if ( $_GET['logout'] )
			{
				UserManager::logout();
				header('Location: http://127.0.0.1/IoEsistoProgetto/v02/sessiontest.php');
			}
			else
			{
				$user = Session::getUser();
				$nome = $user->getName();
				$cognome = $user->getSurname(); 				
				echo '<h1>Bevenuto ' . $nome . " " . $cognome . '</h1>'; 
				echo '<a href="sessiontest.php?logout=1">Logout</a>';
			}
		}		
	?>
	</body>
</html>
