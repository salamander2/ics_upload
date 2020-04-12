<?php
session_start();
require_once('common.php');

//if (isset($username)){
//	header('Location:main.php');
//unset($_SESSION["newsession"]);
//}

$db = connectToDB();

$username="";
$fullname="";
$error_message = "";

/**** LOGIN LOGIC *******/
if(isset($_POST['submit'])) {
	//validate data
	$username = clean_input($_POST['username']);
	$fullname = clean_input($_POST['fullname']);
	$password = $_POST["password"];

	if(strlen($username) < 5) $error_message = "Username must be at least 5 characters";
	if(strlen($password) < 6) $error_message = "Password must be at least 6 characters";

	$pwdhash = password_hash($password, PASSWORD_DEFAULT);
	$password = "---";
	//create and perfrom SQL
	$sql = "SELECT username FROM users WHERE username = ?";
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
	$row_cnt = mysqli_num_rows($result);
	if ($row_cnt > 0) {		
		$error_message = "** That user already exists! ** <a href=\"index.php\"><div class=\"btn btn-secondary float-right shadow\">Go to Login</div></a>";
	}

	// error message ...
	if ($error_message != "") $error_message = '<div class="card"><div class="alert text-white bg-danger"><b>'. $error_message .'</b></div></div>';
	if (empty($error_message)) {
		#$sql = "INSERT INTO users(username, fullname, password) VALUES('$username','$fullname','$pwdhash')";
		$sql = "INSERT INTO users(username, fullname, password) VALUES(?, ?, ?)";
		if ($stmt = $db->prepare($sql)) {
			$stmt->bind_param("sss", $username, $fullname, $pwdhash);
			$stmt->execute();
			$result = $stmt->get_result();
			$stmt->fetch();
			$stmt->close();
		} else {
			$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
			$message_ .= 'SQL: ' . $sql;
			die($message_);
		}

		//sudo chown www-data: files
		mkdir("files/$username");
		//set session variables
		$_SESSION["username"] = $username;
		$_SESSION["fullname"] = $fullname;
		$_SESSION["pwdhash"] = $pwdhash;
		header('Location:main.php');
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
//This makes sure that the data fields have been filled in before the form is submitted. It uses the ID error_message .
function validateData() {
	var x, text;
	x = document.getElementById("username").value;
	if (!x || 0 === x.length) {
		text = "You must include a username";
		document.getElementById("error_message").outerHTML = '<div id="error_message" class="alert alert-danger w-50"></div>';
		document.getElementById("username").outerHTML = '<input type="text" name="username" id="username"  class="form-control border-danger" placeholder="Username">';
		document.getElementById("error_message").innerHTML = text;
		document.getElementById("username").value = "";
		return false;
	}
	x = document.getElementById("fullname").value;
	if (!x || 0 === x.length) {
		text = "You must include your full name";
		document.getElementById("error_message").outerHTML = '<div id="error_message" class="alert alert-danger w-50"></div>';
		document.getElementById("fullname").outerHTML = '<input type="text" name="fullname" id="fullname"  class="form-control border-danger" placeholder="Fullname">';
		document.getElementById("error_message").innerHTML = text;
		document.getElementById("fullname").value = "";
		return false;
	}
	x = document.getElementById("password").value;
	if (!x || 0 === x.length) {
		text = "You must include a password";
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
<p class="">Register and then start your session</p>

<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" onsubmit="return validateData()">
<div class="input-group mb-3">
<input type="text" name="username" id="username"  class="form-control" placeholder="Username" <?php if (!empty($username)) echo "value=$username";?>>
<div class="input-group-append">
<div class="input-group-text"> <span class="fa fa-envelope"></span> </div>
</div>
</div>
<div class="input-group mb-3">
<input type="text" name="fullname" id="fullname"  class="form-control" placeholder="Full name" <?php if (!empty($fullname)) echo "value=$fullname";?>>
<div class="input-group-append">
<div class="input-group-text"> <span class="fa fa-user"></span> </div>
</div>
</div>
<div class="input-group mb-3">
<input type="password" name="password" id="password" class="form-control" placeholder="Password">
<div class="input-group-append">
<div class="input-group-text"> <span class="fa fa-lock"></span> </div>
</div>
</div>
<div class="row">
<div class="col-4">
<button type="submit" name="submit" class="btn btn-primary btn-block">Register</button>
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

