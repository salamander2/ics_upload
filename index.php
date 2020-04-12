
<?php
session_start();
require_once('common.php');

if (isset($username)){
#	header('Location:center.php');
}

$db = connectToDB();

$error_message = "";

/**** LOGIN LOGIC *******/
if(isset($_POST['submit'])) {
	$username = clean_input($_POST['username']);
	$password = $_POST["password"];

	//check password for that user
	$sql = "SELECT password, fullname FROM users WHERE username = ?";
	if ($stmt = $db->prepare($sql)) {
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->execute();
		$stmt->bind_result($pwdHash, $fullname);
		$stmt->fetch(); //needed to actually get the result for binding
		$stmt->close();
	} else {
		$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
		$message_ .= 'SQL: ' . $sql;
		die($message_);
	}
	// die(var_dump($result));
	// die($pwdHash);
	$row_cnt = mysqli_num_rows($result);
	// die($row_cnt);
	if (0 === $row_cnt) {		
		$error_message = "That user does not exist";
	} elseif (!password_verify ($password, $pwdHash )) {
		$error_message = "Invalid password";
	}
	$password = "---";
	
	// error message ...
	if ($error_message != "") $error_message = '<div class="alert text-white bg-danger"><b> ** '. $error_message .' **</b></div>';
	if (empty($error_message)) {
		$_SESSION["username"] = $username;
		$_SESSION["fullname"] = $fullname;
		header('Location:center.php');
	}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Classroom</title>
<link rel="stylesheet" href="./resources/bootstrap.min.css">
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
<!--    <script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>-->
<!--    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
</head>
<body>
<!-- This form will call either login.php or register.php with the same fields. -->
<script>
function validateData() {
	var x, text;
	x = document.getElementById("username").value;
	if (!x || 0 === x.length) {
		text = "You must include a username";
		//text = "<div class=\"error\">" + text + "</div>";
		document.getElementById("error_message").outerHTML = '<div id="error_message" class="alert alert-danger w-50"></div>';
		document.getElementById("username").outerHTML = '<input type="text" name="username" id="username"  class="form-control border-danger" placeholder="Username">';
		document.getElementById("error_message").innerHTML = text;
		document.getElementById("username").value = "";
		return false;
	}
	x = document.getElementById("password").value;
	if (!x || 0 === x.length) {
		text = "You must include a password";
		//text = "<div class=\"error\">" + text + "</div>";
		document.getElementById("error_message").outerHTML = '<div id="error_message" class="alert alert-danger w-50 "></div>';
		document.getElementById("password").outerHTML = '<input type="text" name="password" id="password"  class="form-control border-danger" placeholder="Password">';
		document.getElementById("error_message").innerHTML = text;
		document.getElementById("password").value = "";
		return false;
	}
	//    document.getElementById("form1").action = "/lockers/edit/" + x;
	return true;
}
</script>

<div class="container mt-5 w-50 text-center">
<h2>Welcome to the <b>File Upload Centre</b></h2>
<div id="error_message"></div>
<div class="card">

<div class="card-header bg-info text-white"><b>For ICS class to upload java files</b></div>
<div class="card-body">
<p class="">Sign in to start your session</p>

<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" onsubmit="return validateData()">
<div class="input-group mb-3">
<input type="text" name="username" id="username"  class="form-control" placeholder="Username">
<div class="input-group-append">
<div class="input-group-text">
<span class="fa fa-envelope"></span>
</div>
</div>
</div>
<div class="input-group mb-3">
<input type="password" name="password" id="password" class="form-control" placeholder="Password">
<div class="input-group-append">
<div class="input-group-text">
<span class="fa fa-lock"></span>
</div>
</div>
</div>
<div class="row">
<div class="col-4">
<button type="submit" name="submit" class="btn btn-primary btn-block">Sign In</button>
</div>
<!-- /.col -->
<div class="col-4">
<a href="register.php" class="text-center">Register a new user</a>
</div>
<!-- /.col -->
<div class="col-4">
<a href="forgot-password.html">I forgot my password</a>
</div>
<!-- /.col -->
</div>
</form>

</div>
</div>
<?php if ($error_message != "") echo $error_message; ?>
</div>


</body>
</html>

