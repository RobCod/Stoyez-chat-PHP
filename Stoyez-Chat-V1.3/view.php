<?php
	include('includes/settings.php');
	include('includes/dbh.inc.php');
	
	session_start();

	if ($_SESSION['username'] == null) {
		header('Location: login.php');
	} 

	if(isset($_POST['refresh'])) {
		header("Refresh: 0");
	}
	//checks if users online status equals 0 if it does then log them out and show kick message
	$sql = "SELECT * FROM members WHERE username=?;";
	$stmt = mysqli_stmt_init($conn);
	if(!mysqli_stmt_prepare($stmt, $sql)) {
		header("Location: login.php?error=sqlError");
		exit();
	} else {
		mysqli_stmt_bind_param($stmt, "s", $_SESSION['username']);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		if($row = mysqli_fetch_assoc($result)) {
			if($row['online'] == 0) {
				$kickMsg = $row['kick_msg'];
				$timeout = $row['timeout'];
				$timeKicked = $row['time_kicked'];
				include('includes/logout.inc.php');
				
				session_start();
				
				$_SESSION['kick-msg'] = $kickMsg;
				$_SESSION['timeout'] = $timeout;
				$_SESSION['time-kicked'] = $timeKicked;
				
				header("Location: login.php?message=kicked");
				exit();
			}
		} else {
			header("Location: login.php?error=sqlError");
			exit();
		}
	}
	
	$username = $_SESSION['username'];
	
	//CHAT OUTPUT
	$result = $conn->query("SELECT * FROM messages ORDER BY id DESC") or die($conn->error);

	
	//CHAT REFRESH Time;
	$refreshRate = $conn->query("SELECT refreshrate FROM settings");
	
	$refresh_rate = $refreshRate->fetch_assoc();
	
	$refresh = (int)$refresh_rate['refreshrate'];
?>
<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/view_stylesheet.css" />
	<?php echo '<meta charset="UTF-8" http-equiv="refresh" content=' .$refresh. ';url=view.php>'; ?>
	<?php echo 	'<title>'.$chatName?>
	<?php echo " - View </title>"; ?>	
</head>
<body>
	<div class="view">
		<table>
			<?php
				$sql = mysqli_query($conn, "SELECT topic FROM settings");
				$row = mysqli_fetch_assoc($sql);
				echo "<p><font color='red' style='font-weight:bold; font-size:15px;margin-left:5px;'>" . $row['topic'] . "</font> </p>";
				
				
				while($row = mysqli_fetch_array($result)){   //Creates a loop to loop through chat results
					if($_SESSION['level'] >= 0 && $row['recipient'] == 1 && $row['delstatus'] == 0) {
						echo "<tr><td><span class='usermsg'> <span style='color:#ffffff;font-size: 14px;'>" . $row['postdate'] . " " . " - " . $row['poster'] . " " . " - " . $row['message'] . "</span><span style='color:#ffffff;font-size: 14px;'></span></span>" . "</td>";
					} 
					if ($_SESSION['level'] >= 2 && $row['recipient'] == 2 && $row['delstatus'] == 0) {
						echo "<tr><td><span class='usermsg'> <span style='color:#ffffff;font-size: 14px;'>" . $row['postdate'] . " " . " - " . "<b> [M]</b> " . $row['poster'] . " " . " - " . $row['message'] . "</span><span style='color:#ffffff;font-size: 14px;'></span></span>" . "</td>";
					//$textFancy = "<span class='usermsg'>[M] <span style='color:#ffffff;'>".$username."</span> - <span style='color:#ffffff;'>" .$textCleaned. "</span></span>";
					}
					if ($_SESSION['level'] >= 3 && $row['recipient'] == 3 && $row['delstatus'] == 0) {
						echo "<tr><td><span class='usermsg'> <span style='color:#ffffff;font-size: 14px;'>" . $row['postdate'] . " " . " - " . "<b> [STAFF]</b> " . $row['poster'] . " " . " - " . $row['message'] . "</span><span style='color:#ffffff;font-size: 14px;''></span></span>" . "</td>";
					}
					if ($_SESSION['level'] >= 4 && $row['recipient'] == 4 && $row['delstatus'] == 0) {
						echo "<tr><td><span class='usermsg'> <span style='color:#ffffff;font-size: 14px;'>" . $row['postdate'] . " " . " - " . "<b> [ADMIN]</b> " . $row['poster'] . " " . " - " . $row['message'] . "</span><span style='color:#ffffff;font-size: 14px;''></span></span>" . "</td>";
					}
						
				}
			?>
		</table>
	</div>
</body>
</html>