<?php
	if(isset($_POST['submit-login'])) {
		require 'dbh.inc.php';
		
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		$entryLevel = $conn->query("SELECT * FROM settings");
		while($row = mysqli_fetch_array($entryLevel)){
			$access = $row['chat_access_level'];
		}
		
		if(empty($username) || empty($password)) {
			header("Location: ../login.php?error=emptyFields");
			exit();	
		} else {
			$sql = "SELECT * FROM members WHERE username=?;";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				header("Location: ../login.php?error=sqlError");
				exit();
			} else {
				mysqli_stmt_bind_param($stmt, "s", $username);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				if($row = mysqli_fetch_assoc($result)) {
					$pwdCheck = password_verify($password, $row['password']);
					if($pwdCheck == false) {
						header("Location: ../login.php?error=wrongPassword");
						exit();
					} else if($pwdCheck == true) {
						$adjustStatus = "UPDATE members SET online=1, lastlogin=CURRENT_TIMESTAMP WHERE username=?";
						
						if(!mysqli_stmt_prepare($stmt, $adjustStatus)) {
							echo "SQL statment failed";
						} else {
							//Bind parameters to the placeholder
							mysqli_stmt_bind_param($stmt, "s", $username);
							//Run params in the database
							mysqli_stmt_execute($stmt);
							
							session_start();
							$_SESSION['UserID'] = $row['id'];
							$_SESSION['username'] = $row['username'];
							$_SESSION['level'] = $row['level'];
							$_SESSION['online'] = $row['online'];
							$_SESSION['enablePM'] = $row['enablePM'];
							$_SESSION['incognito'] = $row['incognito'];
						
							header("Location: ../chat.php?login=success");
							exit();
							
						}
					} else {
						header("Location: ../login.php?error=wrongPassword");
						exit();
					}
				} else {
					$guestAccount = "INSERT INTO members (username, password, online, lastlogin) VALUES (?, ?, '1', CURRENT_TIMESTAMP)";
					if(!mysqli_stmt_prepare($stmt, $guestAccount)) {
						header("Location: ../login.php?error=sqlError");
						exit();
					} else {
						//create guest account
						$hashedPwd = password_hash($password, PASSWORD_DEFAULT);
						mysqli_stmt_bind_param($stmt, "ss", $username, $hashedPwd);
						mysqli_stmt_execute($stmt);
							
							
						//check existing accounts access levels for login perms
						$sql = "SELECT * FROM members WHERE username=?;";
						if(!mysqli_stmt_prepare($stmt, $sql)) {
							header("Location: ../login.php?error=sqlError");
							exit();
						} else {
							mysqli_stmt_bind_param($stmt, "s", $username);
							mysqli_stmt_execute($stmt);
							$result = mysqli_stmt_get_result($stmt);
							if($row = mysqli_fetch_assoc($result)) {
								$levelSQL = $conn->query("SELECT * FROM settings");
								if($row2 = mysqli_fetch_array($levelSQL)){
									$access = $row2['chat_access_level'];
									if($access == "1") {
										existingMember($username, $password, $row, $stmt);
									} else if($access == "2") {
										existingMember($username, $password, $row, $stmt);
									} else if($access >= 3 && $access > $row['level']) {
										deleteTempAccount($username, $stmt);
									} else if($access == 4 && $access > $row['level'] && $row['level'] < 2) {
										deleteTempAccount($username, $stmt);
									} else if($access == 4 && $access > $row['level'] && $row['level'] == "2") {
										header("Location: /logout.inc.php");
										exit();
									} else if($access < $row['level']) {
										existingMember($username, $password, $row, $stmt);
									} else {
										header("Location: /logout.inc.php");
										exit();
									}
								}
							} 
						}
					}
				}
			}
		}
		
	} else {
		header("Location: ../login.php");
		exit();
	}
	
	function guestAccount($username, $password, $stmt){
		
	}
	
	function deleteTempAccount($username, $stmt) {
		$delete = "DELETE FROM members WHERE username=?";
		if(!mysqli_stmt_prepare($stmt, $delete)){
			echo "SQL statment failed";
		} else {
			mysqli_stmt_bind_param($stmt, "s", $username);
			mysqli_stmt_execute($stmt);
			session_unset();
			session_destroy();
			
			header("Location: ../login.php");
			exit();
		}
	}
	
	function existingMember($username, $password, $row, $stmt) {
		$pwdCheck = password_verify($password, $row['password']);
		if($pwdCheck == false) {
			header("Location: ../login.php?error=wrongPassword");
			exit();
		} else if($pwdCheck == true) {
			$adjustStatus = "UPDATE members SET online=1, lastlogin=CURRENT_TIMESTAMP WHERE username=?";
												
			if(!mysqli_stmt_prepare($stmt, $adjustStatus)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "s", $username);
				//Run params in the database
				mysqli_stmt_execute($stmt);
												
				session_start();
				
				$_SESSION['UserID'] = $row['id'];
				$_SESSION['username'] = $row['username'];
				$_SESSION['level'] = $row['level'];
				$_SESSION['online'] = $row['online'];
				$_SESSION['enablePM'] = $row['enablePM'];
				$_SESSION['incognito'] = $row['incognito'];
				
				//for waiting room
				date_default_timezone_set($timeZone);
				$_SESSION['waiting_room_start'] = strtotime(date('g:i:s'));
												
				header("Location: ../chat.php?login=success");
				exit();
													
			}
		} else {
			header("Location: ../login.php?error=wrongPassword");
			exit();
		}
	}
?>