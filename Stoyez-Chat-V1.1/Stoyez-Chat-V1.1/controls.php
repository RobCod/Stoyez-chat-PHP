<?php
	session_start();

	include('settings.php');
	include('version.php');
	
	$username = $_SESSION['username'];
	
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname);
	$stmt = mysqli_stmt_init($mysqli);
	
	$sql = "SELECT * FROM members WHERE nickname=?";
	
	if(!mysqli_stmt_prepare($stmt, $sql)) {
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


if ($_SESSION['username'] == null) {
	header('Location: login.php');
} 

?>

<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/view_stylesheet.css" />
	<?php echo 	"<title>".$chatname. " - Controls </title>"; ?>
</head>
<body>
	<div class="controls_parent">
		<div class="controls_objects">
			<form method="post" action="post.php">
				<button id="reload" type="submit" formtarget="post">Reload Post Box</button>
			</form>
			<form method="post" action="view.php">
				<button id="refresh" type="submit" formtarget="middle">Reload Messages</button>
			</form>
			<form method="post" action="profile.php">
				<button id="refresh" type="submit" formtarget="middle">Profile</button>
			</form>
			<?php
				if($userLevel == 8) {
					echo "<form method='post' action='admin.php'>
							<button id='admin' type='submit' formtarget='middle'>Admin</button>
						</form>";
				}
			?>
			<form method="post" action="logout.php">
				<button id="exit" type="submit" formtarget="_parent">Exit Chat</button>
			</form>
		</div>
		<div class="version">
			<p><?php echo $version;?></p>
		</div>
	</div>
</body>
</html>