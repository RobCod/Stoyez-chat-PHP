<?php
	session_start();

	include ('includes/settings.php');
	include ('includes/version.php');
	
	$username = $_SESSION['username'];

if ($_SESSION['username'] == null) {
	header('Location: login.php');
} 

?>

<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/view_stylesheet.css" />
	<?php echo 	"<title>".$chatName. " - Controls </title>"; ?>
</head>
<body>
	<div class="controls_parent">
		<div class="controls_objects">
			<form method="post" action="post.php">
				<button name="reload" id="reload" type="submit" formtarget="post">Reload Post Box</button>
			</form>
			<form method="post" action="view.php">
				<button name="refresh" id="refresh" type="submit" formtarget="middle">Reload Messages</button>
			</form>
			<form method="post" action="profile.php">
				<button id="refresh" type="submit" formtarget="middle">Profile</button>
			</form>
			<?php
				if($_SESSION['level'] >= 8) {
					echo "<form method='post' action='admin.php'>
							<button id='admin' type='submit' formtarget='middle'>Admin</button>
						</form>";
				}
			?>
			<form method="post" action="includes/logout.inc.php">
				<button id="exit" type="submit" formtarget="_parent">Exit Chat</button>
			</form>
		</div>
		<div class="version">
			<p><?php echo $version;?></p>
		</div>
	</div>
</body>
</html>