<?php
//	include 'session.inc';
	
//	function check_auth() { return 4; }
	
	// DEBUG
	class Session {
		static function getUser() {
			return 1;
		}
	}
	// END DEBUG
?>

<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"-->
<!--"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">-->
<!--<html>-->
<!--	<head>-->
<!--		<title>Login</title>-->
<!--	</head>-->
<!--	<body>-->
<!--	<?php
	// 	if ( !isset( $_SESSION['uid'] ) )
	//	{
	//		if ( isset( $_POST['login'] ) && ( $_POST['login'] == 'Log in' ) && 
	//		 ( $uid = check_auth( $_POST['email'], $_POST['password'] ) ) )
	//		{
	//			/* Login utente corretto: il cookie è impostato */
	//			$_SESSION['uid'] = $uid;
	//			header('Location: http://'. $_SERVER['SERVER_NAME'] . ':8888/ioesisto/v0/session.php');
	//		} else {
	?>
	<form action="session.php" method="post">
<!--		<fieldset>	-->
<!--			<legend>Login</legend>-->
<!--							-->
<!--			<label for="email">E-mail:</label><br />-->
<!--			<input type="text" size="30" id="email" name="email" /><br />-->
<!--				-->
<!--			<label for="password">Password:</label><br />-->
<!--			<input type="password" size="30" id="password" name="password" /><br />-->
<!--				-->
<!--			<input type="submit" value="Log in" id="login" name="login" />-->
<!--			<input type="reset" value="Reset" id="reset" name="reset" />-->
<!-- 		</fieldset>-->
<!--	</form>-->
	<?php
		//	}
		//}
		//else
		//{
		//	if ( $_GET['logout'] )
		//	{
		//		session_start();
		//		$_SESSION = array();
		//		session_destroy();
		//		header('Location: http://'. $_SERVER['SERVER_NAME'] . ':8888/ioesisto/v0/session.php');
		//	}
		//	else
		//	{
		//		echo '<h1>Bevenuto utente con id=' . $_SESSION['uid'] . '</h1>'; 
		//		echo '<a href="index.php?logout=1">Logout</a>';
		//	}
		//}
	?>
<!--	</body>-->
<!--</html>-->
