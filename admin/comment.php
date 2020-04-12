<?php
	/**
	 * Created by PhpStorm.
	 * User: desmond
	 * Date: 2018-05-14
	 * Time: 9:56 AM
	 */
	header("Content-Type: text/html; charset=utf-8");
	$username = explode('-',$_POST["filename"])[0];
	$filename = explode('-',$_POST["filename"])[1];
	require_once "function.php";
	$conn = connectDB();
	$sql = "select comment from fileinfo where username = '$username' and filename = '$filename'";
	$result = mysqli_query($conn,$sql);
	$response  = mysqli_fetch_assoc($result);
	$comment = $response["comment"];
	?>


<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Classroom</title>
	<link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>
	<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div align="center">
	<h3>Commend For <?php print $username.'/'; print $filename?></h3>
	<form method="post" action="submit_comment.php">
	<textarea style="width: 80%;" rows="10" name="commend"><?php print $commend?></textarea>
		<input name="filename" value="<?php print $username?>-<?php print $filename?>" hidden>
	<br/>
	<button class="btn-lg btn-info" onclick="submit()">Submit</button>
	</form>
</div>

</body>
</html>
