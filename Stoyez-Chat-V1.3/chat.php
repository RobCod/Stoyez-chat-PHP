<?php
	include ('includes/settings.php');
	include ('includes/dbh.inc.php');
	
	session_start();
	
	echo 	'<title>'.$chatName. ' - Chat </title>';

	if(isset($_SESSION['username'])) {
		$username = $_SESSION['username'];
		
		
		//check if kicked or banned
		
		$sql = "SELECT * FROM members WHERE username=?";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
				header("Location: ../login.php?error=sqlError");
				exit();
		} else {
			mysqli_stmt_bind_param($stmt, "s", $username);
			mysqli_stmt_execute($stmt);
			$result = mysqli_stmt_get_result($stmt);
			
			if($row = mysqli_fetch_assoc($result)) {
				if($row['level'] == "0") {
					echo "<style>body {background-color: black}a {color:black;text-decoration:none;}</style>";
					echo "<body>";
						echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;margin-top:20%;text-align: center;'>
							 Sorry $username, but you have been perm banned from $chatName</p>";
					echo "</body>";
					include('includes/logout.inc.php');
					header("Refresh: 10;URL=login.php");
					exit();
				} else {
					$timeoutSec = $row['timeout'] * 60;
					$curTime = strtotime(date('g:i:s'));
					$kickTime = strtotime($row['time_kicked']);
					if((int)(($kickTime - $curTime  + $timeoutSec)  / 60) > 0  && $row['time_kicked'] != null) {
						echo "<style>body {background-color: black}</style>";
						echo "<body>";
							echo "<p style='color: red;font-weight:bold; text-align:center;margin-top:20%;'> You have ";
							
							echo (int)((($kickTime - $curTime ) + $timeoutSec))  . " seconds left until your kick ends</p>";
							echo "<p style='color:red;font-size:11px;font-weight:bold;text-align:center;margin:auto 0;'>(Page reloads every 10 seconds automatically)</p>";
							header("Refresh: 10");
						echo "</body>";
					} else {
					
					}
				}
			}
		}
		
		
		
		//check account level for access perms
		$chat_access = mysqli_query($conn, "SELECT * from settings;");
		
		if($settings = mysqli_fetch_assoc($chat_access)) {
			if($_SESSION['level'] == "1" && $settings['chat_access_level'] == 0) {
			
			} else if($_SESSION['level'] == "1" && $settings['chat_access_level'] == 1) {
				date_default_timezone_set($timeZone);
				$duration = 50;
				$startTime = $_SESSION['waiting_room_start'];
				$curTime = strtotime(date('g:i:s'));
				$totalTimeElapsed = (($startTime - $curTime) + $duration);
					
				header("Refresh: 20");
				if($totalTimeElapsed > 0) {
					echo "<style>body {background-color: black;text-align:center;}
					body p {background-color: black;text-align:center;font-size:15px;}
					body a {background-color: black;color:white;text-align:center;font-size:15px;}</style>";
				
					echo "<body>";
						echo "<h2><font color='white'>Waiting room</font></h2>";
						echo "<p><font color='white'>Welcome <font color='red'>" . $_SESSION['username'] . " </font>, your login has been delayed, you can access the chat in ".$totalTimeElapsed." seconds.</p></font>";
						echo "<p><font color='white'>If this page doesn't refresh every 20 seconds, use the button below to reload it manually!</p></font>";
						echo "<a href='includes/logout.inc.php'>Exit Chat</a>";   
					echo "</body>";
				} else {
					
				}
					
				
			} else if($_SESSION['level'] == "1" && $settings['chat_access_level'] == 2) {
				echo "<font color='red'>mod approval</font>";
			}  else if($_SESSION['level'] == "2" && $settings['chat_access_level'] >=4) {
				include("includes/logout.inc.php");
				header("Location: login.php");
				exit();
			}
		}
	} else {
		header("Location: login.php");
	}
?>

<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/chat_stylesheet.css" />
	<meta charset="UTF-8" http-equiv="refresh" content="900;url=chat.php">
</head>
<frameset rows="100px, *, 62px" framespacing="3" border="3" frameborder="3" >
	<frame src="post.php" name="post" scrolling="no" >
	<frame src="view.php" name="middle" >
	<frame src="controls.php" name="controls" scrolling="no" >
</frameset>
</html>