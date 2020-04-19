<?php
session_start();
require_once('common.php');
$db = connectToDB();

$filename = clean_input($_POST['filename']);
$foldername = clean_input($_POST['foldername']);
//Find if the file already exists, if so, don't create a new entry, just erase comment and mark.

//Need two versions of this depending if path is empty or not.
if (empty($foldername)) {
	$sql = "SELECT id from fileinfo WHERE username = ? AND filename = ? AND (path IS NULL OR path = '')";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("ss", $username, $filename);
		$stmt->execute();
		$stmt->store_result();                                                                                                                                                                       
		$stmt->bind_result($id);
//		$result = $stmt->get_result();
		$stmt->fetch();
		$num_rows = $stmt->num_rows;  
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
} else {
	$sql = "SELECT id from fileinfo WHERE username = ? AND filename = ? AND path = ?";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("sss", $username, $filename, $foldername);
		$stmt->execute();
		$stmt->store_result();                                                                                                                                                                       
		$stmt->bind_result($id);
		//$result = $stmt->get_result();
		$stmt->fetch();
		$num_rows = $stmt->num_rows;  
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
}

$fileExists = true;
if (0 === $num_rows) {
	$fileExists = false;
}

//echo json_encode(array('status'=>$fileExists));
echo json_encode($fileExists);