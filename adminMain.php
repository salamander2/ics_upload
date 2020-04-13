<?php

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Classroom: Admin</title>
    <link rel="stylesheet" href="./resources/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
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
        $sql = "SELECT username from users";
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
        <hr />
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