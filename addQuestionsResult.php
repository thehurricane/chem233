<?php
include 'accesscontrol.php';
$pageTitle = "Add questions";
include 'header.php';
//TODO: Make this page only accessible by administrators

//Debug statements
//echo "<p>POST:</p>\n";
//print_r($_POST);
//echo "<p>FILES:</p>\n";
//print_r($_FILES);
$questionID = $_POST["questionID"];
//Escape the description
echo "<p>This is the question description: " . $_POST["comments"] . "</p>\n";
$questionDescription = addslashes($_POST["comments"]);
$result = mysql_query("INSERT INTO questions (description) VALUES ('$questionDescription');");
if(!$result) {
	echo "<p>Error: could not make new question.</p>\n";
} else {
	echo  "<p>Question created: " . $questionID. "</p>\n";
}
for ($i = 1; $i <= $_SESSION['numberOfQuestionMRVs']; $i++) {
	$questionMRVFilePath = "./questionMRVs/q" . $questionID . "." . $i . ".mrv";
	$fileName = "questionMRV" . $i;
	echo $_FILES[$fileName]['tmp_name'];
	if ($_FILES[$fileName]['error'] > 0) {
		echo "<p>Error: " . $_FILES[$fileName]['error'] . "</p>\n";
	} else {
		move_uploaded_file($_FILES[$fileName]['tmp_name'], $questionMRVFilePath);
		$result = mysql_query("INSERT INTO questionMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$i', '$questionMRVFilePath');");
		if(!$result) {
			echo "<p>questionMRV" . $i . ": Could not insert this file.</p>\n";
		} else {
			echo  "<p>questionMRV" . $i . ": File inserted</p>\n";
		}
	}
	
	for ($j = 1; $j <= $_SESSION['maxNumberOfCorrectMRVs']; $j++) {
		$fileName = "correctMRV" . $i . "_" . $j;
		echo "<p>_FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
		if ($_FILES[$fileName]['error'] > 0) {
			echo "<p>correctMRV" . $i . "." . $j . ": Error: " . $_FILES[$fileName]['error'] . "</p>\n";
		} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0) {
			$correctMRVFilePath = "./correctMRVs/q" . $questionID . ".correct." . $i . "." . $j . ".mrv";
			move_uploaded_file($_FILES[$fileName]['tmp_name'], $correctMRVFilePath);
			$result = mysql_query("INSERT INTO correctMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$i', '$correctMRVFilePath');");
			if(!$result) {
				echo "<p>correctMRV" . $i . "." . $j . ": Could not insert this file.</p>\n";
			} else {
				echo  "<p>correctMRV" . $i . "." . $j . ": File inserted</p>\n";
			}
		} else {
			echo "<p>correctMRV" . $i . "." . $j . ": file isn't set.</p>\n";
		}
	}
	
	for ($j = 1; $j <= $_SESSION['maxNumberOfFeedbackMRVs']; $j++) {
		$fileName = "feedbackMRV" . $i . "_" . $j;
		echo "<p>_FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
		if ($_FILES[$fileName]['error'] > 0) {
			echo "<p>feedbackMRV" . $i . "." . $j . ": Error: " . $_FILES[$fileName]['error'] . "</p>\n";
		} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0){
			$feedbackMRVFilePath = "./feedbackMRVs/q" . $questionID . ".feedback." . $i . "." . $j . ".mrv";
			move_uploaded_file($_FILES[$fileName]['tmp_name'], $feedbackMRVFilePath);
			$feedbackDescriptionID = "feedbackDescription" . $i . "_" . $j;
			echo "<p>This is the feedback description: " .  $_POST[$feedbackDescriptionID] . "</p>\n";
			$feedbackDescription = addslashes($_POST[$feedbackDescriptionID]);
			$result = mysql_query("INSERT INTO feedbackMRVs (questionID, questionIndex, filepath, description) VALUES ('$questionID', '$i', '$feedbackMRVFilePath', '$feedbackDescription');");
			if(!$result) {
				echo "<p>feedbackMRV" . $i . "." . $j . ": Could not insert this file.</p>\n";
			} else {
				echo  "<p>feedbackMRV" . $i . "." . $j . ": File inserted</p>\n";
			}
		} else {
			echo "<p>feedbackMRV" . $i . "." . $j . ": file isn't set.</p>\n";
		}
	}
}
//Print out the total number of questionMRVs that were inserted.
$result = mysql_query("SELECT * FROM questionMRVs WHERE questionID = '$questionID';");
$resultSize = mysql_num_rows($result);
echo "<p>" . $resultSize . " question MRVs inserted for " . $questionID . ".</p>\n";
//Print out the total number of correctMRVs that were inserted.
$result = mysql_query("SELECT * FROM correctMRVs WHERE questionID = '$questionID';");
$resultSize = mysql_num_rows($result);
echo "<p>" . $resultSize . " correct MRVs inserted for " . $questionID . ".</p>\n";
//Print out the total number of feedbackMRVs that were inserted.
$result = mysql_query("SELECT * FROM feedbackMRVs WHERE questionID = '$questionID';");
$resultSize = mysql_num_rows($result);
echo "<p>" . $resultSize . " feedback MRVs inserted for " . $questionID . ".</p>\n";
//Clear the files array
$_FILES = array();
//print_r($_FILES);
include 'footer.php';
?>