<?php
 session_start();
 include('settings.php');
 
 $mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname);
 $stmt = mysqli_stmt_init($mysqli);
 
?>

<html>
<head>
	<?php echo 	"<title>".$chatname. " - Error </title>"; ?>
	<link rel = "stylesheet" type = "text/css" href = "css/error_stylesheet.css" />
</head>
<body>
	<?php
	$errorMessage = (String)$_SESSION['message'];
	
	if($errorMessage == '<p>Error: Invalid/expired session</p>') {
	//Database delete user if users rank is below 2.
	$username = $_SESSION['username'];

	$sql = "UPDATE members SET status=? WHERE nickname=?";
		
	$status = 0;
		
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo "SQL statment failed";
		} else {
			//Bind parameters to the placeholder
			mysqli_stmt_bind_param($stmt, "is", $status, $username);
			//Run params in the database
			mysqli_stmt_execute($stmt);
				
			//Check User level to see if its too high for deletion
			$levelMysql = "SELECT * FROM members WHERE nickname=?";
				
			if(!mysqli_stmt_prepare($stmt, $levelMysql)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "s", $username);
				//Run params in the database
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				$levelRaw = $result->fetch_assoc();
			
				$userLevel = (int)$levelRaw['level'];
			}
		
			if($userLevel >= 2) {
				echo $_SESSION['message'];
				session_destroy();
				echo "<div class='my_content_container'><a href='login.php'>Return to Login</a></div>";				
			}else if ($userLevel == 1){
				$delete = "DELETE FROM members WHERE nickname=?";
				
				if(!mysqli_stmt_prepare($stmt, $delete)) {
					echo "SQL statment failed";
				} else {
					//Bind parameters to the placeholder
					mysqli_stmt_bind_param($stmt, "s", $username);
					//Run params in the database
					mysqli_stmt_execute($stmt);
					echo $_SESSION['message'];
					session_destroy();
					echo "<div class='my_content_container'><a href='login.php'>Return to Login</a></div>";		
				}
			}
			mysqli_close($mysqli);
		}
	} else {
		if(isset($_SESSION['message']) AND !empty($_SESSION['message'])){
			echo $_SESSION['message'];
			session_destroy();
			echo "<div class='my_content_container'><a href='login.php'>Return to Login</a></div>";
		} else {
			header('Location: chat.php');
		}
	}
	?>
</body>
</html>

