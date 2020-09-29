<?php
    require 'settings.php';
    
	$conn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
	if(!$conn) {
		die("Connection to Database Failed: " . mysqli_connect_error());
	}
?>