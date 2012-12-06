<?php
//Database host name
$dbHostname = "localhost:8889";
//Database user name
$dbUsername = "root";
//Database password
$dbPassword = "root";
//Connect to the database
$dbConnection = mysql_connect($dbHostname, $dbUsername, $dbPassword);
if (!$dbConnection) {
	die('Could not connect: ' . mysql_error());
}
//If the name of the database changes, change it here
mysql_select_db("chem233applet", $dbConnection);
?>