<?php
	session_start();

	include('settings.php');
	
	$username = $_SESSION['username'];
	
	//establish database connection
	$mysqli = mysqli_connect($host, $dbuser, $dbpass, $dbname);
	$stmt = mysqli_stmt_init($mysqli);
	
	$result = "SELECT * FROM members WHERE nickname=?";
	
	if(!mysqli_stmt_prepare($stmt, $result)) {
		echo "SQL statment failed";
	} else {
		//Bind parameters to the placeholder
		mysqli_stmt_bind_param($stmt, "s", $username);
		//Run params in the database
		mysqli_stmt_execute($stmt);
		$checkLevel = mysqli_stmt_get_result($stmt);
				
		$level = $checkLevel->fetch_assoc();
	
		$userLevel = (int)$level['level'];
	}


if ($_SESSION['username'] == null || $userLevel != 8) {
	header('Location: login.php');
}

	if(isset($_POST['admin'])) {
		echo "<p>Feature Coming soon...</p>";
	} 
	
	if(isset($_POST['register'])) {
		
		$guest = $_POST['register_guest'];
		$whatLevel = 2;
		
		$result = "UPDATE members SET level=? WHERE nickname=?";
		
		if(!mysqli_stmt_prepare($stmt, $result)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "is", $whatLevel, $guest);
				//Run params in the database
				mysqli_stmt_execute($stmt);
			}
			
		$sql = "SELECT * FROM members WHERE nickname=?";
		
		if(!mysqli_stmt_prepare($stmt, $sql)) {
			echo "SQL statment failed";
		} else {
			//Bind parameters to the placeholder
			mysqli_stmt_bind_param($stmt, "s", $guest);
			//Run params in the database
			mysqli_stmt_execute($stmt);
			
			$sql = mysqli_stmt_get_result($stmt);
			
			$guestLevel = $sql->fetch_assoc();
	
			$guestUserLevel = (int)$guestLevel['level'];
			}
		
		if($guestUserLevel == '2') {
			echo "Successfully Registered " .$guest;
		} else {
			echo "Registration Failed on.... " .$guest;
		}
	}
	
	if(isset($_POST['change'])) {
		$rank = $_POST['rank'];
		$member = $_POST['member'];
		
		if($rank == 1) {
			$qLevel = 1;
			
			$result = "UPDATE members SET level=? WHERE nickname=?";
			
			if(!mysqli_stmt_prepare($stmt, $result)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "is", $qLevel, $member);
				//Run params in the database
				mysqli_stmt_execute($stmt);
			}
		}
		if($rank == 2) {
			$qLevel = 2;
			
			$result = "UPDATE members SET level=? WHERE nickname=?";
			
			if(!mysqli_stmt_prepare($stmt, $result)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "is", $qLevel, $member);
				//Run params in the database
				mysqli_stmt_execute($stmt);
			}
		}
		if($rank == 3) {
			$qLevel = 3;
			
			$result = "UPDATE members SET level=? WHERE nickname=?";
			
			if(!mysqli_stmt_prepare($stmt, $result)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "is", $qLevel, $member);
				//Run params in the database
				mysqli_stmt_execute($stmt);
			}
		}
		if($rank == 4) {
			$qLevel = 4;
			
			$result = "UPDATE members SET level=? WHERE nickname=?";
			
			if(!mysqli_stmt_prepare($stmt, $result)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "is", $qLevel, $member);
				//Run params in the database
				mysqli_stmt_execute($stmt);
			}
		}
			
		$sql = "SELECT * FROM members WHERE nickname=?";
		
		if(!mysqli_stmt_prepare($stmt, $sql)) {
				echo "SQL statment failed";
			} else {
				//Bind parameters to the placeholder
				mysqli_stmt_bind_param($stmt, "s", $member);
				//Run params in the database
				mysqli_stmt_execute($stmt);
				$sql = mysqli_stmt_get_result($stmt);
				
				$guestLevel = $sql->fetch_assoc();
	
				$guestUserLevel = (int)$guestLevel['level'];
			}

		//check if passes
		
		if($guestUserLevel == '1' && $rank == '1') {
			echo " Successfully switched ".$member." to Guest.";
		}else if($guestUserLevel == '2' && $rank == '2') {
			echo " Successfully switched ".$member." to registered member.";
		} else if($guestUserLevel == '3' && $rank == '3') {
			echo " Successfully switched ".$member." to Moderator.";
		}else if($guestUserLevel == '4' && $rank == '4') {
			echo " Successfully switched ".$member." to Admin.";
		} else {
			echo " Failed to switch ".$member." to the selected level.";
		}
		
	}
	
	if(isset($_POST['register_member'])) {
		echo "<p>Still In progress still has no functionality.</p>";
	}
	
