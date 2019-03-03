<?php
 session_start();
 include('settings.php');
?>

<html>
<head>
	<?php echo 	'<title>'.$chatname?>
	<?php echo " - Error </title>"; ?>
	<link rel = "stylesheet" type = "text/css" href = "css/error_stylesheet.css" />
</head>
<body>
	<?php
	$errorMessage = (String)$_SESSION['message'];
	
	if(isset($_SESSION['message']) AND !empty($_SESSION['message'])) {
		echo $_SESSION['message'];
		session_destroy();
		echo "<div class='my_content_container'><a href='login.php'>Return to Login</a></div>";
	} else {
		header('Location: chat.php');
	}
	
	if($errorMessage == '<p>Error: Invalid/expired session</p>') {
		//Database delete user if users rank is below 2.
		$username = $_SESSION['username'];
		
		$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname);

		$sql = "UPDATE members SET status='0' WHERE nickname='$username'";
		
		$result = "DELETE FROM members WHERE nickname='$username'";
		
		
		//Check User level to see if its too high for deletion
		$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname);
	
		$levelMysql= $mysqli->query("SELECT * FROM members WHERE nickname='$username'") or die($mysqli->error());
		
		$levelRaw = $levelMysql->fetch_assoc();
			
		$userLevel = (int)$levelRaw['level'];
		
		//Run Above mysql querys.
		if($mysqli->query($sql) === TRUE) {
			if($userLevel < 2) {
				if($mysqli->query($result) === TRUE) {
					echo $_SESSION['message'];
					session_destroy();
					echo "<div class='my_content_container'><a href='login.php'>Return to Login</a></div>";			
				}		
			}
		}
	}
	
	?>
</body>
</html>
