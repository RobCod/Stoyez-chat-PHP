<?php
	$setup_ran = 0;
	
	include ('lang_eng.php');
	include ('settings.php');
	
	session_start();

function thr(){
	echo '<tr><td><hr></td></tr>';
}

if($setup_ran==1) {
	header('Location: login.php');
} else if($setup_ran==NULL || $setup_ran==0){
	
}

?>

<html>
<head>
<link rel = "stylesheet" type = "text/css" href = "css/setup_stylesheet.css" />
<title><?php echo $I['init'] ?></title>
</head>
<body>
<div class="banner">Stoyez Chat</div>
<form action="functions.php" method="post">

<!--Super Admin account setup-->
<div class="setup_container">
<h2>First Time setup</h2>
<div class="superUsr"><label for="superUsr"><b>SuperAdmin Username: </b></label>
<input type="text" placeholder="Enter Username" name="superUsr" id="size" required>
<br><label for="superUsrPass"><b>SuperAdmin Password: </b></label>
<input type="password" placeholder="Enter Password" name="superUsrPass" id="size" required>
</div>
				 
<!--Sectioning off Account creation & database input-->
<br><br><br><tr><td><hr></td></tr>
<br><h2>DataBase Setup</h2>
				 
<!--Data Base setup-->
<div class="database_setup1"><label for="dbhost"><b>Database Host : </b></label>
<input type="text" value="localhost" name="dbhost" id="size" required>
			
<br><label for="dbuser"><b>Database User : </b></label>
<input type="text" value="root" name="dbuser" id="size" required>

<br><label for="dbname"><b>Database Name: </b></label>
<input type="text" value="stoyez_chat" name="dbname" id="size" required>

<br><label for="dbpass"><b> Database Pass : </b></label>
<input type="password" value="" name="dbpass" id="size">
</div>

<!--Sectioning off Database setup && chat creation (Create this into its own file eventually)-->
<br><br><br><tr><td><hr></td></tr>
<br><h2>Chat Setup</h2>

<!--CHAT SETUP-->
<div class="chat_setup"><label for="chatname"><b>Chat Name : </b></label>
<input type="text" placeholder="My Chat" name="chatname" id="size" required>
</div>
				 
<div class="superButton"><button type="submit">Setup & Create Admin Account</button></div>
</div>
</div>
</body>
</html>

