<?php
session_start();
require_once('common.php');
$db = connectToDB();

$id = $_SESSION["fileID"];
$foldername = $_SESSION['foldername'];
$targetdir = $_SESSION['targetdir'];
$fileExists = true;
if (-1 === $id) $fileExists = false;

$_FILES=$_SESSION['filesArray'];
$filename=$_FILES["fileToUpload"]["name"];
$targetfile = $targetdir . basename($_FILES["fileToUpload"]["name"]);

/*
echo "<pre>"; 
print_r($_FILES); 
echo "</pre>"; 
die();
*/
//die("file=".$filename . " folder=" . $foldername. " id=" . $id);

//actually do the uploading
if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetfile)) {
	//echo "<script>alert('The file has been successfully uploaded')</script>";
} else {
	echo $targetfile;
	echo "<pre>"; 
	print_r($_FILES); 
	echo "</pre>"; 
	echo "<script>alert('Sorry, there was an error uploading your file. Overwriting files is still causing errors!');";
	echo "window.location.href='main.php'";
	echo "</script>";
	exit;
}

if ($fileExists) {
	$sql = "UPDATE fileinfo SET comment='', mark='', timeUploaded=now() WHERE id=$id";
	if ($stmt = $db->prepare($sql)) {
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
