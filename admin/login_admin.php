<?php
	session_start();
	require_once('common.php');
	$username = $_COOKIE["admin"];

	if (isset($username)){
		header('Location:main_admin.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Classroom</title>
	<link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<div align="center">
	<form method="post" action="_login.php">
		<h3>Login as Administrator</h3>
		<label>Password:</label><input name="username">
		<br/>
		<button class="btn-primary btn">Login</button>
	</form>
</div>
