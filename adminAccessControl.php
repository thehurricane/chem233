<?php
include 'db.php';
require("connectBB.php");
if (session_start()) {
	//echo "Session Active.";
}
//Use this line once the program is on the UBC server
//if (isset($_SESSION['uID']) || isUserValid()) {
if (isset($_SESSION['aID'])) {
	//Admin is already logged in. Do nothing.
} else {
	//Admin is not logged in
	$pageTitle = "Access Denied";
	include 'header.php';
?>
	<p>You must log in to access this area of the site. </p>
	<p>Click <a href='./adminLogin.php'>here</a> to log in.</p>;
<?php
	include 'footer.php';
	exit;
}
?>