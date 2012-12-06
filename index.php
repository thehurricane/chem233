<?php
include 'accesscontrol.php';
//Take the user to the assignments page if they are logged in
header("location:assignments.php");
$pageTitle = "Log In";
include 'header.php';
?>
<?php
include 'footer.php';
?>