<?php
	include('includes/settings.php');
	include('includes/dbh.inc.php');

	session_start();
	
	$username = $_SESSION['username'];

	if ($_SESSION['username'] === null || $_SESSION['level'] != 8) {
		header('Location: login.php');
	}

	if(isset($_POST['admin'])) {
		echo "<p style='color:red;text-align:center;font-weight:bold;'>Button not implemented yet...</p></style>";
	}
	
	if(isset($_POST['cleanmessages'])) {
		if(isset($_POST['delete_selection'])){
			if($_POST['delete_selection'] === "whole-room") {
				
				$sql = "UPDATE messages SET delstatus=?;";
				$stmt = mysqli_stmt_init($conn);
				
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 SQL statment failed.</p></style>";
				} else {
					$delStatus=1;
					mysqli_stmt_bind_param($stmt, "i", $delStatus);
					mysqli_stmt_execute($stmt);
					
					echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 All messages deleted.</p></style>";
				}
			} else if($_POST['delete_selection'] === "nickname"){
				$poster_to_del = $_POST['nickname-messages'];
				
				$sql = "UPDATE messages SET delstatus=1 WHERE poster=?;";
				$stmt = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 There was an SQL error</p></style>";
				} else {
					mysqli_stmt_bind_param($stmt, "s", $poster_to_del);
					mysqli_stmt_execute($stmt);
					if($_POST['nickname-messages'] === "") {
						echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
							 You must select a member that you'd like to delete the messages from</p>";
					} else {
						echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
							 Successfully deleted all messages sent by '".$poster_to_del."'</p></style>";
					}
				}
			} else if ($_POST['delete_selection'] === "individual") {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 Feature not yet implemented, I'm very sorry.</p></style>";
			}
		} else {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 You must make a selection...'</p></style>";
		}
	}
	
	if(isset($_POST['kick-chatter'])) {
		$kickedUser = $_POST['kick-list'];
		$kickedMessage = $_POST['kick-msg'];
		$purgeMessages = $_POST['del-msgs'];
		$kickTime = $_POST['kick-timeout'];
		
		$sql = "UPDATE members SET kick_msg=? WHERE username=?;";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 There was an SQL error</p></style>";
		} else {
			mysqli_stmt_bind_param($stmt, "ss", $kickedMessage, $kickedUser);
			mysqli_stmt_execute($stmt);
			
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 There was an error kicking $kickedUser</p></style>";
			} else {
				
				//If purge messages box is ticked then delete all their messages
				if($purgeMessages == 1) {
				
					$sql = "UPDATE messages SET delstatus=1 WHERE poster=?;";
					$stmt = mysqli_stmt_init($conn);
					if(!mysqli_stmt_prepare($stmt, $sql)) {
						echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
							 There was an SQL error</p></style>";
					} else {
						mysqli_stmt_bind_param($stmt, "s", $kickedUser);
						mysqli_stmt_execute($stmt);
						
						if(!mysqli_stmt_prepare($stmt, $sql)) {
							echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
								 There was an error purging $kickedUser messages.</p></style>";
						}		
					}
				}
				
				//set timeout value
				$sql = "UPDATE members SET timeout=? WHERE username=?;";
				$stmt = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 There was an SQL error</p></style>";
				} else {
					mysqli_stmt_bind_param($stmt, "is", $kickTime, $kickedUser);
					mysqli_stmt_execute($stmt);	

					if(!mysqli_stmt_prepare($stmt, $sql)) {
						echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
							 There was an error setting $kickedUser timeout</p></style>";
					}						
				}
				
				//set user online status to 0
				$curTime = date('g:i:s');
				$sql = "UPDATE members SET online=0, time_kicked='$curTime' WHERE username=?;";
				$stmt = mysqli_stmt_init($conn);
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 There was an SQL error</p></style>";
				} else {
					mysqli_stmt_bind_param($stmt, "s", $kickedUser);
					mysqli_stmt_execute($stmt);
					
					if(!mysqli_stmt_prepare($stmt, $sql)) {
						echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
							 There was an error kicking $kickedUser</p></style>";
					} else {
						if($_POST['kick-list'] == ""){
							echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
								 You must select a user to be kicked.</p></style>";
						} else {
							echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
								 $kickedUser has been successfully kicked with the message '$kickedMessage' for $kickTime minutes.</p></style>";
						}
					}
				}
			}
		}
	}
	
	if(isset($_POST['topic-button'])) {
		$topic_message = $_POST['topic'];
		
		$sql = "UPDATE settings SET topic=?;";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 There was an SQL error</p></style>";
		} else {
			mysqli_stmt_bind_param($stmt, "s", $topic_message);
			mysqli_stmt_execute($stmt);
			
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 There was an error updating the topic</p></style>";
			} else {
				echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 Topic updated</p></style>";
			}
		}
	}
	
	if(isset($_POST['access-button'])) {
		$newAccessLevel = $_POST['access'];
		
		$sql = "UPDATE settings SET chat_access_level=?;";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 There was an SQL error</p></style>";
		} else {
			mysqli_stmt_bind_param($stmt, "i", $newAccessLevel);
			mysqli_stmt_execute($stmt);
			
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 Error updating chat access level</p></style>";
			} else {
				echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 Updated chat access level</p></style>";
			}
		}
	}
	
	if(isset($_POST['level-change-button'])) {
		$memberLevel = $_POST['member-list'];
		$newLevel = $_POST['level-change'];
		
		if($memberLevel == "") {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 You must select a user to change the level of '$memberLevel' first!</p></style>";
		}else if($newLevel == "") {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 You must select a level to change $memberLevel to first!</p></style>";
		} else if($newLevel == "delete") {
			$sql = "UPDATE members SET online=0, level=1 WHERE username=?;";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 There was an SQL error</p></style>";
			} else {
				mysqli_stmt_bind_param($stmt, "s", $memberLevel);
				mysqli_stmt_execute($stmt);
					
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 Error deleting $memberLevel</p></style>";
				} else {
					echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 successfully Deleted $memberLevel</p></style>";
				}
			}
		} else if($newLevel == "deny") {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 Sorry, but this feature hasn't been implemented yet...</p></style>";
		} else {
			$sql = "UPDATE members SET level=? WHERE username=?;";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 There was an SQL error</p></style>";
			} else {
				mysqli_stmt_bind_param($stmt, "is", $newLevel, $memberLevel);
				mysqli_stmt_execute($stmt);
					
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 Error changing $memberLevel level</p></style>";
				} else {
					echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 Updated $memberLevel level to $newLevel</p></style>";
				}
			}
		}	
	}
	
	if(isset($_POST['reset-password'])) {
		$members = $_POST['member-list'];
		$newPwd = $_POST['newPwd'];
		$hashedPwd = password_hash($newPwd, PASSWORD_DEFAULT);
		
		if($members == "") {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 You must select a user to reset the password of first!</p></style>";
		} else {
			$sql = "UPDATE members SET password=? WHERE username=?;";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 There was an SQL error</p></style>";
			} else {
				mysqli_stmt_bind_param($stmt, "ss", $hashedPwd, $members);
				mysqli_stmt_execute($stmt);
				
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 Error resetting $members's password.</p></style>";
				} else {
					echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 Changed $members's password.</p></style>";
				}
			}
		}	
	}
	
	if(isset($_POST['register-guest'])) {
		$guestToMember = $_POST['register-list'];
		
		if($guestToMember == "") {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 You must select a user to make a member first!</p></style>";
		} else {
			$sql = "UPDATE members SET level=2, joined=CURRENT_TIMESTAMP WHERE username=?;";
			$stmt = mysqli_stmt_init($conn);
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 There was an SQL error</p></style>";
			} else {
				mysqli_stmt_bind_param($stmt, "s", $guestToMember);
				mysqli_stmt_execute($stmt);
				
				if(!mysqli_stmt_prepare($stmt, $sql)) {
					echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 Error making $guestToMember a member.</p></style>";
				} else {
					echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
						 Made $guestToMember a member!</p>";
				}
			}
		}	
	}
	
	if(isset($_POST['register-new-member'])) {
		$newMember = $_POST['member-name'];
		$newMemberPass = $_POST['member-pass'];
		$hashedPwd = password_hash($newMemberPass, PASSWORD_DEFAULT);
		
		$sql = "INSERT INTO members (username, password, joined, level) VALUES (?, ?, CURRENT_TIMESTAMP, '2')";
		$stmt = mysqli_stmt_init($conn);
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
				 There was an SQL error</p></style>";
		} else {
			mysqli_stmt_bind_param($stmt, "ss", $newMember, $hashedPwd);
			mysqli_stmt_execute($stmt);
				
			if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "<p style='color: red;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 Error registering $newMember.</p></style>";
			} else {
				echo "<p style='color: green;width: 300px;font-weight: bold;margin: 0 auto;text-align: center;'>
					 Registered $newMember as a new member account.</p></style>";
			}
		}
	}
		
