<?php
session_start();
require_once "common.php";


//Check if logged in. If not, redirect to index.php 
if (empty($username)) header("Location: logout.php");

//echo $username;
//echo $fullname;
//echo $pwdhash."<br>";
$db = connectToDB();
$error_message = "";
$sql = "SELECT filename,path,time,comment,mark from fileinfo WHERE username = ?";
if ($stmt = $db->prepare($sql)) {
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->fetch();
	$stmt->close();
} else {
	$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
	$message_ .= 'SQL: ' . $sql;
	die($message_);
}

$data = mysqli_fetch_all($result);

$directory = "./files/$username";
//remove . and .. from directory listings
//$scanned_directory = array_diff(scandir($directory), array('..', '.'));
$scanned_directory = scandir($directory);
/*
foreach($scanned_directory as $file)
{
    if (!is_dir($file))
    {
        echo $file.'\n';
    }
}
*/
?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<title>Classroom <?= $username?></title>
	<link rel="stylesheet" href="./resources/bootstrap.min.css">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
<script>
function confirmAction() {
	return confirm("Are you sure");
}

function validateData() {
	var x, text;
	x = document.getElementById("fileToUpload").value;
	if (!x || 0 === x.length) {
		text = "You must choose a file";
		//text = "<div class=\"error\">" + text + "</div>";
		document.getElementById("error_message").outerHTML = '<div id="error_message" class="alert alert-danger w-50"></div>';
		document.getElementById("error_message").innerHTML = text;
		return false;
	}
	return false;
}
</script>
	<div class="container my-2">
		<div class="card Xtext-center bg-secondary my-2 py-2">
			<h3 class="text-center text-white">Hello <?php echo $fullname?> <button class="btn float-right btn-warning mr-2 shadow" onclick="location.href='logout.php'">Logout</button></h3>
			<form action="upload.php" method="post" enctype="multipart/form-data">
			<div class="row  mx-2">
    			<div class="col-sm-4">
				<input class="btn btn-primary shadow" type="file" name="fileToUpload" id="fileToUpload">
				</div>
    			<div class="col-sm-4"></div>
    			<div class="col-sm-4">
				<input class="btn btn-success shadow" type="submit" value="Upload chosen file" name="submit" onclick="return validateData()">
				</div>
			</div>
			</form>
		</div>
		<div id="error_message"></div>
		<div class="text-secondary">Uploaded files</div>
		<table class="table table-bordered">
			<caption>Uploaded files</caption>
				<tr>
					<th>FileName</th>
					<th>Path</th>
					<th>Date</th>
					<th></th>
					<th>Comments</th>
					<th>Marked?</th>
				</tr>

<?php

//filename,path,time,comment,mark
foreach ($data as $item){
	$filename = $item[0];
	$path = $item[1];
	$time = $item[2];
	$comment = $item[3];
	$mark = $item[4];
	echo "<tr>";
	echo "<td>$filename</td>";
	echo "<td>$path</td>";
	echo "<td>$time</td>";
	echo "<td>";
	echo "<form class='d-inline' method='post' action='download.php'><input name='filename' value='$filename' hidden><button class='btn btn-info shadow'>Download</button></form> &nbsp ";
	echo "<form class='d-inline' method='post' action='delete.php' onsubmit=\"return confirmAction()\"> <input name='filename' value='$filename' style='outline: none;' hidden><button class='btn btn-danger shadow'>Delete</button></form></td>";
	echo "<td>$comment</td>";
	echo "<td>$mark</td>";
	echo "</tr>";
}

?>
		</table>
	</div>

TODO: add option to make folder (just one level) and upload files to that.
<?php 
//TODO: fix the path stuff
//var_dump($scanned_directory);
?>
</body>

</html>