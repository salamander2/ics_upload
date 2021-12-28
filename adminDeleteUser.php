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
//TODO if user does not exist, then return
$sql = "SELECT fullname FROM users WHERE username = ?";
if ($stmt = $db->prepare($sql)) {
	$stmt->bind_param("s", $user);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->execute();
	$stmt->bind_result($fullname);
	$stmt->fetch(); //needed to actually get the result for binding
	$stmt->close();
} else {
	$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
	$message_ .= 'SQL: ' . $sql;
	die($message_);
}
$row_cnt = mysqli_num_rows($result);
if (0 === $row_cnt) {		
	die("That user does not exist. ");
	header("Location:adminMain.php");
}

//die("danger - fix username:".$user);

//This was not working, because file folder was incorrectly located.
//It still does not delete recursively into folders
//array_map('unlink', glob("./files/$user/*.*"));
//array_map('unlink', glob("./files/$user/*"));
//Try using Standard PHP Library
//$dir = "./files/$user/*";
$dir = "./files/$user/";
echo $dir."<br>";
if(file_exists($dir)){
    $di = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
	#var_dump($di);
    $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
    foreach ( $ri as $file ) {
		echo $file . "<br>";
        $file->isDir() ?  rmdir($file) : unlink($file);
    }
} else {
  echo "no files found<br>";
}
echo "files deleted<br>".PHP_EOL;
rmdir("./files/$user");

//THIS WORKS
//TODO:  make into prepared statements
$sql = "DELETE FROM users WHERE username = '$user'";
mysqli_query($db,$sql);
$sql = "DELETE FROM fileinfo WHERE username = '$user'";
mysqli_query($db,$sql);

die("SQL info deleted for ".$user);
//header("Location:adminMain.php");
