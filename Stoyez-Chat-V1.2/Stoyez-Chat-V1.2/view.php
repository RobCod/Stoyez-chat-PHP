<?php
	session_start();

	include('settings.php');

	if ($_SESSION['username'] == null) {
		header('Location: login.php');
	} 

	if(isset($_POST['refresh'])) {
		header("Refresh: 0");
	}
	//establish database connection
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname);
	$stmt = mysqli_stmt_init($mysqli);
	
	//CHAT OUTPUT
	$result = $mysqli->query("SELECT * FROM messages ORDER BY id DESC") or die($mysqli->error);

	
	//CHAT REFRESH Time;
	$refreshRate = $mysqli->query("SELECT refreshrate FROM settings");
	
	$refresh_rate = $refreshRate->fetch_assoc();
	
	$refresh = (int)$refresh_rate['refreshrate'];
	
	
	//Levels
	$sql = $mysqli->query("SELECT * FROM members");
	
	//FIND LEVEL FOR CHAT VIEW
	
	$username = $_SESSION['username'];
	
	$getLevel = "SELECT * FROM members WHERE nickname=?";
	
	if(!mysqli_stmt_prepare($stmt, $getLevel)) {
		echo "SQL statment failed";
	} else {
		//Bind parameters to the placeholder
		mysqli_stmt_bind_param($stmt, "s", $username);
		//Run params in the database
		mysqli_stmt_execute($stmt);
		$result2 = mysqli_stmt_get_result($stmt);
		
		$level = $result2->fetch_assoc();
		
		$userLevel = (int)$level['level'];
		mysqli_close($mysqli);
	}
	
	
?>
<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/view_stylesheet.css" />
	<?php echo '<meta charset="UTF-8" http-equiv="refresh" content=' .$refresh. ';url=view.php>'; ?>
	<?php echo 	'<title>'.$chatname?>
	<?php echo " - View </title>"; ?>	
</head>
<body>
	<div class="view">
		<table>
			<?php
				echo "<p>Online: ";
				while($row = mysqli_fetch_array($sql)){   //Creates a loop to loop through results
					$online_name = (string)$row['nickname'];
					$online_status = (string)$row['status'];
					if($online_status == '1') {					
						echo "<font color='red'>" . $row['nickname'] . ", ". "</font> "; 
					}
				}
				echo "</p>";
				
			
				if($userLevel >= 1) {
					while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through results
						if($userLevel >= 0 && $row['poststatus'] == 1) {
							echo "<tr><td><span class='usermsg'> <span style='color:#ffffff;font-size: 14px;'>" . $row['postdate'] . " " . " - " . $row['poster'] . " " . " - " . $row['text'] . "</span><span style='color:#ffffff;font-size: 14px;'></span></span>" . "</td>";
						} 
						if ($userLevel >= 2 && $row['poststatus'] == 2) {
							echo "<tr><td><span class='usermsg'> <span style='color:#ffffff;font-size: 14px;'>" . $row['postdate'] . " " . " - " . "<b> [M]</b> " . $row['poster'] . " " . " - " . $row['text'] . "</span><span style='color:#ffffff;font-size: 14px;'></span></span>" . "</td>";
						//$textFancy = "<span class='usermsg'>[M] <span style='color:#ffffff;'>".$username."</span> - <span style='color:#ffffff;'>" .$textCleaned. "</span></span>";
						}
						if ($userLevel >= 3 && $row['poststatus'] == 3) {
							echo "<tr><td><span class='usermsg'> <span style='color:#ffffff;font-size: 14px;'>" . $row['postdate'] . " " . " - " . "<b> [STAFF]</b> " . $row['poster'] . " " . " - " . $row['text'] . "</span><span style='color:#ffffff;font-size: 14px;''></span></span>" . "</td>";
						}
						if ($userLevel >= 4 && $row['poststatus'] == 4) {
							echo "<tr><td><span class='usermsg'> <span style='color:#ffffff;font-size: 14px;'>" . $row['postdate'] . " " . " - " . "<b> [ADMIN]</b> " . $row['poster'] . " " . " - " . $row['text'] . "</span><span style='color:#ffffff;font-size: 14px;''></span></span>" . "</td>";
						}
						
					}
				} 


			?>
		</table>
	</div>
</body>
</html>