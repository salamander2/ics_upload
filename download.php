<?php
session_start();
require_once('common.php');
$db = connectToDB();

$id = $_POST["id"];

//Don't get username from Session variable since this can also be called from adminMain and then the username is the admin.
$sql = "SELECT filename, path, username FROM fileinfo WHERE id = $id";
$result = runSimpleQuery($db,$sql);
//TODO: update notes: do not user fetch_all when you are getting unique data as it returns an array of arrays. 
$response = mysqli_fetch_row($result);
$filename=$response[0];
$path=$response[1];
$user=$response[2];

if (empty($path)) {
	$file_url = "files/$user/$filename";
} else {
	$file_url = "files/$user/$path/$filename";
}
//die($file_url);

header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
readfile($file_url);
//header('Location:main.php');
