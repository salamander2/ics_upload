<?php
	
	header("Content-Type: text/html; charset=utf-8");
	$username = $_POST["username"];
	setcookie('admin',$username);
	header('Location:main_admin.php');