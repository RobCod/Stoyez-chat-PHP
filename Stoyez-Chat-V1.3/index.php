<?php
	require 'includes/settings.php';
	
	if(isset($_SESSION)) {
		header('Location: chat.php');
	} else if($setup_ran === "0"){
		header("Location: setup.php");
	} else if($setup_ran === "1") {
		header("Location: login.php");
	}
?>