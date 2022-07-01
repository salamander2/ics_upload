<?php
/*
Name: adminOneUser.php

Purpose: show all the files for one user
	The User should be stored in session (or via URL header)

*/
session_start();
require_once('common.php');
$db = connectToDB();

//make sure that this is the admin user!
if ($username != ADMINUSER) {
    header("Location: main.php");
}

$student = $_GET['ID'];
//$_SESSION["studentID"] = $studentID;
//TODO
//if ($student == '') return to adminMain.php

$sql = "SELECT fullname FROM users WHERE username = ?";
if ($stmt = $db->prepare($sql)) {
    $stmt->bind_param("s", $student);
    $stmt->execute();
    $stmt->bind_result($stFullname);
    $stmt->fetch();
    $stmt->close();
} else {
   $message_  = 'Invalid query: ' . mysqli_error(MYSQL_DB) . "\n<br>";
   $message_ .= 'SQL: ' . $sql;
   die($message_); 
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>File Uploader: Admin (<?=$stFullname ?>)</title>
    <link rel="stylesheet" href="./resources/bootstrap.min.css">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="local.css">
	<script src="./resources/jquery.3.4.1.min.js"></script>
	<script src="./resources/bootstrap.4.5.2.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<script>
	var showMarked = true;
	function hideShowMarked() {
		//document.getElementById("results_box").style = "display:inline-block;";
		// alert("hello button 3");
		showMarked = !showMarked;
		if (showMarked) {
			$('.marked').css('display','table-row');
			$('#btnMarked').text('Hide marked work');
		} else {
			$('.marked').css('display','none');
			$('#btnMarked').text('Show marked work');
		}
	}

	</script>
</head>

<body>
    <script>
		function confirmAction() {
			return confirm("Are you sure");
		}

        function updateRow(num) {

            //Create a formdata object
            var formData = new FormData();

            formData.append("fileid", num);
            var name = "mark" + num;
            var val = document.getElementById(name).value;
            formData.append("mark", val);
            var name = "comment" + num;
            var val = document.getElementById(name).value;
            formData.append("comment", val);

            //Warning: You have to use encodeURIComponent() for all names and especially for the values so that possible & contained in the strings do not break the format.

            var xmlhttp = new XMLHttpRequest();
            //Send the proper header information along with the request: DOES NOT WORK!
            //xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            //xmlhttp.setRequestHeader('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=1');
            //xmlhttp.setRequestHeader("Content-length", params.length);
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    //alert(xmlhttp.responsetext);
                    //var txt="";
                    // for (x in XMLHttpRequestUpload) {
                    //for (x in xmlhttp) {
                    //    txt += xmlhttp[x] + " ";
                    //};
                    //alert(txt);
                    // alert(xmlhttp);
					alert("Row " + num + " updated"); 
                    window.location.reload(true);
                }
            }

            xmlhttp.open("POST", "updateMark.php");
            xmlhttp.send(formData);
        }
    </script>


    <div class="container my-2 mx-auto">
        <div class="card bg-secondary pt-3 my-2 py-2">
            <h3 class="text-center text-white">
			<button class="btn float-left btn-success ml-2 shadow" onclick="location.href='adminUserList.php'">Back</button>
			Hello <u><?php echo $fullname?></u> 
			<button class="btn float-right btn-warning mr-2 shadow" onclick="location.href='logout.php'">Logout</button>
            </h3>
            <div class="card mx-4 my-2 pt-2 bg-primary text-center text-white">
                <h3>Files Control Panel</h3>
            </div>
        </div>

            <h2 class="text-center">All files for <?php echo $stFullname; ?> </h2>
</div>
<div class="container-fluid">
            <div id="error_message"></div>

<button id="btnMarked" class="btn btn-outline-warning float-right" type="button" onclick="hideShowMarked()">Hide marked work</button></h2>
            <table class="table table-bordered">
                <tr>
                    <th>Filename with path</th>
                    <th><span class="purp">Date Marked</span>/Uploaded</th>
                    <th></th>
                    <!-- <th>Comments</th> 
					THis does not make the column wide enough ...-->
					<th style="overflow:hidden;">Comments . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . .</th>
                    <th>Mark</th>
                    <th>&nbsp;<th>
                </tr>

                <?php
//$sql = "SELECT id, username, path, filename, time, comment, mark FROM fileinfo ORDER BY time DESC";
$sql = "SELECT id, path, filename, timeMarked, timeUploaded, comment, mark FROM fileinfo WHERE username='$student' ORDER BY timeMarked DESC;";
$result = mysqli_query($db,$sql);
//TODO Fix the next line! Why is it here?
#$stmt->execute();

while($row = $result->fetch_assoc()) {
    $id = $row['id'];
    //$user = $row['username'];
    $path = $row['path'];
    $filename = $row['filename'];
    $timeMK = $row['timeMarked'];
    $timeUP = $row['timeUploaded'];
    //$timeUP = explode(" ",$timeUP)[0];
    $comment = stripslashes($row['comment']);
    $mark = $row['mark'];
    
	if ($mark != "") {
		echo "<tr class=\"marked\">";
	} else {
		echo "<tr>";
	}
    echo "<td>$path/$filename</td>";
    echo "<td><span class='purp'>$timeMK</span><br>$timeUP</td>".PHP_EOL;
    echo "<td>";
    echo "<form class='d-inline' method='post' action='download.php'><input name='id' value='$id' hidden><button class='btn btn-info shadow'>Download</button></form> &nbsp; ".PHP_EOL;
    echo "<form class='d-inline' method='post' action='delete.php' onsubmit=\"return confirmAction()\"> <input name='id' value='$id' style='outline: none;' hidden><input name='student' value='$student' style='outline: none;' hidden> <button class='btn btn-danger shadow'>Delete</button></form></td>".PHP_EOL;
    echo '<td style="color:black;"><textarea class="shaded" id="comment'.$id.'" rows="1" style="width:100%">'.$comment.'</textarea></td>'.PHP_EOL;
    echo '<td style="color:black;"><input class="shaded" id="mark'.$id.'" type="text" size="4" value="'.$mark.'"></td>'.PHP_EOL;
    echo "<td><button type=\"submit\" onclick=\"updateRow(".$id.")\">Update</button></td>".PHP_EOL;
    echo "</tr>".PHP_EOL;
}
?>
            </table>

    </div> <!-- end container -->
</body>

</html>
