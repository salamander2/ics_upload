<?php
	session_start();
	require_once('common.php');

//TODO get the filename and path from the SQL database based on $id
	$filename = $_POST["filename"];
	unlink("files/$username/$filename");
	//echo "$username/$filename Delete Successfully";  THIS BLOCKS THE PAGE FROM GOING TO MAIN.PHP
	$db = connectToDB();

	$sql = "DELETE from fileinfo where filename= ? and username = ?";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("ss", $filename, $username);
		$stmt->execute();
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}

	header('Location:main.php');
?>
