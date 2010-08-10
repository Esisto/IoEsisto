<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>IOESISTO prova</title>
</head>

<body>

<?php
	require_once("post/PostTest.php");
	
	echo testPost();
	echo "<br />";
	echo testComment();
	echo "<br />";
	echo testVote();
	echo "<br />";
	echo testEditPost();
?>

</body>
</html>
