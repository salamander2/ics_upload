<?php
    session_start();
    require_once('common.php');
    $db = connectToDB();

    //make sure that this is the admin user!
    if ($username != ADMINUSER) {
        header("Location: main.php");
    }

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
    <div class="container my-2">
        <div class="card bg-secondary pt-3 my-2 py-2">
            <h3 class="text-center text-white">Hello <?php echo $fullname?> <button class="btn float-right btn-warning mr-2 shadow" onclick="location.href='logout.php'">Logout</button></h3>
            <div class="card mx-4 my-2 pt-2 bg-primary text-center text-white">
            <h3>Files Control Panel</h3>
            </div>
        </div>
        <div align="center">
            <h2>Users</h2>
            <table class="table table-bordered">
                <tr>
                    <th>User name</th>
                    <th>Full name</th>
                    <th>TotalFiles</th>
                    <th>Delete</th>
                </tr>
                <?php
                
        foreach ($response as $item){
            $user = $item[0];
            $fullname = $item[1];

            if ($user == ADMINUSER) continue;
            $sql = "SELECT COUNT(filename) FROM fileinfo WHERE username = ?";
            if ($stmt = $db->prepare($sql)) {
                $stmt->bind_param("s", $user);
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
            echo "<td>$user</td>";
            echo "<td>$fullname</td>";
            echo "<td>$count</td>";
            //TODO add in a confirm!
	        echo "<td><form method='post' action='adminDeleteUser.php'><input name='user' value='$user' style='outline: none;' hidden><button>Delete</button></form></td>";
	        echo "</tr>";
        }
        ?>
            </table>

            <div id="error_message"></div>
            <H2>Uploaded Files</H2>
            <table class="table table-bordered">
                <tr>
                    <th>Username</th>
                    <th>Filename with path</th>
                    <th>Date</th>
                    <th></th>
                    <th>Comments</th>
                    <th>Marked?</th>
                </tr>

                <?php
        $sql = "SELECT id, username, path, filename, time, comment, mark from fileinfo order by username";
        $result = mysqli_query($db,$sql);
        $stmt->execute();
        while($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $user = $row['username'];
            $path = $row['path'];
            $filename = $row['filename'];
            $time = $row['time'];
            $comment = $row['comment'];
            $mark = $row['mark'];

            echo "<tr>";
            echo "<td>$user</td>";
            echo "<td>$path/$filename</td>";
            echo "<td>$time</td>";
            echo "<td>";
			echo "<form class='d-inline' method='post' action='download.php'><input name='id' value='$id' hidden><button class='btn btn-info shadow'>Download</button></form> &nbsp; ";
			echo "<form class='d-inline' method='post' action='delete.php' onsubmit=\"return confirmAction()\"> <input name='id' value='$id' style='outline: none;' hidden><button class='btn btn-danger shadow'>Delete</button></form></td>";
            echo "<td>$comment <form method='post' action='comment.php'><input name='fe' hidden><button>Edit</button></form></td>";
            echo "<td></td>";
            echo "</tr>";
        }
        ?>
            </table>

        </div>
    </div>
</body>

</html>