<?php
include ('includes/settings.php');
include ('includes/version.php');
include ('includes/dbh.inc.php');

session_start();

	//Get Names from settings.php
	echo "<title>" .$chatName . " - Login </title>";
	echo "<body>" . "<div class=\"banner\">" . $chatName . "</div>";
	echo "<div class=\"footer\">" . "<p>".$version."</p></div>";

	if(!isset($_SESSION)) {
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
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
</head>
<body>
	<div class="login">
	<h2>Login</h2>
	
	<?php
		$sql = $conn->query("SELECT * FROM settings");
					
		while($row =  mysqli_fetch_array($sql)){
			$access = $row['chat_access_level'];
			$chat_disabled = $row['closed_message'];
						
			if($access == '1') {
				echo '<p class="signuperror">This chat has a waitingroom for guests upon logging in.</p>';
			} else if($access == '2') {
				echo '<p class="signuperror">This chat requires a mod to accept guests upon logging in.</p>';
			} else if($access == '3') {
				echo '<p class="signuperror">This chat is currently in Member only mode.</p>';
			} else if($access == '4') {
				echo '<p class="signuperror">This chat is currently in Maintanence mode.</p>';
			} else if($access == '5') {
				echo "$chat_disabled";
			}
		}
		
		if(isset($_GET['error'])){
			if($_GET['error'] == "emptyFields"){
				echo '<p class="signuperror">Fill in all fields!</p>';
			} else if($_GET['error'] == "sqlError") {
				echo '<p class="signuperror">There was an SQL error, please contact an administrator.</p>';
			} else if($_GET['error'] == "wrongPassword") {
				echo '<p class="signuperror">You entered the incorrect password.</p>';
			} else if($_GET['error'] == "noUsername") {
				echo '<p class="signuperror">The username you entered does not exist.</p>';
			}
		} else if(isset($_GET['message'])){
			if($_GET['message'] == "kicked"){
				$kickedMsg = $_SESSION['kick-msg'];
				$timeout = $_SESSION['timeout'];
				$timeKicked = $_SESSION['time-kicked'];
				
				if($kickedMsg == null) {
					echo "<p class='signuperror'>You've been kicked, please refresh the page and login again.</p>";
				} else {
					session_unset();
					session_destroy();
					
					echo "<p class='signuperror'>$kickedMsg for $timeout minute(s), please refresh the page and login again.</p>";
				}
			}
		}
	?>
		<form action="includes/login.inc.php" method="post">
			<label for="username">
				<i class="fas fa-user"></i>
			</label>
			<input type="text" name="username" placeholder="Username" id="username" required>
			<label for="password">
				<i class="fas fa-lock"></i>
			</label>
			<input type="password" name="password" placeholder="Password" id="password" required>
			<input type="submit" name="submit-login" value="Login">
		</form>
	</div>
</body>
</html>