?>
<html>
<head>
	<link rel = "stylesheet" type = "text/css" href = "css/admin_stylesheet.css" />
	<?php echo 	'<title>'.$chatname. " - Admin </title>"; ?>
</head>
<body>
	<div class="options">
		<h2>Administrative Functions</h2>
		<tr><td><hr></td></tr>
		<form method="post" action="admin.php">
			<button type="submit" name="admin" id="admin">Admin Setup</button>
		</form>
		<tr><td><hr></td></tr>
		<p>Clean Messages</p>
		<tr><td><hr></td></tr>
		<p>Kick Chatter</p>
		<tr><td><hr></td></tr>
		<p>logout Inactive Chatter</p>
		<tr><td><hr></td></tr>
		<p>View Active Sessions:</p>
			<?php 
			$sql = $mysqli->query("SELECT * FROM members");
					while($row = mysqli_fetch_array($sql)){   //Creates a loop to loop through results
						$online_name = (string)$row['nickname'];
						$online_status = (string)$row['status'];
						if($online_status == '1') {					
							echo $row['nickname'] . ", "; 
						}
					}
			?>
		<tr><td><hr></td></tr>
		<p>Change Guest Access</p>
		<tr><td><hr></td></tr>
		<p>Change Members Level:</p>
		<div class="adjust_member">
			<form id="change" action="admin.php" method="post" target="_self">
				<select name="member" style="width:100;text-align: center; background-color: black; color: white;">
					<option value="">(choose)</option>
					<?php
					$sql = $mysqli->query("SELECT * FROM members");
					while($row =  mysqli_fetch_array($sql)){
						$nickname = $row['nickname'];
						$online_status = (string)$row['status'];
						$account_level = (string)$row['level'];
						
						if($online_status == '1' && $account_level == '2' || $online_status == '1' && $account_level == '3'|| $online_status == '1' && $account_level == '4') {
							echo "<option value=".$nickname.">".$nickname."</option>";
						}
					}
					?>
				</select>
				<select name="rank" style="width:200;text-align: center; background-color: black; color: white;">
					<option value="">(choose)</option>
					<option value="1">Guest</option>
					<option value="2">Set to regular member</option>
					<option value="3">Set to moderator (M)</option>
					<option value="4">Set to admin (A)</option>
				</select>
				<button type="submit" name="change" id="change">Change</button>
			</form>
		</div>
		<tr><td><hr></td></tr>
		<p>Register Guest</p>
		<div class="register_guest">
			<form id="register" action="admin.php" method="post" target="_self">
				<select name="register_guest" style="text-align: center; background-color: black; color: white;">
					<option value="">(choose)</option>
					<?php
					$sql = $mysqli->query("SELECT * FROM members");
					while($row =  mysqli_fetch_array($sql)){
						$nickname = $row['nickname'];
						$online_status = (string)$row['status'];
						$account_level = (string)$row['level'];
						
						if($online_status == '1' && $account_level == '1') {
							echo "<option value=".$nickname.">".$nickname."</option>";
						}
					}
					mysqli_close($mysqli);
					?>
				</select>
				
				<button type="submit" name="register" id="register">Register</button>
			</form>
		</div>
		<tr><td><hr></td></tr>
		<p>Register New Member</p>
		<div class="register_member">
			<form id="register_member" action="admin.php" method="post" target="middle">
			<input type="text" placeholder="Username" name="username" id="username">
			<input type="password" placeholder="Password" name="password" id="password">
			<button type="submit" name="register_member" id="register_member">Register</button>
			</form>
		</div>
		<tr><td><hr></td></tr>
	</div>
</body>
</html>