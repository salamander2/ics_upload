<?php
/**
 * Created by PhpStorm.
 * User: Lisa
 * Date: 2018/5/14
 * Time: 21:22
 */
$username = $_COOKIE["username"];
$filename = $_POST["filename"];
$file_url = "files/$username/$filename";
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
readfile($file_url);
$url = "center.php";
echo "<script type='text/javascript'>";
echo "window.location.href='$url'";
echo "</script>";
exit;