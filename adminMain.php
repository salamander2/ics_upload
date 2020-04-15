<?php
    session_start();
    require_once('common.php');
    $db = connectToDB();

    //get all of the users
	$sql = "SELECT username,fullname from users";
    $result=runSimpleQuery($db,$sql);
    $response = mysqli_fetch_all($result);

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
    LOGOUT<br>ADD MARK
    <div align="center" style="padding: 5%">
    <h2>Users</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>User name</th>
                    <th>Full name</th>
                    <th>TotalFiles</th>
                    <th>Delete</th>
                </tr>
                <?php
        //	        print_r($response);
        foreach ($response as $item){
            $username = $item[0];
            $fullname = $item[1];
            //TODO change to PDO / prepared statments
            $sql = "select count(filename) from fileinfo where username = '$username'";
            $result = mysqli_query($db,$sql);
            $response = mysqli_fetch_assoc($result);
            $count = $response["count(filename)"];
            echo "<tr>";
            echo "<td>$username</td>";
            echo "<td>$fullname</td>";
            echo "<td>$count</td>";
	        echo "<td><form method='post' action='delete_users.php'><input name='username' value='$username' style='outline: none;' hidden><button>Delete</button></form></td>";
	        echo "</tr>";
        }
        ?>
                </tbody>
        </table>
        <H2>Uploaded Files</H2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>FileName (with path)</th>
                    <th>Date</th>
                    <th>Download/Delete</th>
                    <th>Comment</th>
                </tr>
                <?php
        $sql = "SELECT username,path,filename,time,comment from fileinfo order by username";
        $result = mysqli_query($db,$sql);
        $response = mysqli_fetch_all($result);
        //	        print_r($response);
        foreach ($response as $item){
            $username = $item[0];
            $path = $item[1];
            $filename = $item[2];
            $time = $item[3];
            $commend = $item[4];
            echo "<tr>";
            echo "<td>$username</td>";
            echo "<td>$path/$filename</td>";
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