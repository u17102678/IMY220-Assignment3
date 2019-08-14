<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	// Your database details might be different
	$mysqli = mysqli_connect("localhost", "root", "", "dbUser");

	$email = isset($_POST["loginName"]) ? $_POST["loginName"] : false;
	$pass = isset($_POST["loginPassw"]) ? $_POST["loginPassw"] : false;	
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 3</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Cadon Gernandt">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if( (($row = mysqli_fetch_array($res))) && (!isset($_POST["submit-files"])) ) {
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='login.php' method='POST' enctype='multipart/form-data'>
									<div class='form-group'>
									<input type='hidden' name='loginName' value='" . $_POST["loginName"] . "' />
									<input type='hidden' name='loginPassw' value='" . $_POST["loginPassw"] . "' />
										<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple='multiple'/><br/>
										<input type='submit' class='btn btn-standard' value='Upload Image' name='submit-files' />
									</div>
						  	</form>";
				} else if (isset($_POST["submit-files"])) {
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='login.php' method='POST' enctype='multipart/form-data'>
									<div class='form-group'>
									<input type='hidden' name='loginName' value='" . $_POST["loginName"] . "' />
									<input type='hidden' name='loginPassw' value='" . $_POST["loginPassw"] . "' />
										<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple='multiple'/><br/>
										<input type='submit' class='btn btn-standard' value='Upload Image' name='submit-files' />
									</div>
						  	</form>";

					$total = count($_FILES['picToUpload']['name']);

					for($i = 0; $i < $total; $i++ ) {
					  $tmpFilePath = $_FILES['picToUpload']['tmp_name'][$i];

					  if ($tmpFilePath != "" && ($_FILES['picToUpload']['type'][$i] == "image/jpeg") && ($_FILES['picToUpload']['size'][$i] < 1000000)) {
					    $newFilePath = "./gallery/" . $_FILES['picToUpload']['name'][$i];
					    if(move_uploaded_file($tmpFilePath, $newFilePath)) {

					      // Select user id
					      $userId = "SELECT user_id FROM tbUsers WHERE email = '$email'";
								$result = $mysqli->query($userId);

								// Insert filename
								while ($row = mysqli_fetch_assoc($result)) {
						      $galleryInsert = "INSERT INTO tbgallery (user_id, filename) VALUES ('{$row["user_id"]}', '{$_FILES['picToUpload']['name'][$i]}');";
						      $mysqli->query($galleryInsert);
						    }

					    }
					  } else {
							echo "invalid file";
						}
					}

					$userId = "SELECT user_id FROM tbUsers WHERE email = '$email'";
					$result = $mysqli->query($userId);

					// Insert filename
					while ($row = mysqli_fetch_assoc($result)) {
			      $galleryQuery = "SELECT filename FROM tbgallery WHERE user_id = '{$row["user_id"]}'";
			      $idResult = $mysqli->query($galleryQuery);

			      if (mysqli_num_rows($idResult) > 0) {
							echo '
								<h2>Image Gallery</h2>
								<div class="row imageGallery">';

							while ($galleryRow = mysqli_fetch_assoc($idResult)) {
								echo '<div class="col-3" style="background-image: url(gallery/' . $galleryRow["filename"] . ')"></div>';
					    }
					    echo '</div>';
					  }
			    }

				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>