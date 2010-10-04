<?php
	ini_set("display_errors", "On");
	error_reporting(E_ALL ^ E_WARNING);
	require_once("settings.php");	
	require_once(USER_DIR . "/UserPage.php");
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
			UserPage::showLoginForm();
			if (Session::getUser() != false )
				header('Location: http://127.0.0.1/IoEsistoProgetto/v02/sessiontest.php');
		}
		else
		{
			if ( isset($_GET['logout']) )
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
