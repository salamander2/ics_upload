<?php

error_reporting(E_ALL);
//for production
//error_reporting(0); ini_set('display_errors','0');

require_once 'config.php';

$username = $_SESSION["username"];
$fullname = $_SESSION["fullname"];

//header("Content-Type: text/html; charset=utf-8");

//Checking User not Logged in. No. This cannot be in common.php as it makes an infinite loop in index.php (which requires common.php)
//if(empty($_SESSION['username'])){
//   header("location:index.php");
//}


function connectToDB() {
//    $servername = getenv('IP');
//   $servername = "localhost";
    $db = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
    if ($db->connect_errno) {
        echo "<script>";
        echo 'alert("Error connecting to database '.$database.'. Your connection has probably timed out. Please log in again");';
        echo "window.location='index.php';";
        echo "</script>";
        // header("Location: index.php");
#       echo "Failed to connect to MySQL database $database : " . mysqli_connect_error();
#       die("Program terminated");
    }
    //mysqli_query($db, "set names UTF8;");
    return $db;
}


function clean_input($string) {
    $string = trim(strip_tags(addslashes($string)));
    return $string;
}  

?>
