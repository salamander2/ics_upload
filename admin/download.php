<?php
/**
 * Created by PhpStorm.
 * User: Lisa
 * Date: 2018/5/14
 * Time: 21:22
 */
$username = explode('-',$_POST["filename"])[0];
$filename = explode('-',$_POST["filename"])[1];
$file_url = "../files/$username/$filename";
header('Content-Type: application/octet-stream');
header("Content-Transfer-Encoding: Binary");
header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
readfile($file_url);
$url = "index.php";
echo "<script type='text/javascript'>";
echo "window.location.href='$url'";
echo "</script>";
exit;