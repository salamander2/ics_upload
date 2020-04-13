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
	<title>Classroom: Admin</title>
	<link rel="stylesheet" href="./resources/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
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
