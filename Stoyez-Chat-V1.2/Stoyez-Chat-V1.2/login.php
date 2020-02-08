<?php
include ('settings.php');
include ('version.php');

session_start();

	//Get Names from settings.php
	echo "<title>" .$chatname . " - Login </title>";
	echo "<body>" . "<div class=\"banner\">" . $chatname . "</div>";
	echo "<div class=\"footer\">" . "<p>".$version."</p></div>";

	//Start mysql connection
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname) or die($mysqli->error);
	
	if(isset ($_SESSION['username']) && isset ($_SESSION['password'])) {
		header('Location: chat.php');
	}

	if(isset($_POST['login'])) {
		
		$_SESSION['last_time'] = time();
		
		require 'login_functions.php';
	}
	
	if($setup_ran == '0') {
		header('Location: setup.php');
	}
?>

<html>
<head>
<link rel = "stylesheet" type = "text/css" href = "css/login_stylesheet.css" />
</head>
	<div class="section_header">
		<hr>
	</div>
	<form action="login.php" method="post" target="_self">
		<div class="setup_container">
 			<div class="login_form">
				<h2>Login<h2><br>
				<?php 
					$sql = $mysqli->query("SELECT * FROM settings");
					
					while($row =  mysqli_fetch_array($sql)){
						$access = $row['access'];
						$chat_disabled = $row['disabletext'];
						
						if($access == '2') {
							echo "This Chat is currently in Member only mode.";
						} else if($access == '3') {
							echo "This Chat is currently in Mod only mode.";
						} else if($access == '4') {
							echo "This Chat is currently in Admin only mode.";
						}
					}
				?>
					<label for="username"><b>Username : </b></label>
					<input type="text" placeholder="Enter Username" name="username" id="username" required>
					<br><label for="password"><b>Password: </b></label>
					<input type="password" placeholder="Enter Password" name="password" id="password" required>
					<br><br><button type="submit" name="login" id="login">Login</button>
			</div>
 		<div>
	</form>
	<div class="section_footer">
		<hr>
	</div>
</body>
</html>
