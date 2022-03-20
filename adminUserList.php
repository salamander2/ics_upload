<?php

/*
username and fullname refer to the logged in user (admin)

student and stFullname refer to the student

NOTE: everything here (should) use prepared statements.
      The "Update" button uses AJAX, so it will not time out like everything else. 
	  If this screen is open for hours, you'll still be able to update data!
*/
session_start();
require_once('common.php');
$db = connectToDB();

//make sure that this is the admin user!
if ($username != ADMINUSER) {
    header("Location: main.php");
}

//get all of the users (students)
$sql = "SELECT username,fullname,lastLogin FROM users ORDER BY fullname";
$sql = "SELECT username,fullname,DATE_FORMAT(lastLogin,'%a, %b %e %Y') FROM users ORDER BY fullname";
$result=runSimpleQuery($db,$sql);
$response = mysqli_fetch_all($result);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>File Uploader: Admin - user list</title>
    <link rel="stylesheet" href="./resources/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<script src="./resources/jquery.3.4.1.min.js"></script>
	<script src="./resources/bootstrap.4.5.2.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<body>
    <script>
		function confirmAction(student) {
			return confirm("Delete "+student+". Are you sure");
		}
	
		function gotoUser(student) {
			console.log(student);
			location.href="adminOneUser.php?ID="+student;
		}
    </script>


    <div class="container my-2 mx-auto">
        <div class="card bg-secondary pt-3 my-2 py-2">
            <h3 class="text-center text-white">
			<button class="btn float-left btn-success ml-2 shadow" onclick="location.href='adminMain.php'">Back to Main</button>
			Hello <u><?php echo $fullname?></u> <button
                    class="btn float-right btn-warning mr-2 shadow" onclick="location.href='logout.php'">Logout</button>
            </h3>
            <div class="card mx-4 my-2 pt-2 bg-primary text-center text-white">
                <h3>Users Control Panel</h3>
            </div>
        </div>

<!-- Show All Users -->
		<div id="usertable">
		<table class="table table-bordered table-striped">
			<tr>
				<th>Full name</th>
				<th>User name</th>
				<th>Last Login</th>
				<th>TotalFiles</th>
				<th>Delete</th>
			</tr>
<?php

foreach ($response as $item){
    $student = $item[0];
    $stFullname = $item[1];
    $lastLogin = $item[2];
    
	//skip ADMINUSER's files
    if ($student == ADMINUSER) continue;

    $sql = "SELECT COUNT(filename) FROM fileinfo WHERE username = ?";
    if ($stmt = $db->prepare($sql)) {
        $stmt->bind_param("s", $student);
        $stmt->bind_result($count);
        $stmt->execute();
        $stmt->fetch();
        $stmt->close();
    } else {
        $message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
        $message_ .= 'SQL: ' . $sql;
        die($message_);
    }
    
    echo "<tr>";
    //echo "<td onclick=\"gotoUser(\"$user\")\" >$fullname &bull;</TD>";
    echo "<th onclick=\"gotoUser('".$student."')\" class=\"text-primary\">&bull; $stFullname &bull;</th>";
    echo "<td>$student</td>";
    echo "<td>$lastLogin</td>";
    echo "<td>$count</td>";
    echo "<td><form method='post' onsubmit=\"return confirmAction('$student')\" action='adminDeleteUser.php'><input name='user' value='$student' style='outline: none;' hidden><button>Delete</button></form></td>";
    echo "</tr>".PHP_EOL;
}
?>
		</table>
	</div> 

<hr>
</div>

</body>

</html>
