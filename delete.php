<?php
	session_start();
	require_once('common.php');
	$db = connectToDB();

	$id = $_POST["id"];

	$sql = "SELECT filename, path FROM fileinfo WHERE id = $id";
	$result = runSimpleQuery($db,$sql);
	$response = mysqli_fetch_all($result);
	$filename=$response[0];
	$path=$response[1];
	
	if (empty($path))
		unlink("files/$username/$filename");
	else 
		unlink("files/$path/$username/$filename");

	//echo "$username/$filename Delete Successfully";  THIS BLOCKS THE PAGE FROM GOING TO MAIN.PHP

	$sql = "DELETE from fileinfo where id = $id";
	runSimpleQuery($db,$sql);
/*	
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
*/

	header('Location:main.php');
?>
