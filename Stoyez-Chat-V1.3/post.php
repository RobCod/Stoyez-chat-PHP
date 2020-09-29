<?php	
	include('includes/settings.php');
	include('includes/dbh.inc.php');
	
	session_start();
	
	//Get names
	echo "<title>" .$chatName . " - Post </title>";
	
	$username = $_SESSION['username'];
	
	if(isset($_POST['submit'])) {
		if(empty($_POST['text'])) {
			header("Refresh: 0");
		} else {
			//User variables
			$message = $_POST['text'];
			$recipient = $_POST['sent_to'];
			
			
		
			$date = date("Y-m-d H:i:s");
			
			$sql = "INSERT INTO messages(poster, message, postdate, recipient) VALUES (?, ?, ?, ?)";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				header("Location: chat.php?error=sqlError");
				exit();
			} else {
				mysqli_stmt_bind_param($stmt, "sssi", $username, $message, $date, $recipient);
				mysqli_stmt_execute($stmt);
			}
			header('Refresh: 0');
		}
	}

	if(isset($_POST['reload'])) {
		header("Refresh: 0");
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
						if($_SESSION['level'] >= 2) {
							echo "<option value='2'>-Members-</option>";
						}
						?>
						<?php 
						if($_SESSION['level'] >= 3) {
							echo "<option value='3'>-Mod-</option>";
						}
						?>
						<?php 
						if($_SESSION['level'] >= 4) {
							echo "<option value='4'>-Admin-</option>";
						}
						?>
					</select> 
				</div>
			</form>
			<div class="time">
				<?php 
				date_default_timezone_set($timeZone);
				if($timeFormat=="12hr") {
					$date = date("n/j/Y g:i:s a"); 
					echo "Current Time: " . $date;
				} else if($timeFormat=="24hr") {
					$date = date("n/j/Y H:i:s"); 
					echo "Current Time: " . $date;
				}
				?>
			</div>
			<?php
				echo "<div class='online-list'>";
					echo "<p style='font-weight: bold; font-size: 17px'>Online: </p>";
					$sql = $conn->query("SELECT * FROM members WHERE online=1");
					if (!$sql) {
						printf("Error: %s\n", mysqli_error($conn));
						exit();
					}
					while($row = mysqli_fetch_array($sql)){   //Creates a loop to loop through results
						if($row['incognito'] == 0) {
							if($row['level'] >= 2) {
								echo "<a href=''><font color='red' style='font-weight: bold; font-size: 17px'>" . $row['username'] . "</font> </a>";
							} else {
								echo "<a href=''><font color='grey' style='font-size: 15px'>" . $row['username'] . "</font> </a>";
							}
						}
					}
					echo "</p>";
				echo "</div>";
			?>
	</div>