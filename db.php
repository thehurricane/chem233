<?php
//Database host name
$dbHostname = "localhost";
//Database user name
$dbUsername = "root";
//Database password
$dbPassword = "root";
//Database name
$dbName = "chem233applet";
//Connect to the database
//$dbConnection = mysqli_connect($dbHostname, $dbUsername, $dbPassword, $dbName);
$mysqli = new mysqli($dbHostname, $dbUsername, $dbPassword, $dbName);
if ($mysqli->connect_errno) {
    echo "Connect failed: " . $mysqli->connect_error;
    exit();
}
?>