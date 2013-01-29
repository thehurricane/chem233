<?php
include 'adminAccessControl.php';
$pageTitle = "Administrator Login";
include 'header.php';

if (isset($_SESSION['aID'])) {
	$aID = $_SESSION['aID'];
	$adminsResult = $mysqli->query("SELECT firstName, lastName FROM admins WHERE aID = '$aID'");
	if ($adminsResult) {
		$adminsResultArray = $adminsResult->fetch_assoc();
		$firstName = $adminsResultArray['firstName'];
		$lastName = $adminsResultArray['lastName'];
		echo "<p>Hello, $firstName $lastName.</p>\n";
	} else {
		//Database problem
		echo "<p>A database error occurred while checking your login details. If this error persists, please contact your administrator.</p>";
	}
}
echo "<p>You are logged in as an administrator.</p>";
echo "<p><a href = 'addAssignmentsStep1.php'>Assignments Administration</a></p>";
echo "<p><a href = 'addQuestionsStep1.php'>Questions Administration</a></p>";

include 'footer.php';
?>