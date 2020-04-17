<?php
session_start();
require_once('common.php');
$db = connectToDB();

/* The upload has to be split into two parts because we need a user confirmation before overwriting.
  This part does all of the pre-checking, then it calls doUpload.php
*/ 

if (empty($username)) {
	//print error and go back to previous screen.
	echo "<script>alert('No username! Cannot upload. Login again.')</script>";
	echo "<script>";
	echo "window.location.href='main.php'";
	echo "</script>";
	exit;
}

$filename = $_FILES["fileToUpload"]["name"];
$foldername = $_POST['foldername'];
if ($foldername == "none") $foldername = '';
if (empty($foldername))
	$targetdir = "files/$username/";
else
	$targetdir = "files/$username/$foldername/";

//check for empty file (this is also done before submitting the data)
if (empty($filename)) {
	//print error and go back to previous screen.
	echo "<!DOCTYPE html><script>";
	echo "alert('You don\'t seem to have selected a file');";
	echo "window.location.href='main.php';";
	//echo "window.history.back();"; 
	echo "</script>";
	exit;
}


//Find if the file already exists, if so, don't create a new entry, just erase comment and mark.

//Need two versions of this depending if path is empty or not.
if (empty($foldername)) {
	$sql = "SELECT id from fileinfo WHERE username = ? AND filename = ? AND (path IS NULL OR path = '')";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("ss", $username, $filename);
		$stmt->execute();
		$stmt->store_result();                                                                                                                                                                       
		$stmt->bind_result($id);
//		$result = $stmt->get_result();
		$stmt->fetch();
		$num_rows = $stmt->num_rows;  
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
} else {
	$sql = "SELECT id from fileinfo WHERE username = ? AND filename = ? AND path = ?";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("sss", $username, $filename, $foldername);
		$stmt->execute();
		$stmt->store_result();                                                                                                                                                                       
		$stmt->bind_result($id);
		//$result = $stmt->get_result();
		$stmt->fetch();
		$num_rows = $stmt->num_rows;  
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
}

//die("id=".$id." rows=".$num_rows);

$fileExists = true;
if (0 === $num_rows) {
	$fileExists = false;
	$id=-1;
}
//Store all needed variables in SESSION
$_SESSION["fileID"]=$id;
$_SESSION["foldername"]=$foldername;
$_SESSION["targetdir"]=$targetdir;

$_SESSION["filesArray"]=$_FILES;

/*
echo "<pre>"; 
print_r($_FILES); 
echo "</pre>"; 
*/


//Find a way to reload this program with the authorization to continue the upload of a duplicate file.
if ($fileExists) {
	echo "<!DOCTYPE html><script>";
	echo 'ans = confirm("This file already exists, do you want to overwrite it?");';
	echo 'if (ans == false) window.location.href="main.php";';
	echo 'else window.location.href="doUpload.php";';
	echo "</script>";
} else {
	include 'doUpload.php';
}