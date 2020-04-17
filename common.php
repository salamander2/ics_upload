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

function runSimpleQuery($mysqli, $sql_) {                                                                                                                                                                
    $result = mysqli_query($mysqli, $sql_);                                                                                                                                                              
//  if (!$mysqli->error) {                                                                                                                                                                               
//      printf("Errormessage: %s\n", $mysqli->error);                                                                                                                                                    
//  }                                                                                                                                                                                                    
                                                                                                                                                                                                         
    // Check result. This shows the actual query sent to MySQL, and the error. Useful for debugging.                                                                                                     
    if (!$result) {                                                                                                                                                                                      
       $message_  = 'Invalid query: ' . mysqli_error($mysqli) . "\n<br>";                                                                                                                                
       $message_ .= 'SQL: ' . $sql_;                                                                                                                                                                     
       die($message_);                                                                                                                                                                                   
    }                                                                                                                                                                                                    
    return $result;
}   

function clean_input($string) {
    $string = trim(strip_tags(addslashes($string)));
    return $string;
}  

