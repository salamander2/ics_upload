<?php

session_start();
require_once('common.php');
$db = connectToDB();

//make sure that this is the admin user!
if ($username != ADMINUSER) {
    header("Location: main.php");
}

## This will go to xmlhttp.responsetext
//die("HERE in updateUser!");

$error_message="";

$frmID = $frmMark = $frmComment = "";

$frmID = clean_input($_POST['fileid']);
$frmMark = clean_input($_POST['mark']);
$frmComment = clean_input($_POST['comment']);

$sql = "UPDATE fileinfo SET mark=?, comment=? WHERE id=?";
if ($stmt = $db->prepare($sql)) {
    $stmt->bind_param("ssi", $frmMark, $frmComment, $frmID );
    $stmt->execute();
    $stmt->close();
} else {
    $message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
    $message_ .= 'SQL: ' . $sql;
    die($message_);
}