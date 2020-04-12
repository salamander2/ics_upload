<?php
	session_start();
	require_once('common.php');

	$filename = $_POST["filename"];
	unlink("files/$username/$filename");
	echo "$username/$filename Delete Successfully";
	$db = connectToDB();
	$sql = "DELETE from fileinfo where filename='$filename' and username = '$username'";
	mysqli_query($db,$sql);
	header('Location:main.php');
?>