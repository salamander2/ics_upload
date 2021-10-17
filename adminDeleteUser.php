<?php
//TODO : add prepared statments here

//	header("Content-Type: text/html; charset=utf-8");
session_start();
require_once "common.php";
$db = connectToDB();
//make sure that this is the admin user!
if ($username != ADMINUSER) {
	header("Location: main.php");
}

$user = $_POST["user"];
die("danger - fix username:".$user);

//TODO:  make into prepared statements
array_map('unlink', glob("../files/$user/*.*"));
rmdir("../files/$user");
$sql = "DELETE FROM users WHERE username = '$user'";
mysqli_query($db,$sql);
$sql = "DELETE FROM fileinfo WHERE username = '$user'";
mysqli_query($db,$sql);
header("Location:adminMain.php");
