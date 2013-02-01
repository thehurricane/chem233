<?php
include 'accesscontrol.php';
$pageTitle = "Home";
include 'header.php';
if (isset($_SESSION['uID'])) {
	$aID = $_SESSION['uID'];
	$usersResult = $mysqli->query("SELECT firstName, lastName FROM users WHERE uID = '$uID'");
	if ($usersResult) {
		$usersResultArray = $usersResult->fetch_assoc();
		$firstName = $usersResultArray['firstName'];
		$lastName = $usersResultArray['lastName'];
		echo "<p>Hello, $firstName $lastName.</p>\n";
	} else {
		//Database problem
		echo "<p>A database error occurred while checking your login details. If this error persists, please contact your administrator.</p>";
	}
}
include 'footer.php';
?>