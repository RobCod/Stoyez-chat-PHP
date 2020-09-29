<?php
	include('settings.php');
	echo "<title>" .$chatName . " - Logout </title>";
?>
<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "../css/logout_stylesheet.css" />
</head>
<body>
	<?php
		require 'dbh.inc.php';
		
		session_start();	
		$stmt = mysqli_stmt_init($conn);
		
		if(isset($_SESSION) && !isset($_SESSION['username'])) {
			echo "<p>You are already Logged out <br><br> Redirecting to login</p>";
			header('Refresh: 5; ../login.php');
			echo "<div class='my_content_container'>
					<a href='../login.php'>Return to Login</a>
				 </div>";
		} else {
			$username = $_SESSION['username'];
			
			$adjustStatus = "UPDATE members SET online='0' WHERE username=?";
			if(!mysqli_stmt_prepare($stmt, $adjustStatus)) {
				echo "SQL statment failed, try logging out again.";
			} else {
				mysqli_stmt_bind_param($stmt, "s", $username);
				mysqli_stmt_execute($stmt);
			}
			
			$checklevel = "SELECT * FROM members WHERE username=?";
			if(!mysqli_stmt_prepare($stmt, $checklevel)) {
				echo "SQL statment failed";
			} else {
				mysqli_stmt_bind_param($stmt, "s", $username);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if($row = mysqli_fetch_assoc($result)) {
					$_SESSION['level'] = $row['level'];
					if($_SESSION['level'] == 1) {
						$delete = "DELETE FROM members WHERE username=?";
						if(!mysqli_stmt_prepare($stmt, $delete)){
							echo "SQL statment failed";
						} else {
							mysqli_stmt_bind_param($stmt, "s", $username);
							mysqli_stmt_execute($stmt);
							
							echo "<p>Bye " .$_SESSION['username']. ", visit again soon!</p>";
							session_unset();
							session_destroy();
							echo "<div class='my_content_container'><a href='../login.php'>Return to Login</a></div>";	
						}
					} else if($_SESSION['level'] == 0) {
						echo "<p>Sorry " .$_SESSION['username']. ", but you've been perm banned from $chatName...</p>";
						session_unset();
						session_destroy();
						
						echo "<div class='my_content_container'><a href='../login.php'>Return to Login</a></div>";	
					} else if($_SESSION['level'] > 1) {
						echo "<p>Bye " .$_SESSION['username']. ", visit again soon!</p>";
						session_unset();
						session_destroy();
						
						echo "<div class='my_content_container'><a href='../login.php'>Return to Login</a></div>";		
					}
				}
			}
			mysqli_stmt_close($stmt);
			mysqli_close($conn);
		}
	?>
</body>