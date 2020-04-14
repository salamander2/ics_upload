<?php
	session_start();
	require_once('common.php');

	$folder = $_POST["folder"];
	$folder = clean_input($folder);
	$folder = str_replace(' ', '_', $folder);
	mkdir("files/$username/$folder");

	header('Location:main.php');
?>
