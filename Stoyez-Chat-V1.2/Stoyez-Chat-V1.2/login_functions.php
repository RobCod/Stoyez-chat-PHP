<?php
	include ('settings.php');
	
	echo	"<link rel = \"stylesheet\" type = \"text/css\" href = \"css/chat_stylesheet.css\" />";
	
	session_start();
	
	//establish database connection
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname) or die($mysqli->error);
	
	// Escape all $_POST variables to protect against SQL & XSS injections
	$unfilteredUsername= $_POST['username'];
	$XSSfilteredUsername = htmlspecialchars($unfilteredUsername, ENT_QUOTES, 'UTF-8');
	$sterilizedUsername = $mysqli->escape_string($XSSfilteredUsername);
	
	//sterilize password;
	$unfilteredPassword= $_POST['password'];
	$XSSfilteredPassword = htmlspecialchars($unfilteredPassword, ENT_QUOTES, 'UTF-8');
	$sterilizedEnteredPassword = $mysqli->escape_string($XSSfilteredPassword);
	
	$_SESSION['username'] = $sterilizedUsername;
	$_SESSION['password'] = $sterilizedEnteredPassword;
	
	//Select the nickname entered by the user
	$sql = "SELECT * FROM members WHERE nickname=?;";
	$get_level = "SELECT level FROM members WHERE nickname=?";
	
	
	$stmt = mysqli_stmt_init($mysqli);

	if(!mysqli_stmt_prepare($stmt, $get_level)) {
		echo "SQL statment failed";
	} else {
		//Bind parameters to the placeholder
		mysqli_stmt_bind_param($stmt, "s", $sterilizedUsername);
		//Run params in the database
		mysqli_stmt_execute($stmt);
		$result2 = mysqli_stmt_get_result($stmt);
		
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo "SQL statment failed";
		} else {
			//Bind parameters to the placeholder
			mysqli_stmt_bind_param($stmt, "s", $sterilizedUsername);
			//Run params in the database
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);

			if($result->num_rows == 0) { //user doesn't exist
				userDoesntExist();
			} else {
				$user = $result->fetch_assoc();
				$userlevel = $result2->fetch_assoc();
				
				$level = (int)$userlevel['level'];
				
				if( password_verify($sterilizedEnteredPassword, $user['passhash'])) {
					$passwordCheck = "UPDATE members SET status=? WHERE nickname=?";
					$status = 1;
					
					if(!mysqli_stmt_prepare($stmt, $passwordCheck)) {
						echo "SQL statment failed";
					} else {
						//Bind parameters to the placeholder
						mysqli_stmt_bind_param($stmt, "is", $status, $sterilizedUsername);
						//Run params in the database
						mysqli_stmt_execute($stmt);
						$result2 = mysqli_stmt_get_result($stmt);
						
						header('Location: chat.php');
					}
					
				} else {
						
					if($level >= 2){
						$_SESSION['message'] = '<p>Error: This nickname is a registered member.<br> Wrong Password!</p>';
						header('Location: error.php');
					} else {
						$_SESSION['message'] = "<p>Error: This Account is being currently being used.</p>";
						header('Location: error.php');
					}

				}
			}
		}
	}
mysqli_close($mysqli);

function userDoesntExist() {
	include ('settings.php');
	
	// Escape all $_POST variables to protect against SQL injections
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname) or die($mysqli->error);
	
	
	$newUnfilteredUsername= $_POST['username'];
	$newXSSfilteredUsername = htmlspecialchars($newUnfilteredUsername, ENT_QUOTES, 'UTF-8');
	$NewSterilizedUsername = $mysqli->escape_string($newXSSfilteredUsername);

	// Escape all $_POST variables to protect against SQL & XSS injections
	$unfilteredPassword= $_POST['password'];
	$XSSfilteredPassword = htmlspecialchars($unfilteredPassword, ENT_QUOTES, 'UTF-8');
	$newSterilizedPassword = $mysqli->escape_string(password_hash($XSSfilteredPassword, PASSWORD_BCRYPT));
	
	$hash = $mysqli->escape_string(md5( rand(0, 1000) ) );
	
	//Connect STMT
	$stmt = mysqli_stmt_init($mysqli);
	
	$selectUser = "SELECT * FROM members WHERE nickname=?";
	
	
	if(!mysqli_stmt_prepare($stmt, $selectUser)) {
		echo "SQL statment failed";
	} else {
		//Bind parameters to the placeholder
		mysqli_stmt_bind_param($stmt, "s", $NewSterilizedUsername);
		//Run params in the database
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		//Check if the password contains special characters, if it does then redirect to an error page
		if(!preg_match("/[^a-z0-9 . $ % & * # @ ]/i", $unfilteredPassword)){
			//Check if the username contains special characters, if it does then redirect to an error page.
			if(!preg_match("/[^a-z0-9]/i", $newUnfilteredUsername) && strlen($newUnfilteredUsername) <= 16) {
				//if the username is not found or already in use
				if ($result->num_rows > 0) {
					$_SESSION['message'] = '<p>Error: That nickname is already in use</p>';
					header("Location: error.php");
				} else {
					$sql = "INSERT INTO members (nickname, passhash, status, regedby) VALUES (?, ?, ?, ?)";
					$status = 1;
					$regedby = "Login";
					
					if(!mysqli_stmt_prepare($stmt, $sql)) {
						echo "SQL statment failed";
					} else {
						//Bind parameters to the placeholder
						mysqli_stmt_bind_param($stmt, "ssis", $NewSterilizedUsername, $newSterilizedPassword, $status, $regedby);
						//Run params in the database
						mysqli_stmt_execute($stmt);
						
						header('Location: chat.php');
					}
				}
			} else {
				$_SESSION['message'] = '<p>Error: Your username contains Special Characters or is larger than 16 characters, please try again only using A-Z, a-z, 0-9</p>';
				header("Location: error.php");
			}
		} else {
			$_SESSION['message'] = '<p>Error: Your password contains Special Characters, please try again only using A-Z, a-z, 0-9, 0-9, @, #, $, %, &, * or .</p>';
			header("Location: error.php");
		}
	}
	mysqli_close($mysqli);
}
?>