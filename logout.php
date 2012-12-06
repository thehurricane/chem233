<?php
	//Destroy any session variables (like uID, effectively log the user out)
	session_start();
	session_destroy();
	//Redirect the user to the main page
	header("location:index.php");
?>