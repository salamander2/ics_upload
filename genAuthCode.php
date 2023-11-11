<?php
/*  genAuthCode.php
 *  *** This is AJAX
 *  This makes a new random 5 digit code that must be typed in when someone is trying to create an account.
 *  Called from: adminUserList.php
 */ 
session_start();
require_once "common.php";

//Check if logged in. If not, redirect to index.php 
if (!isset($username) || empty($username)) header("Location: logout.php");
if ($_SESSION["authkey"] != AUTHKEY) { 
    header("Location:index.php?ERROR=Failed%20Auth%20Key"); 
}  
// all of the above stuff works...

$newCode = rand(0,9) . rand ( 1000 , 9999 );
$oldCode = AUTHCODE;
//Now replace the line that has "AUTHCODE" with the following
#define('AUTHCODE','79351');

#THis does not work as user does not have write access to /tmp
$cmd = "sed -i 's/".AUTHCODE."/$newCode/' config.php";
# This might work: 
$cmd = "CONTENT=$(cat config.php); echo \"\$CONTENT\" | sed 's/".AUTHCODE."/$newCode/' > config.php";
echo shell_exec ($cmd );

echo "New authcode is $newCode (after logging in again)";
/*  *.php files do not seem to be able to be opened for reading!
$fp = fopen("config.php", "r+");
if ($fp === false) {
	echo "FOPEN failed!";
	return;
}
$data = array();
echo "AAA";
while(!feof($fp)) {
	echo "BBB";
    $line = fgets($fp);
    echo $line.PHP_EOL;
    if (strpos($line,"AUTHCODE") !== false) {
    	$line = "define('AUTHCODE','$newCode');" ;
    }
    $data[] = $line;
}
fclose($fp);
 */
?>
