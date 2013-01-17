<?php
include 'db.php';
$pageTitle = "Administrator Login";
include 'header.php';
if (session_start()) {
	//echo "Session Active.";
}
//Use this line once the program is on the UBC server
//if (isset($_SESSION['aID']) || isUserValid()) {
if (isset($_SESSION['aID'])) {
	//Admin is already logged in. Do nothing.
} else if (isset($_POST['aID'])) {
	//Admin has submitted an id
	if (isset($_POST['password'])) {
		//Admin has submitted a password
		if (is_numeric ($_POST['aID'])) {
			//Admin has submitted a valid (numeric) id to be checked
			$aID = $_POST['aID'];
			$aID = $mysqli->real_escape_string($aID);
			$password = $_POST['password'];
			$password = $mysqli->real_escape_string($password);
			$password = hash('sha256', $password);
			$adminsResult = $mysqli->query("SELECT * FROM admins WHERE aID = '$aID' AND passwordHash = '$password'");
			if (!$adminsResult) {
				//Database problem
				$pageTitle = "Access Denied";
				include 'header.php';
				echo "<p>A database error occurred while checking your login details. If this error persists, please contact your administrator. To try logging in again, click <a href='./index.php'>here</a></p>";
				include 'footer.php';
				exit;
			} else if ($adminsResult->num_rows == 0) {
				//Admin doesn't exist in the database
				$pageTitle = "Access Denied";
				include 'header.php';
				echo "<p>Your user ID is incorrect, or you are not a registered admin on this site. To try logging in again, click <a href='./adminLogin.php'>here</a></p>";
				include 'footer.php';
				exit;
			} else {
				//Set this session variable so it can be accessed throughout the app
				$adminsResultArray = $adminsResult->fetch_assoc();
				$_SESSION['aID'] = $adminsResultArray['aID'];
				$firstName = $adminsResultArray['firstName'];
				echo "<p>Welcome, " . $firstName . ". You are now logged in as an administrator.</p>";
			}
		} else {
			//Admin has submitted an invalid id
			$pageTitle = "Access Denied";
			include 'header.php';
			echo "<p>Please enter a valid number. To try logging in again, click <a href='./adminLogin.php'>here</a></p>";
			include 'footer.php';
			exit;
		}
	} else {
		//Admin has submitted an invalid id
		$pageTitle = "Access Denied";
		include 'header.php';
		echo "<p>Please enter a valid number. To try logging in again, click <a href='./adminLogin.php'>here</a></p>";
		include 'footer.php';
		exit;
	}
} else {
?>
	<p>You must log in to access this area of the site. </p>
	<table>
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
			<tr>
				<td>Username:</td>
				<td><input type="text" name="aID"/></td>
			</tr>
			<tr>
				<td>Password:</td>
				<td><input type="password" name="password"/></td>
			</tr>
			<tr>
				<td><input type="submit" value="Log in" /></td>
			</tr>
		</form>
	</table>
	</form>
<?php
}
include 'footer.php';
?>