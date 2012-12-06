<?php
include 'accesscontrol.php';
$pageTitle = "Add questions";
include 'header.php';

print_r($_POST);
$questionID = $_POST["questionID"];
//Escape the description
$questionDescription = addslashes($_POST["comments"]);
$result = mysql_query("INSERT INTO questions (description) VALUES ('$questionDescription');");
if(!$result) {
	echo "<p>Could not make new question.</p>\n";
} else {
	echo  "<p>Question created</p>\n";
}
//TODO: Set the limit of this for loop to something not hard coded
for ($i = 1; $i <= 8; $i++) {
	$questionMRVFilePath = "./questionMRVs/q" . $questionID . "." . $i . ".mrv";
	$fileName = "questionMRV" . $i;
	echo $_FILES[$fileName]['tmp_name'];
	if ($_FILES[$fileName]['error'] > 0) {
		echo "<p>Error: " . $_FILES[$fileName]['error'] . "</p>\n";
	} else {
		move_uploaded_file($_FILES[$fileName]['tmp_name'], $questionMRVFilePath);
		$result = mysql_query("INSERT INTO questionMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$i', '$questionMRVFilePath');");
		if(!$result) {
			echo "<p>Could not insert this file.</p>\n";
		} else {
			echo  "<p>File inserted</p>\n";
		}
	}
	
	$correctMRVFilePath = "./correctMRVs/q" . $questionID . ".correct." . $i . ".mrv";
	$fileName = "correctMRV" . $i;
	echo $_FILES[$fileName]['tmp_name'];
	if ($_FILES[$fileName]['error'] > 0) {
		echo "<p>Error: " . $_FILES[$fileName]['error'] . "</p>\n";
	} else {
		move_uploaded_file($_FILES[$fileName]['tmp_name'], $correctMRVFilePath);
		$result = mysql_query("INSERT INTO correctMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$i', '$correctMRVFilePath');");
		if(!$result) {
			echo "<p>Could not insert this file.</p>\n";
		} else {
			echo  "<p>File inserted</p>\n";
		}
	}
}
$result = mysql_query("SELECT * FROM questionMRVs WHERE questionID = '$questionID';");
$resultSize = mysql_num_rows($result);
echo "<p>" . $resultSize . " intermediates inserted for " . $questionID . ".</p>\n";
$_FILES = array();
//print_r($_FILES);
include 'footer.php';
?>