<?php
/*
This file is included in every non-admin page to prevent unauthorized use of the app.
*/
include 'db.php';
require("connectBB.php");
if (session_start()) {
	//echo "Session Active.";
}
//Use this line once the program is on the UBC server
//if (isset($_SESSION['uID']) || isUserValid()) {
if (isset($_SESSION['uID'])) {
	//User is already logged in. Do nothing.
} else if (isset($_POST['uID'])) {
	//User has submitted a userID
	if (is_numeric ($_POST['uID'])) {
		//User has submitted a valid (numeric) id to be checked
		$uID = $_POST['uID'];
		$uID = $mysqli->real_escape_string($uID);
		$usersResult = $mysqli->query("SELECT * FROM users WHERE uID = $uID");
		if (!$usersResult) {
			//Database problem
			$pageTitle = "Access Denied";
			include 'header.php';
			echo "<p>A database error occurred while checking your login details. If this error persists, please contact your administrator. To try logging in again, click <a href='./index.php'>here</a></p>";
			include 'footer.php';
			exit;
		} else if ($usersResult->num_rows == 0) {
			//User doesn't exist in the database
			$pageTitle = "Access Denied";
			include 'header.php';
			echo "<p>Your user ID is incorrect, or you are not a registered user on this site. To try logging in again, click <a href='./index.php'>here</a></p>";
			include 'footer.php';
			exit;
		} else {
			//Set this session variable so it can be accessed throughout the app
			$usersResultArray = $usersResult->fetch_assoc();
			$_SESSION['uID'] = $usersResultArray['uID'];
		}
	} else {
		//User has submitted an invalid id
		$pageTitle = "Access Denied";
		include 'header.php';
		echo "<p>Please enter a valid number. To try logging in again, click <a href='./index.php'>here</a></p>";
		include 'footer.php';
		exit;
	}
} else {
	//User is not logged in
	$pageTitle = "Log In";
	include 'header.php';
?>
	<p>You must log in to access this area of the site. </p>
	<table>
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<tr>
				<td>UBC Student ID:</td>
				<td><input type="text" name="uID"/></td>
			</tr>
			<tr>
				<td><input type="submit" value="Log in" /></td>
			</tr>
		</form>
	</table>
	</form>
<?php
	include 'footer.php';
	exit;
}
?>