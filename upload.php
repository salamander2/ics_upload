<?php
	session_start();
	require_once('common.php');
	$db = connectToDB();

	if (empty($username)) {
		//print error and go back to previous screen.
		echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
		$url = "index.php";
		echo "<script type='text/javascript'>";
		echo "window.location.href='$url'";
		echo "</script>";
		exit;
	}
	$target_dir = "files/$username/";
	$filename = $_FILES["fileToUpload"]["name"];

	//TODO check for empty file before leaving previous webpage.
	if (empty($filename)) {
		//print error and go back to previous screen.
		echo "<script>alert('You don\'t seem to have selected a file')</script>";
		$url = "center.php";
		echo "<script type='text/javascript'>";
		echo "window.history.back();"; 
		echo "</script>";
		exit;
	}

	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
		//echo "<script>alert('The file has been successfully uploaded')</script>";

	} else {
		echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
		$url = "center.php";
		echo "<script type='text/javascript'>";
		echo "window.location.href='$url'";
		echo "</script>";
		exit;
	}

	$sql = "INSERT INTO fileinfo(username,filename) VALUES ('$username','$filename')";
	mysqli_query($db,$sql);
	$url = "center.php";
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url'";
	echo "</script>";
	exit;
?>