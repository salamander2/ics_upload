<?php
session_start();
require_once "common.php";

//Check if logged in. If not, redirect to index.php 
if (!isset($username) || empty($username)) header("Location: logout.php");
if ($_SESSION["authkey"] != AUTHKEY) { 
    header("Location:index.php?ERROR=Failed%20Auth%20Key"); 
}  

$db = connectToDB();
$error_message = "";

//Find total number of programs in database
$sql = "SELECT COUNT(*) AS total FROM fileinfo";
$result = mysqli_query($db,$sql);
if (!$result) {
   die("Query to count programs in 'fileinfo' failed");
}
$totalNum = $result->fetch_row()[0];

//Find number of unmarked programs in database
$sql = "SELECT COUNT(*) AS total FROM fileinfo WHERE mark IS NULL";
$result = mysqli_query($db,$sql);
if (!$result) {
   die("Query to count unmarked programs in 'fileinfo' failed");
}
$totalUnmarked = $result->fetch_row()[0];

$sql = "SELECT id,filename,path,timeUploaded,timeMarked,comment,mark FROM fileinfo WHERE username = ? ORDER by timeUploaded DESC";
if ($stmt = $db->prepare($sql)) {
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	//$stmt->fetch();
	$stmt->close();
} else {
	$message_  = 'Invalid query: ' . mysqli_error($db) . "\n<br>";
	$message_ .= 'SQL: ' . $sql;
	die($message_);
}

$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

/* Find all (top level) folders for this user */
$dir = "./files/$username/";
//remove . and .. from directory listings
$scanned_directory = array_diff(scandir($dir), array('..', '.'));
$folders = array();
foreach($scanned_directory as $file)
{
	if (is_dir($dir.$file)) $folders[]=$file;
}

?>

<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>File Uploader : <?= $username?></title>
	<link rel="stylesheet" href="./resources/bootstrap.min.css">
    <link rel="stylesheet" href="local.css">
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
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script>
		function confirmAction() {
			return confirm("Are you sure");
		}

		function validateFileData() {
			var x, text;
			x = document.getElementById("fileToUpload").value;
			if (!x || 0 === x.length) {
				text = "You must choose a file";
				//text = "<div class=\"error\">" + text + "</div>";
				document.getElementById("error_message").outerHTML =
					'<div id="error_message" class="alert alert-danger w-50"></div>';
				document.getElementById("error_message").innerHTML = text;
				return false;
			}

			/* Now check if it is a duplicate file */
			//I can't make this a separate function as it ends too soon. ajax is not blocking output
/*
			var filename, foldername;
			filename = document.getElementById("fileToUpload").value;
			foldername = document.getElementById("foldername").value;
			console.log("start");

			event.preventDefault();
			$.ajax({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				},
				type: "POST",
				async: true,
				url: 'checkDuplicateFile.php',
				data: {
					filename: filename,
					foldername: foldername
				},
				// timeout:3000,
				dataType: "json",
				success: function (msg,status,xhr) {
					console.log(JSON.stringify(xhr));
					console.log("status:"+msg.status);
					if (msg.status == true ) {
						var x = confirm("This FILE already exists. Do you want to overwrite it?");
						if (x) {
							console.log("perform upload2")
							return true;
						} else {
							return false;
						}
					} else if (msg.status == false) {
						console.log("perform upload1")
						return true;
					} else {
						ajaxResult = "error";
						alert("A strange error has happened. check duplicate files. JSON." + msg);
						return false;
					}
				},
				error: function (xhr) {
					//alert("An error occured: " + xhr.status + " " + xhr.statusText);
					console.log(JSON.stringify(xhr));
					// console.log("ERROR:" + xhr.status + " " + xhr.statusText);
					return false;
				}
			});
*/

			return true;
		}




		function validateFolder() {
			var x;
			x = document.getElementById("folder").value;
			if (!x || 0 === x.length) return false;
			return true;
		}
	</script>
<div class="">
	<a href="help.html">
<button class="btn m-1 btn-outline-primary ">Help</button></a>
<span class="float-right mt-2 mr-5 text-secondary">
♦ Number not marked =  <?=$totalUnmarked?> ♦
Total # of programs = <?=$totalNum?> ♦

</span></div>
	<div class="container-fluid my-2">
		<div class="card Xtext-center bg-secondary my-2 py-2">
			<h3 class="text-center text-white">Hello <?php echo $fullname?>
				<button class="btn float-right btn-warning mr-2 shadow"
					onclick="location.href='logout.php'">Logout</button></h3>

			<form action="upload.php" method="post" enctype="multipart/form-data" >
				<div class="row mx-2">
					<div class="col-md-4 overflow-hidden">
						<input class="btn btn-primary shadow pb-1" type="file" name="fileToUpload" id="fileToUpload">
					</div>
					<div class="col-md-4 btn btn-outline-info text-white pb-0">
						<label for="foldername">Select folder: </label>
						<select id="foldername" name="foldername">
							<option value="none" selected="selected">none</option>
							<?php
							foreach($folders as $f) {
								echo '<option value="'.$f.'">'.$f.'</option>';
							}
							?>
						</select>
						<!-- <input type="text" name="username" id="username" class="pb-2" placeholder="Save in folder (name)"> -->
					</div>
					<div class="col-md-4">
						<input class="btn btn-success shadow pb-2" type="submit" value="Upload chosen file"
							name="submit" onclick="return validateFileData();"> 
					</div>
				</div>
			</form>
		</div>
		<div id="error_message"></div>
		<div class="text-secondary">Uploaded files
<button id="btnMarked" class="btn btn-outline-warning mb-1 float-right" type="button" onclick="hideShowMarked()">Hide marked work</button></h2>