?>
<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/admin_stylesheet.css" />
	<?php echo 	'<title>'.$chatName. " - Admin </title>"; ?>
</head>
<body>
	<div class="options">
		<h2>Administrative Functions</h2>
		
		<hr>
		<form method="post" action="admin.php">
			<button type="submit" name="admin" id="admin">Admin Setup</button>
		</form>
		
		<hr>
		<div class="clean-messages">
			<p>Clean Messages</p>
		</div>
		
		<div class="clean-chat">
			<form action="admin.php" method="post" target="_self">
				<div class="whole-room">
					<input type="radio" name="delete_selection" value="whole-room">
					<p>Whole room</p>
				</div>
				
				<div class="nickname">
					<input type="radio" name="delete_selection" value="nickname">
					<p>Following Nickname: </p>
				</div>
				
				<div class="selection">
					<input type="radio" name="delete_selection" value="individual">
					<p>Selection: </p>
				</div>
				
				<div class="nickname-selector">
					<select name="nickname-messages">
						<option value="">(choose)</option>
						<?php
							$sql = $conn->query("SELECT DISTINCT poster FROM messages ORDER BY postdate desc");
							while($row = mysqli_fetch_array($sql)){
								echo "<option value='".$row['poster']."'>".$row['poster']."</option>";
							}
						?>
					</select>
				</div>
				
				<div class="clean-chat-submit">
					<button type="submit" id="clean-messages" name="cleanmessages">Clean</button>
				</div>
			</form>
			
			<hr>
			<div class="kick-chatter">
				<p>Kick Chatter</p>
			</div>
			
			<form action="admin.php" method="post" target="_self">
				
				<div class="kick-message">
					<p>Kick Message: </p>
					<input id="kick-msg" type="text" name="kick-msg" value="You've been kicked from the chat">
				</div>
				
				<div class="kick-timeout">
					<p>Kick Time: </p>
					<select name="kick-timeout">
						<option value="0">(choose)</option>
						<?php
							for($x=0;$x<=60;$x++){
								echo "<option value='".$x."'>".$x."</option>";
							}
						?>
					</select>
					<br>
					<p style="font-size:13px">time is in minutes ex 0-60 max</p>
				</div>
				
				<div class="delete-msgs">
					<input type="hidden" name="del-msgs" value="0">
					<input type="checkbox" name="del-msgs" value="1">
					<p>Purge Messages</p>
				</div>
				
				<div class="player-list">
					<p>Players: </p>
					<select name="kick-list">
						<option value="">(choose)</option>
						<?php
							$sql = $conn->query("SELECT username FROM members");
							while($row = mysqli_fetch_array($sql)){
								echo "<option value='".$row['username']."'>".$row['username']."</option>";
							}
						?>
					</select>
				</div>

				<div class="kick-chatter-submit">
					<button type="submit" id="kick-chatter" name="kick-chatter">Kick</button>
				</div>
			</form>
			
			<hr>
			<div class="chat-topic">
				<p>Chat Topic</p>
			</div>
			
			<form action="admin.php" method="post" target="_self">
				<div class="topic">
					<input type="text" name="topic" placeholder="" value="">
				</div>
				
				<div class="topic-submit">
					<button type="submit" id="topic-button" name="topic-button">Change</button>
				</div>
			</form>
			
			<hr>
			<div class="chat-topic">
				<p style="margin-left:-360px;">Change Chat Access</p>
			</div>
			
			<form action="admin.php" method="post" target="_self">
				<div class="chat-access">
					<select name="access">
						<option value="0">Allow</option>
						<option value="1">Allow with waitingroom</option>
						<option value="2">Allow with Mod Approval</option>
						<option value="3">Member only</option>
						<option value="4">Maintanence Mode</option>						
					</select>
				</div>
				
				<div class="topic-submit">
					<button type="submit" id="topic-button" name="access-button">Change</button>
				</div>
			</form>
			
			<hr>
			<div class="chat-topic">
				<p style="margin-left:-342px;">Change Member Level</p>
			</div>
				
			<form action="admin.php" method="post" target="_self">
				<div class="member-list">
					<select name="member-list">
					<option value="">(choose)</option>
					<?php
						$sql = $conn->query("SELECT username FROM members WHERE level>1");
						while($row = mysqli_fetch_array($sql)){
							echo "<option value='".$row['username']."'>".$row['username']."</option>";
						}
					?>
				</select>
				</div>
				
				<div class="member-access">
					<select name="level-change">
						<option value="">(choose)</option>
						<option value="delete">Delete from database</option>
						<option value="deny">Deny access (!)</option>
						<option value="1">Member</option>
						<option value="2">Special</option>
						<option value="3">Moderator</option>
						<option value="4">supermod (SM)</option>
						<option value="8">Admin</option>
					</select>
				</div>
			
				<div class="topic-submit">
					<button type="submit" id="topic-button" name="level-change-button">Change</button>
				</div>
			</form>
			
			<hr>
			<div class="chat-topic">
				<p style="margin-left:-390px;">Reset Password</p>
			</div>
			
			<form action="admin.php" method="post" target="_self">
				<div class="member-list">
					<select name="member-list">
						<option value="">(choose)</option>
						<?php
							$sql = $conn->query("SELECT username FROM members WHERE level>1");
							while($row = mysqli_fetch_array($sql)){
								echo "<option value='".$row['username']."'>".$row['username']."</option>";
							}
						?>
					</select>
				</div>
				
				<div class="reset-password">
					<input type="password" name="newPwd" placeholder="new password" value="" required>
				</div>
				
				<div class="topic-submit">
					<button type="submit" id="topic-button" name="reset-password">Change</button>
				</div>
			</form>
			
			<hr>
			<div class="chat-topic">
				<p style="margin-left:-390px;">Register Guest</p>
			</div>
			
			<form action="admin.php" method="post" target="_self">
				<div class="register-list">
					<select name="register-list">
						<option value="">(choose)</option>
						<?php
							$sql = $conn->query("SELECT username FROM members WHERE level=1");
							while($row = mysqli_fetch_array($sql)){
								echo "<option value='".$row['username']."'>".$row['username']."</option>";
							}
						?>
					</select>
				</div>
				
				<div class="topic-submit">
					<button type="submit" id="topic-button" name="register-guest">Register</button>
				</div>
			</form>
			
			<hr>
			<div class="new-member">
				<p style="margin-left:-345px;">Register new Member</p>
			</div>
			
			<form action="admin.php" method="post" target="_self">
				<div class="new-member-username">
					<p>Username</p>
					<input type="text" name="member-name" placeholder="username" required>
				</div>
				
				<div class="new-member-pass">
				<p>Password</p>
					<input type="password" name="member-pass" placeholder="password" required>
				</div>
				<div class="topic-submit">
					<button type="submit" id="topic-button" name="register-new-member">Register</button>
				</div>
			</form>
			
		</div>
	</div>
</body>
</html>