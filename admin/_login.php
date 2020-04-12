<?php
	/**
	 * Created by PhpStorm.
	 * User: desmond
	 * Date: 2018-05-14
	 * Time: 10:30 AM
	 */
	header("Content-Type: text/html; charset=utf-8");
	$username = $_POST["username"];
	setcookie('admin',$username);
	header('Location:index.php');