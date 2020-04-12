<?php
	/**
	 * Created by PhpStorm.
	 * User: desmond
	 * Date: 2018-05-14
	 * Time: 10:41 AM
	 */
	header("Content-Type: text/html; charset=utf-8");
	require_once "function.php";
	$conn = connectDB();
	$username = $_POST["username"];

    array_map('unlink', glob("../files/$username/*.*"));
    rmdir("../files/$username");
    $sql = "delete from users where username = '$username'";
    mysqli_query($conn,$sql);
    $sql = "delete from fileinfo where username = '$username'";
    mysqli_query($conn,$sql);
    $url = "index.php";
    echo "<script type='text/javascript'>";
    echo "window.location.href='$url'";
    echo "</script>";
    exit;