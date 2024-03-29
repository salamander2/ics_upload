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
if ($_SESSION["authkey"] != AUTHKEY) { 
    header("Location:index.php?ERROR=Failed%20Auth%20Key"); 
}

//select group
$group="O";
if (isset($_GET["GROUP"])) {
  $group=$_GET["GROUP"];
}
if ($group == 'O') $group = '_';  //wildcard

// **** WHY?? THIS IS NEVER USED ****
//get all of the users (students)
$sql = "SELECT username,fullname FROM users ORDER BY fullname";
#if ($group == "A") $sql = "SELECT username,fullname FROM users WHERE code='A' ORDER BY fullname";
#if ($group == "B") $sql = "SELECT username,fullname FROM users WHERE code='B' ORDER BY fullname";
$result=runSimpleQuery($db,$sql);
$response = mysqli_fetch_all($result);

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>File Uploader: Admin</title>
    <link rel="stylesheet" href="./resources/bootstrap.min.css">
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
			//alert (val);
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
            <h3 class="text-center text-white">Hello <u><?php echo $fullname?></u> <button
                    class="btn float-right btn-warning mr-2 shadow" onclick="location.href='logout.php'">Logout</button>
            </h3>
            <div class="card mx-4 my-2 pt-2 bg-primary text-center text-white">
                <h3>Files Control Panel</h3>
            </div>
        </div>

<?php
    $sql = "SELECT COUNT(username) FROM users WHERE code LIKE '$group'";
	$result=runSimpleQuery($db,$sql);
	$numUsers = $result->fetch_row()[0];
?>
		<h5 class="text-center"><a href="adminUserList.php"><button type="button" class="alert alert-success border-success"><?=$numUsers?> Users </button></a></h5>
<hr>
</div>


<div class="container-fluid">
            <div id="error_message"></div>
            <h2 class="text-center">
<div class="btn-group float-left">
<?php
if ($group=="_") {
  echo '<a href=""><button type="button" class="btn btn-secondary">O</button></a>';
  echo '<a href="adminMain.php?GROUP=A"><button type="button" class="btn btn-outline-primary">A</button></a>';
  echo '<a href="adminMain.php?GROUP=B"><button type="button" class="btn btn-outline-success">B</button></a>';
}
if ($group=="A") {
  echo '<a href="adminMain.php?GROUP=O"><button type="button" class="btn btn-outline-secondary">O</button></a>';
  echo '<a href=""><button type="button" class="btn btn-primary">A</button></a>';
  echo '<a href="adminMain.php?GROUP=B"><button type="button" class="btn btn-outline-success">B</button></a>';
}
if ($group=="B") {
  echo '<a href="adminMain.php?GROUP=O"><button type="button" class="btn btn-outline-secondary">O</button></a>';
  echo '<a href="adminMain.php?GROUP=A"><button type="button" class="btn btn-outline-primary">A</button></a>';
  echo '<a href=""><button type="button" class="btn btn-success">B</button></a>';
}
?>
</div>

Uploaded Files

<button id="btnMarked" class="btn btn-outline-warning float-right" type="button" onclick="hideShowMarked()">Hide marked work</button></h2>
            <table class="table table-bordered">
                <tr>
                    <th>Username</th>
                    <th>Filename with path</th>
                    <th>Date Uploaded<br><span class="purp">Marked</span></th>
                    <th></th>
                    <th>Comments</th>
                    <th>Mark</th>
                    <th>&nbsp;</th>
                </tr>

                <?php
$numNotMarked=0;
$numMarked=0;
//$sql = "SELECT id, username, path, filename, time, comment, mark FROM fileinfo ORDER BY time DESC";
//$sql = "SELECT id, users.fullname, users.username, path, filename, timeMarked, timeUploaded, comment, mark FROM fileinfo INNER JOIN users ON fileinfo.username = users.username ORDER BY timeUploaded DESC;";
$sql = "SELECT id, users.fullname, users.username, path, filename, timeMarked, timeUploaded, comment, mark FROM fileinfo INNER JOIN users ON fileinfo.username = users.username WHERE users.code LIKE '$group' ORDER BY timeUploaded DESC;";
$result = mysqli_query($db,$sql);

//$stmt->execute();

while($row = $result->fetch_assoc()) {
    $id = $row['id'];
	//overwriting these next two variables. Is this a problem?
    $studentTemp = $row['username'];
    $stFullname = $row['fullname'];
    $path = $row['path'];
    $filename = $row['filename'];
    $timeMK = $row['timeMarked'];
    //$timeMK = explode(" ",$timeMK)[0];
    $timeUP = $row['timeUploaded'];
    $comment = stripslashes($row['comment']);
    $mark = $row['mark'];
    
	if ($mark != "") {
		echo "<tr class=\"marked\">";
		$numMarked++;
	} else {
		echo "<tr>";
		$numNotMarked++;
	}
    echo "<td>$stFullname</td>";
    echo "<td style=\"max-width:350px;\">$path/ $filename</td>";
	if ($mark != "") {
		echo "<td>$timeUP<br><span class='purp'>$timeMK</span></td>".PHP_EOL;
	} else {
		echo "<td>$timeUP</td>".PHP_EOL;
	}
    echo "<td>";
    echo "<form class='d-inline' method='post' action='download.php'><input name='id' value='$id' hidden><button class='btn btn-info shadow'>Download</button></form> &nbsp; ".PHP_EOL;
    echo "<form class='d-inline' method='post' action='delete.php' onsubmit=\"return confirmAction()\"> <input name='id' value='$id' style='outline: none;' hidden> <input name='student' value='$studentTemp' style='outline: none;' hidden> <button class='btn btn-danger shadow'>Delete</button></form></td>".PHP_EOL;
    echo '<td style="color:black;"><textarea class="shaded" id="comment'.$id.'" rows="1" style="width:100%;">'.$comment.'</textarea></td>'.PHP_EOL;
    echo '<td style="color:black;"><input class="shaded" id="mark'.$id.'" type="text" size="4" value="'.$mark.'"></td>'.PHP_EOL;
    echo "<td><button type=\"submit\" onclick=\"updateRow(".$id.")\">Update</button></td>".PHP_EOL;
    echo "</tr>".PHP_EOL;
}
?>
            </table>
<div class="alert alert-success">Number not marked =  <?=$numNotMarked?> <span class="float-right"> Marked = <?=$numMarked?></div>
    </div> <!-- end container -->
<script>
		hideShowMarked(); //To start with marked work hidden
</script>
</body>
</html>
