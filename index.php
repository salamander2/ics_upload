<?php
session_start();
require_once('common.php');

//TODO: shouldn't username be set to "" no matter what?
if (isset($username)){
	$username = "";
	$_SESSION["username"] = "";
}

//TODO: Add in a connect time, that's udpdated for every action. If the connect time is more than 6 hours old, logout the user.

$db = connectToDB();

$error_message = "";

/**** LOGIN LOGIC *******/
// This also logs in the administrative user. There must be a user registered with the name "adminnimda" and a proper password
// When this user logs on, then the program redirects to the main_admin page and sets a flag that the admin is logged in.

if(isset($_POST['submit'])) {
	$username = clean_input($_POST['username']);
	$password = $_POST["password"];

	//check password for that user
	$sql = "SELECT password, fullname FROM users WHERE username = BINARY ?";
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
		$error_message = "That user does not exist. Please register.<br>(Also check case of username).";
	} elseif (!password_verify ($password, $pwdHash )) {
		$error_message = "Invalid password";
	}
	$password = "---";
	
	// error message ...
	if ($error_message != "") $error_message = '<div class="alert text-white bg-danger"><b> ** '. $error_message .' **</b></div>';
	if (empty($error_message)) {
		$_SESSION["username"] = $username;
		$_SESSION["fullname"] = $fullname;
		//This is set here upon login (AND ALSO IN register.php)  and then session-authkey is never set again.
		$_SESSION["authkey"] = AUTHKEY;

		//Update last login timestamp
		$sql = "UPDATE users set lastLogin=NOW() WHERE username = BINARY ?";
		if ($stmt = $db->prepare($sql)) {
			$stmt->bind_param("s", $username);
			$stmt->execute();
			$stmt->close();
		} else {
			$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
			$message_ .= 'SQL: ' . $sql;
			die($message_);
		}

		if ($username == ADMINUSER) {
			header('Location:adminMain.php');
		} else {
			header('Location:main.php');
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<title>File Uploader - login</title>
	<link rel="stylesheet" href="./resources/bootstrap.min.css">
<!--	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css"> -->
	 <link rel="stylesheet" href="./resources/font-awesome.min.css">
	<!--    <script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>-->
	<!--    <script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>-->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
				document.getElementById("error_message").outerHTML =
					'<div id="error_message" class="alert alert-danger w-50"></div>';
				document.getElementById("username").outerHTML =
					'<input type="text" name="username" id="username"  class="form-control border-danger" placeholder="Username">';
				document.getElementById("error_message").innerHTML = text;
				document.getElementById("username").value = "";
				return false;
			}
			x = document.getElementById("password").value;
			if (!x || 0 === x.length) {
				text = "You must include a password";
				//text = "<div class=\"error\">" + text + "</div>";
				document.getElementById("error_message").outerHTML =
					'<div id="error_message" class="alert alert-danger w-50 "></div>';
				document.getElementById("password").outerHTML =
					'<input type="password" name="password" id="password" class="form-control border-danger" placeholder="Password">';
				document.getElementById("error_message").innerHTML = text;
				document.getElementById("password").value = "";
				return false;
			}

			return true;
		}
	</script>
	<a href="help.html"><button class="btn m-1 btn-outline-primary ">Help</button></a>
	<div class="container-md mt-5 xxw-50 text-center">
		<h2>Welcome to the <b>File Upload Centre</b></h2>
		<div id="error_message"></div>
		<div class="card">

			<div class="card-header bg-info text-white"><b>For ICS class to upload java files</b></div>
			<div class="card-body">
				<p class="">Sign in to start your session</p>

				<form action="<?php echo $_SERVER["PHP_SELF"];?>" method="post" onsubmit="return validateData()">
					<div class="input-group mb-3">
						<input type="text" name="username" id="username" class="form-control" placeholder="Username" autofocus>
						<div class="input-group-append">
							<div class="input-group-text">
								<i class="fa fa-user"></i>
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
						<div class="col-lg-2 col-md-4 col-12 mt-1">
							<button type="submit" name="submit" class="btn btn-primary btn-block">Sign In</button>
						</div>
						<!-- /.col -->
						<div class="col-lg-3 col-md-4 col-12 mt-1">
							<a href="register.php" class="btn btn-outline-primary btn-block">Register a new user</a>
						</div>
						<!-- /.col -->
						<div class="col-lg-3 col-md-4 col-12 mt-1">
							<!-- <a href="help.html">I forgot my password</a> -->
							<a href="help.html" class="btn btn-outline-primary btn-block">I forgot my password</a>
						</div>
						<!-- /.col -->
					</div>
				</form>

			</div>
		</div>
		<?php if ($error_message != "") echo $error_message; ?>
<!-- <div class="alert alert-success">Hi. I still have a few things to mark, and some of you are checking here to see...<br>
I've estimated marks for the final projects and as I've marked them found that it doesn't change your mark.<br> 
I do want to finish marking the final projects and give feedback to you (in case you want it). <br>
I can email you so that you don't have to keep checking here.
 </div>
-->
	</div>


</body>

</html>
