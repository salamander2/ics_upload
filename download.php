<?php
session_start();
require_once('common.php');

$filename = $_POST["filename"];
$file_url = "files/$username/$filename";
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
readfile($file_url);
header('Location:main.php');
?>