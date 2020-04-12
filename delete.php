<?php
	/**
	 * Created by PhpStorm.
	 * User: desmond
	 * Date: 2018-05-09
	 * Time: 8:36 PM
	 */
	header("Content-Type: text/html; charset=utf-8");
	$username = $_COOKIE["username"];
	$filename = $_POST["filename"];
	unlink("files/$username/$filename");
	echo "$username/$filename Delete Successfully";
	require_once "function.php";
	$conn = connectDB();
	$sql = "DELETE from fileinfo where filename='$filename' and username = '$username'";
	mysqli_query($conn,$sql);
	$url = "center.php";
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url'";
	echo "</script>";
	exit;
