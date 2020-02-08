<?php

	if (isset($_POST['superUsr'])) {
		$user = $_POST['superUsr'];
	}

	if (isset($_POST['superUsrPass'])) {
		$pass = $_POST['superUsrPass'];
	}

	if (isset($_POST['dbhost'])) {
		$host = $_POST['dbhost'];
	}
	
	if (isset($_POST['dbuser'])) {
		$dbuser = $_POST['dbuser'];
	}

	if (isset($_POST['dbname'])) {
		$dbname = $_POST['dbname'];
	}

	if (isset($_POST['dbpass'])) {
		$dbpass = $_POST['dbpass'];
	}
	
	if (isset($_POST['chatname'])) {
		$chatname = $_POST['chatname'];
	}
	
	$setup_ran = "1";
	$timesetting = "12hr";

	//saving settings to settings.php for future grab
	$var_str3 = var_export($host, true);
	$var_str4 = var_export($dbuser, true);
	$var_str5 = var_export($dbname, true);
	$var_str6 = var_export($dbpass, true);
	$var_str7 = var_export($chatname, true);
	$var_str8 = var_export($timesetting, true);
	$var_str9 = var_export($setup_ran, true);
	$var = "<?php\n\n\$host = $var_str3;\n\n\$dbuser = $var_str4;\n\n\$dbname = $var_str5;\n\n\$dbpass = $var_str6;\n\n\$chatname = $var_str7;\n\n\$timesetting = $var_str8;\n\n\$setup_ran = $var_str9;\n\n ?>";
	file_put_contents('settings.php', $var);
	
	first_setup();
	adminAccountCreation();
	
	header("Location: login.php");
	exit();
	
function first_setup(){
	include 'settings.php';
	
	//Create Database connection
	$conn_check = new mysqli($host, $dbuser, $dbpass, $dbname);
	
	if($conn_check->connect_error){
		$conn = new mysqli($host, $dbuser, $dbpass);
		//Verify if connection is correct
		if($conn->connect_error){
			die("Connection Failed: " . $conn->connect_error);
		}
		
		
		//IF DATABASE "stoyez_chat" Does not exist - then create it
		$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
		
		if($conn->query($sql) === TRUE){
			echo "<div class=\"success\">MYSQL has shown a non existing database; A database has been created successfully matching the name provided in the settings</div>";
		} else {
			echo "<div class=\"failed\">Error creating database: " . $conn-error;
			echo "</div>";
		}
		$conn->close();
	} else {
		echo "<div class=\"failed\">DATABASE not created, already exists, if you wish to reinstall with a new Database; Please delete the database prior to running this page.</span>";
		echo "</div>";
		$conn_check->close();
	}
	
	//IF Table "messages, members" doesn't exist - then create it
	$conn = new mysqli($host, $dbuser, $dbpass, $dbname);
	if($conn->connect_error){
		die("Connection Failed: " . $conn->connect_error);
	}
	//Creating Tables
	$membersTable = "CREATE TABLE members (id integer PRIMARY KEY AUTO_INCREMENT, nickname VARCHAR(50) NOT NULL UNIQUE, passhash VARCHAR(255) NOT NULL, level SMALLINT DEFAULT '1', status int(11) NOT NULL, refresh SMALLINT DEFAULT '20', regedby VARCHAR(50) DEFAULT '', lastlogin INTEGER DEFAULT '0', timestamps SMALLINT DEFAULT '0', incognito SMALLINT DEFAULT '0', eninbox SMALLINT DEFAULT '0')";
	$messagesTable = "CREATE TABLE messages (id integer PRIMARY KEY AUTO_INCREMENT, postdate VARCHAR(50) DEFAULT '0', poststatus smallint DEFAULT '1', poster varchar(50) NOT NULL, recipient varchar(50) NOT NULL, text text NOT NULL, delstatus smallint DEFAULT '0')";
	$settingsTable = "CREATE TABLE settings (captcha VARCHAR(50) DEFAULT '0', captchachars VARCHAR(255) DEFAULT '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', chatname VARCHAR(50) DEFAULT 'My Chat', refreshrate VARCHAR(50) DEFAULT 20, disablepm VARCHAR(50) DEFAULT '0', disabletext VARCHAR(50) DEFAULT '<h1>Chatting Temporarily Disabled.</h1>', enablegreeting VARCHAR(50) DEFAULT '0', englobalpass VARCHAR(50) DEFAULT '0', eninbox VARCHAR(50) DEFAULT '1', globalpass VARCHAR(50) DEFAULT '', access VARCHAR(50) DEFAULT '1', guestexpire VARCHAR(50) DEFAULT '15', guestreg VARCHAR(50) DEFAULT '0')";
	
	
		
	if($conn->query($membersTable ) === TRUE) {
		echo "<div class=\"success\">Table \"Members\" created successfully!</div>";
	} else {
		echo "<div class=\"failed\">Error creating table \"Members\": " . $conn->error;
		echo "</div>";
	}
	
	if($conn->query($messagesTable) === TRUE) {
		echo "<div class=\"success\">Table \"Messages\" created successfully!</div>";
	} else {
		echo "<div class=\"failed\">Error creating table \"Messages\": " . $conn->error;
		echo "</div>";
	}
	
	if($conn->query($settingsTable) === TRUE) {
		echo "<div class=\"success\">Table \"Settings\" created successfully!</div>";
		
			$conn2 = new mysqli($host, $dbuser, $dbpass, $dbname);
			
			if($conn2->connect_error) {
				die("Connection failed, could\'t insert default settings: " . $conn2->connect_error);
			}
			
			$sql = "INSERT INTO settings (chatname) VALUES ('$chatname')";
			if($conn2->query($sql) === TRUE) {
				echo "New record created successfully<br>";
			} else {
				echo "Error: " . $sql . "<br>" . $conn2->error;
			}
			
			$conn2->close();
	} else {
		echo "<div class=\"failed\">Error creating table \"Settings\": " . $conn->error;
		echo "</div>";
	}
	
	$conn->close();
}

function adminAccountCreation(){
	
	include ('settings.php');
	
	//establish database connection
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname) or die($mysqli->error);
	
	// Escape all $_POST variables to protect against SQL injections
	$username = $mysqli->escape_string($_POST['superUsr']);
	$password = $mysqli->escape_string(password_hash($_POST['superUsrPass'], PASSWORD_BCRYPT));
	$hash = $mysqli->escape_string(md5( rand(0, 1000) ) );
	
	$result = $mysqli->query("SELECT * FROM members WHERE nickname='$username'") or die($mysqli->error());
	
	if ($result->num_rows > 0) {
		
	} else {
		$sql = "INSERT INTO members (nickname, passhash, level, regedby, eninbox) VALUES ('$username', '$password', '8', 'root', '1')";
		
		if($mysqli->query($sql) === TRUE) {
			session_destroy();
			header('Location login.php');
		} else {
			echo "Error: " . $sql . "<br>" . $mysqli->error;
		}
	}
}

?>