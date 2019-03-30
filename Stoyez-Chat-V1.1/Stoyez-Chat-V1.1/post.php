<?php	
	include('settings.php');
	
	session_start();
	
	//Get names
	echo "<title>" .$chatname . " - Post </title>";
	
	
	//establish database connection
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname);
	
	$result = $mysqli->query("SELECT * FROM messages") or die($mysqli->error);
	
	$stmt = mysqli_stmt_init($mysqli);
	
	$username = $_SESSION['username'];
	
	if(isset($_POST['submit'])) {
		if(empty($_POST['text'])) {
			header("Refresh: 0");
		} else {
		
			//Additional MSQL injection prevention
			$unfilteredText = $_POST['text'];
			$XSSfilteredText = htmlspecialchars($unfilteredText, ENT_QUOTES, 'UTF-8');
			$textCleaned = $mysqli->escape_string($XSSfilteredText);
			
			$send_to = $_POST['sent_to'];
		
			$date = date("Y-m-d H:i:s");
			
			$sql = "INSERT INTO messages(poster, text, postdate, poststatus) VALUES (?, ?, ?, ?)";
			
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "sssi", $username, $textCleaned, $date, $send_to);
				//Run params in the database
				mysqli_stmt_execute($stmt);
			}
		
			mysqli_close($mysqli);
			$_SESSION['last_time'] = time();
			header('Refresh: 0');
		}
	} 
	
	//Get Account Level 
	$getLevel = "SELECT * FROM members WHERE nickname=?";
	
	if(!mysqli_stmt_prepare($stmt, $getLevel)) {
		echo "SQL statment failed";
	} else {
		//Bind parameters to the placeholder
		mysqli_stmt_bind_param($stmt, "s", $username);
		//Run params in the database
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		$level = $result->fetch_assoc();
		
		$userLevel = (int)$level['level'];
		mysqli_close($mysqli);
	}
?>

<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/view_stylesheet.css" />
	<?php  ?>

</head>
<body>
	<div class="post_parent">
			<form id="textToTalk" action="post.php" method="post" target="_self">
				<div class="post_objects">
					<label for="username"><b><?php echo $username . " :";?></b></label>
					<input type="text" placeholder="" name="text" id="username">
					<button type="submit" name="submit" id="submit">Send To</button>
					<select name="sent_to" style="width: 120px; text-align: center; background-color: black; color: white;">
						<option value="1">-Everyone-</option>
						<?php 
						if($userLevel >= 2) {
							echo "<option value='2'>-Members-</option>";
						}
						?>
						<?php 
						if($userLevel >= 3) {
							echo "<option value='3'>-Mod-</option>";
						}
						?>
						<?php 
						if($userLevel >= 4) {
							echo "<option value='4'>-Admin-</option>";
						}
						?>
					</select> 
				</div>
			</form>
			<?php $date = date("Y-m-d H:i:s"); echo "Current Time: " . $date;?>
	</div>