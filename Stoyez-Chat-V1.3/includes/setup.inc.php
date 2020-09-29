<?php
	if(isset($_POST['submit-setup'])) {
		require 'settings.php';
		
		//Database Info
		$host = $_POST['dbhost'];
		$user = $_POST['dbuser'];
		$pass = $_POST['dbpass'];
		$name = $_POST['dbname'];
		
		//Chat settings
		$chatname = $_POST['chatname'];
		$setupran = '1';
		$timeformat = "12hr";
		$timezone = "Europe/London";
		
		//Admin Account
		$username = $_POST['adminUser'];
		$password = $_POST['adminPass'];
		
		//Make settings	file
		
		$dbHost = var_export($host, true);
		$dbUser = var_export($user, true);
		$dbPass = var_export($pass, true);
		$dbName = var_export($name, true);
		$chatName = var_export($chatname, true);
		$timeZone = var_export($timezone, true);
		$timeFormat = var_export($timeformat, true);
		$setupRan = var_export($setupran, true);
		
		$Data = "<?php\n\n\$dbHost = '$host';\n\$dbUser = '$user';\n\$dbPass = '$pass';\n\$dbName = '$name';\n\$chatName = '$chatname';\n\$timeFormat = '$timeformat';\n\$timeZone = '$timezone';\n\$setup_ran = $setupRan;\n\n ?>";
		file_put_contents('settings.php', $Data);
		
		//Create Databases
		require 'dbh.inc.php';
		
		$membersTable = "CREATE TABLE members (id integer PRIMARY KEY AUTO_INCREMENT, username VARCHAR(50), password VARCHAR(255), level int(1) DEFAULT '1', online int(1), regedby VARCHAR(50), incognito int(1) DEFAULT '0', enablePM int(1) DEFAULT '1', kick_msg VARCHAR(255), timeout int(3), time_kicked TIME, lastlogin DATE, joined DATE)";
		$messagesTable = "CREATE TABLE messages (id integer PRIMARY KEY AUTO_INCREMENT, postdate DATE, poster varchar(50), recipient varchar(50), message text, delstatus int(1) DEFAULT '0')";
		$settingsTable = "CREATE TABLE settings (captcha int(1), chatname varchar(50), topic varchar(50), refreshrate int(2), chat_access_level int(2), closed_message text, enable_greeting int(1), greeting_message text, guest_kick int(5), guest_registration int(1))";
		
		if ($conn->query($membersTable) === TRUE) {
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
			
			$sql = "INSERT INTO settings (captcha, chatname, refreshrate, chat_access_level, closed_message, enable_greeting, greeting_message, guest_kick, guest_registration) VALUES ('0', '$chatname', '20', '0', '<h1 style=\'color:red;width:300px;font-weight:bold;margin:0 auto;text-align:center;\'>Chatting Temporarily Disabled.</h1>', '0', '<h1>Welcome to our Chat!</h1>', '0', '0')";
				if($conn->query($sql) === TRUE) {
					echo "New record created successfully<br>";
				} else {
					echo "Error: " . $sql . "<br>" . $conn2->error;
				}
		} else {
			echo "<div class=\"failed\">Error creating table \"Settings\": " . $conn->error;
			echo "</div>";
		}
		
		
		//Create Admin Account
		if(!preg_match("/^[A-Za-z0-9]*$/",$username)) {
			header("Location: ../setup.php?error=invalidUsername");
			exit();
		} else {
			$sql = "SELECT username FROM members WHERE username=?";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				header("Location: ../setup.php?error=sqlError");
				exit();
			} else {
				$sql = "INSERT INTO members (username, password, level, online, regedby, lastlogin, joined) VALUE(?, ?, '8', '0', 'setup', NOW(), NOW())";
				$stmt = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					header("Location: ../setup.php?error=sqlError");
					exit();
				} else {
					$hashedPwd = password_hash($password, PASSWORD_DEFAULT);
					
					mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPwd);
					mysqli_stmt_execute($stmt);
					header("Location: ../login.php?setup=success");
					exit();
				}
			}
		}
		mysqli_stmt_close($stmt);
		mysqli_close($conn);
	} else {
		header("Location: ../setup.php");
		exit();
	}
?>