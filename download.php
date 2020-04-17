<?php
session_start();
require_once('common.php');
$db = connectToDB();

$id = $_POST["id"];

$sql = "SELECT filename, path FROM fileinfo WHERE id = $id";
$result = runSimpleQuery($db,$sql);
//TODO: update notes: do not user fetch_all when you are getting unique data as it returns an array of arrays. 
$response = mysqli_fetch_row($result);

$filename=$response[0];
$path=$response[1];

if (empty($path)) {
	$file_url = "files/$username/$filename";
} else {
	$file_url = "files/$username/$path/$filename";
}

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
readfile($file_url);
//header('Location:main.php');
