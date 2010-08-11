<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>IOESISTO prova</title>
</head>

<body>

<?php
	require_once("post/PostTest.php");
	require_once("post/collection/CollectionTest.php");
	
	echo testPost();
	echo "<br />";
	echo testComment();
	echo "<br />";
	echo testVote();
	echo "<br />";
	echo testEditPost();
	echo "<br />";
	echo testCollection();
	echo "<br />";
	echo testAddPostToCollection();
?>

</body>
</html>
