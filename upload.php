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
$foldername = $_POST['foldername'];
if ($foldername == "none") $foldername = '';
if (!empty($foldername)) $target_dir = $target_dir.$foldername.'/';

//check for empty file (this is also done before submitting the data)
if (empty($filename)) {
	//print error and go back to previous screen.
	echo "<script>alert('You don\'t seem to have selected a file')</script>";
	echo "<script'>";
	echo "window.history.back();"; 
	echo "</script'>";
	exit;
}

//TODO: what happens with two files with the same name in different folders. Does it work?

//Find if the file already exists, if so, don't create a new entry, just erase comment and mark.


//Need two versions of this depending if path is empty or not.
if (empty($foldername)) {
	$sql = "SELECT filename from fileinfo WHERE username = ? AND filename = ?";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("ss", $username, $filename);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->fetch();
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
} else {
	$sql = "SELECT filename from fileinfo WHERE username = ? AND filename = ? AND path = ?";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("sss", $username, $filename, $foldername);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->fetch();
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
}
$row_cnt = mysqli_num_rows($result);	
$fileExists = true;
if (0 === $row_cnt) $fileExists = false;

if ($fileExists) {
	echo "<!DOCTYPE html><script>";
	echo 'ans = confirm("This file already exists, do you want to overwrite it?")';
	echo 'if (ans == false) window.location.href="main.php";';
	echo "</script>";
	//	exit;	//TODO: find a way to reload this program with the authorization to continue the upload of a duplicate file.
}

//actually do the uploading
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	//echo "<script>alert('The file has been successfully uploaded')</script>";
} else {
	echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
	$url = "main.php";
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url'";
	echo "</script>";
	exit;
}

if ($fileExists) {
	//		die("exists");
	$sql = "UPDATE fileinfo SET comment='', mark='', time=now() WHERE username = ? AND filename = ? AND path = ?";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("sss", $username, $filename, $foldername);
		$stmt->execute();
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
	header("Location: main.php");
	exit;
}

//add new entry to database
if (empty($foldername)) {
	$sql = "INSERT INTO fileinfo(filename, username) VALUES (?,?)";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("ss", $filename, $username);
		$stmt->execute();
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
} else {
	$sql = "INSERT INTO fileinfo(filename, username, path) VALUES (?,?,?)";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("sss", $filename, $username, $foldername);
		$stmt->execute();
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
}
header('Location:main.php');

?>