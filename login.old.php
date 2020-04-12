<?php

function isusername($str) {
	if (preg_match('/^[0-9a-zA-Z_]{1,30}$/',$str)){
		return true;
	}else {
		return false;
	}
}

header("Content-Type: text/html; charset=utf-8");
require_once "function.php";
$username = clean_input($_POST['username']);
$password = $_POST["password"];
$PWD = password_hash($password, PASSWORD_DEFAULT);

if (!isusername($username)){
	echo "<script>alert('Invalid Username')</script>";
	$url = "index.php";
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url'";
	echo "</script>";
	exit;
}
$conn = connectDB();
$sql = "select username from users where username = '$username' and password = '$password'";
//	die($sql);
//	exit();
$result = mysqli_query($conn,$sql);
if (mysqli_num_rows($result) >=1){
	setcookie('username',$username);
	setcookie('password',$password);
	header('Location:center.php');
}

?>

<script>
alert("Username or Password is wrong")
window.location.href = 'index.php'
</script>

