<?php
	session_start();
	require_once('common.php');
	$db = connectToDB();

	$id = $_POST["id"];

	$sql = "SELECT filename, path FROM fileinfo WHERE id = $id";
	$result = runSimpleQuery($db,$sql);
	$response = mysqli_fetch_row($result);
	$filename=$response[0];
	$path=$response[1];

	if (empty($path))
		$url="files/$username/$filename";
	else 
		$url="files/$username/$path/$filename";

	unlink($url);

	$sql = "DELETE from fileinfo where id = $id";
	runSimpleQuery($db,$sql);

	header('Location:main.php');