</div>
		<table class="table table-bordered">
			<tr>
				<th>FileName</th>
				<th>Folder</th>
				<th>Date Uploaded<br><span class="purp">Marked</span></th>
				<th></th>
				<th class="commentW">Comments</th>
				<th>Mark</th>
			</tr>

			<?php

			//filename,path,time,comment,mark
			foreach ($data as $row){
/*
				$id = $item[0];
				$filename = $item[1];
				$path = $item[2];
				$time = $item[3];
				$comment = stripslashes($item[4]);
				$mark = $item[5];
*/
				$id = $row['id'];
				$filename = $row['filename'];
				$path = $row['path'];
				$timeMK = $row['timeMarked'];
				$timeUP = $row['timeUploaded'];
				$comment = stripslashes($row['comment']);
				$mark = $row['mark'];
				#marked:
				if ($mark != "") {
					echo "<tr class=\"marked\">";
					echo "<td>$filename</td>";
					echo "<td>$path</td>";
					echo "<td>$timeUP<br><span class='purp'>$timeMK</span></td>";
					echo "<td colspan=2>";
					echo "<form class='d-inline' method='post' action='download.php'><input name='id' value='$id' hidden>";
					echo "<button class='btn btn-info shadow smallbtn sml'>Download</button>";
					echo "</form> &nbsp; ";
					echo "<form class='d-inline' method='post' action='delete.php' onsubmit=\"return confirmAction()\"> <input name='id' value='$id' style='outline: none;' hidden>";
					echo "<button class='btn btn-danger shadow smallbtn smr'>Delete</button>";
					echo "</form></td>";
					#echo '<td><textarea readonly rows="2" style="width:100%">'.$comment.'</textarea></td>';
					echo "<td>$mark</td>";
					echo "</tr>";
					echo "<tr class=\"marked\">";
					echo "<td>&nbsp;</td>";
					echo '<td colspan=4><textarea readonly rows="2" style="width:100%;background-color:#C7C7F4;">'.$comment.'</textarea></td>';
					echo "<td>&nbsp;</td>";
					echo "</tr>";
				
				}

//TODO: smallbtn does nothing. What is smr ??? Use btn-sm
				#notmarked and not comment
				 elseif ($mark == "" && $comment == "") {
					echo "<tr>";
					echo "<td>$filename</td>";
					echo "<td>$path</td>";
					echo "<td>$timeUP</td>";
					echo "<td colspan=2>";
					echo "<form class='d-inline' method='post' action='download.php'><input name='id' value='$id' hidden>";
					echo "<button class='btn btn-info shadow smallbtn sml'>Download</button>";
					echo "</form> &nbsp; ";
					echo "<form class='d-inline' method='post' action='delete.php' onsubmit=\"return confirmAction()\"> <input name='id' value='$id' style='outline: none;' hidden>";
					echo "<button class='btn btn-danger shadow smallbtn smr'>Delete</button>";
					echo "</form></td>";
					#echo '<td><textarea readonly rows="1" style="width:100%">'.$comment.'</textarea></td>';
					echo "<td>$mark</td>";
					echo "</tr>";
			    #not marked, but has comment
				} else {
					echo "<tr>";
					echo "<td>$filename</td>";
					echo "<td>$path</td>";
					echo "<td>$time</td>";
					echo "<td>";
					echo "<form class='d-inline' method='post' action='download.php'><input name='id' value='$id' hidden>";
					echo "<button class='btn btn-info shadow smallbtn sml'>Download</button>";
					echo "</form> &nbsp; ";
					echo "<form class='d-inline' method='post' action='delete.php' onsubmit=\"return confirmAction()\"> <input name='id' value='$id' style='outline: none;' hidden>";
					echo "<button class='btn btn-danger shadow smallbtn smr'>Delete</button>";
					echo "</form></td>";
					echo '<td><textarea readonly rows="1" style="width:100%">'.$comment.'</textarea></td>';
					echo "<td>$mark</td>";
					echo "</tr>";
				}
				
			}

			?>
		</table>

		<div class="divider py-1 mb-3 bg-info rounded"></div>
		<div class="row">
			<div class="col-md-6">
				<div class="text-secondary">Folders</div>
				<table class="table table-bordered">
					<tr>
						<td>
							<?php 
					if (count($folders) == 0) echo "-- none --";
					foreach ($folders as $f) {
						echo "&bull; ".$f."<br>";
					}
					?>
						</td>
					</tr>
				</table>

			</div>


			<div class="col-md-6">
				<div class="card border-success">
					<div class="card-header">Checklist for Java programs before uploading</div>
					<div class="card-body">
						<div class="card-text">
							<ul type="square">
								<li>comment at top with your name, date and purpose of program
								<li>class names are uppercase
								<li>variable names are lowercase
								<li>method names are lower case
								<li>program is indented correctly
							</ul>
						</div>
					</div>
				</div>

			</div>
		</div>
		<div id="folderForm" class="d-none">
			<form method='post' action='createFolder.php' onsubmit="return validateFolder()">
				<div class="row input-group ml-0">
					<input type="text" name="folder" id="folder" class="col-4 form-control"
						placeholder="Enter folder name">
					<input type="submit" class="btn btn-outline-success mr-2 shadow" value="Create folder">
				</div>
			</form>
		</div>
		<button id="folderBtn" class="btn btn-outline-secondary mr-2 shadow" onclick="displayForm()">Create
			folder</button>
		<p class="text-success">Only create a folder if you have a project with many files</p>
	</div>
	<script>
		function displayForm() {
			document.getElementById("folderBtn").outerHTML = '<div id="folderBtn" class="d-none"></div>';
			document.getElementById("folderForm").classList.remove('d-none');
			document.getElementById("folderForm").classList.add('d-block');
		}
	</script>
</body>

</html>
