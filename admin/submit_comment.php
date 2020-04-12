<?php
	/**
	 * Created by PhpStorm.
	 * User: desmond
	 * Date: 2018-05-14
	 * Time: 10:01 AM
	 */
	header("Content-Type: text/html; charset=utf-8");
	$username = explode('-',$_POST["filename"])[0];
	$filename = explode('-',$_POST["filename"])[1];
	$comment = $_POST["comment"];

	require_once "function.php";
	$conn = connectDB();
	$sql = "update fileinfo set comment = '$comment' where username = '$username' and filename = '$filename'";
	mysqli_query($conn,$sql);
	print "Success";
	$url = "index.php";
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url'";
	echo "</script>";
	exit;
