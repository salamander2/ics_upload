<?php

/**
 * Created by PhpStorm.
 * User: desmond
 * Date: 2018-05-09
 * Time: 2:39 PM
 */
header("Content-Type: text/html; charset=utf-8");

if (isset($_COOKIE["admin"])){
	if ($_COOKIE["admin"] != "pwd"){
		setcookie('admin', null, -1);
		$url = "login.php";
		echo "<script type='text/javascript'>";
		echo "window.location.href='$url'";
		echo "</script>";
		exit;
	}
}else{
	$url = "login.php";
	echo "<script type='text/javascript'>";
	echo "window.location.href='$url'";
	echo "</script>";
	exit;
}
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
    <h3>Files Control Panel</h3>
</div>
<div align="center" style="padding: 5%">
    <table class="table table-bordered">
        <caption>Users</caption>
        <thead>
        <tr>
            <th>Username</th>
            <th>Password</th>
            <th>TotalFiles</th>
            <th>Delete</th>
        </tr>
        <?php
        require_once "function.php";
        $conn = connectDB();
        $sql = "SELECT username,password from users";
        $result = mysqli_query($conn,$sql);
        $response = mysqli_fetch_all($result);
        //	        print_r($response);
        foreach ($response as $item){
            $username = $item[0];
            $password = $item[1];
            $sql = "select count(filename) from fileinfo where username = '$username'";
            $result = mysqli_query($conn,$sql);
            $response = mysqli_fetch_assoc($result);
            $count = $response["count(filename)"];
            echo "<tr>";
            echo "<td>$username</td>";
            echo "<td>$password</td>";
            echo "<td>$count</td>";
	        echo "<td><form method='post' action='delete_users.php'><input name='username' value='$username' style='outline: none;' hidden><button>Delete</button></form></td>";
	        echo "</tr>";
        }
        ?>
        </tbody>
    </table>
    <hr/>
    <table class="table table-bordered">
        <caption>Uploaded Files</caption>
        <thead>
        <tr>
            <th>Username</th>
            <th>FileName</th>
            <th>Date</th>
            <th>Download</th>
            <th>Delete</th>
            <th>Comment</th>
        </tr>
        <?php
        require_once "function.php";
        $conn = connectDB();
        $sql = "SELECT username,filename,time,commend from fileinfo order by username";
        $result = mysqli_query($conn,$sql);
        $response = mysqli_fetch_all($result);
        //	        print_r($response);
        foreach ($response as $item){
            $username = $item[0];
            $filename = $item[1];
            $time = $item[2];
            $commend = $item[3];
            echo "<tr>";
            echo "<td>$username</td>";
            echo "<td>$filename</td>";
            echo "<td>$time</td>";
            echo "<td><form method='post' action='download.php'><input name='filename' value='$username-$filename' hidden><button>Download</button></form></td>";
            echo "<td><form method='post' action='delete.php'><input name='filename' value='$username-$filename' style='outline: none;' hidden><button>Delete</button></form></td>";
            echo "<td>$comment <form method='post' action='comment.php'><input name='filename' value='$username-$filename' style='outline: none;' hidden><button>Edit</button></form></td>";
            echo "</tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
