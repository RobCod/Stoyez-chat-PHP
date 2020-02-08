<?php
	include('settings.php');
	echo "<title>" .$chatname . " - Logout </title>";
?>

<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/logout_stylesheet.css" />
</head>
<body>
	<?php
	include ('settings.php');
	
	session_start();
	
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname);
	$stmt = mysqli_stmt_init($mysqli);
	
	if ( isset($_SESSION) && !isset($_SESSION['username']) ) {
		echo "<p>You are already Logged out <br><br> Redirecting to login</p>";
		header('Refresh: 5; login.php');
		echo "<div class='my_content_container'>
				<a href='login.php'>Return to Login</a>
			</div>";
	} else {
		
		//Database delete user if users rank is below 2.
		$username= $_SESSION['username'];
		$status = 0;

		$adjustStatus = "UPDATE members SET status=? WHERE nickname=?";
		
		if(!mysqli_stmt_prepare($stmt, $adjustStatus)) {
			echo "SQL statment failed";
		} else {
			//Bind parameters to the placeholder
			mysqli_stmt_bind_param($stmt, "is", $status, $username);
			//Run params in the database
			mysqli_stmt_execute($stmt);
			
		}
		
		$delete = "DELETE FROM members WHERE nickname=?";
		
		//Check User level to see if its too high for deletion
	
		$levelMysql= "SELECT * FROM members WHERE nickname=?";
		
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
		
		//Run Above mysql querys.
		if($userLevel < 2) {
			if(!mysqli_stmt_prepare($stmt, $delete)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "s", $username);
				//Run params in the database
				mysqli_stmt_execute($stmt);
			}
			
			echo "<p>Bye " .$_SESSION['username']. ", visit again soon!</p>";
			session_destroy();
			echo "<div class='my_content_container'><a href='login.php'>Return to Login</a></div>";		
			
		} else if($userLevel >= 2) {
			echo "<p>Bye " .$_SESSION['username']. ", visit again soon!</p>";
			session_destroy();
			echo "<div class='my_content_container'><a href='login.php'>Return to Login</a></div>";		
		}
	}
	?>
</body>
</html>