<?php
session_start();
require_once('common.php');
$db = connectToDB();

if ($_SESSION["authkey"] != AUTHKEY) { 
    header("Location:index.php?ERROR=Failed%20Auth%20Key"); 
}  
//make sure that this is the admin user!
//NO! Users need to download and delete as well
//if ($username != ADMINUSER) {
//	header("Location: main.php");
//}

$student = $username;
if ($username == ADMINUSER) {
	$student = $_POST["student"];		
}

$id = $_POST["id"];
$sql = "SELECT filename, path FROM fileinfo WHERE id = $id";
$result = runSimpleQuery($db,$sql);
$response = mysqli_fetch_row($result);
$filename=$response[0];
$path=$response[1];

if (empty($path))
	$url="files/$student/$filename";
	else 
	$url="files/$student/$path/$filename";

	unlink($url);

	$sql = "DELETE from fileinfo where id = $id";
	runSimpleQuery($db,$sql);

	if ($username == ADMINUSER) {
		if (isset($_SERVER["HTTP_REFERER"])) {
			 header("Location: " . $_SERVER["HTTP_REFERER"]);
		 } else {
			header('Location:adminMain.php');
		}
	} else {
		header('Location:main.php');
	}
