<?php
include 'adminAccessControl.php';
$pageTitle = "Add questions";
include 'header.php';

//Debug statements
//echo "<p>POST:</p>\n";
//print_r($_POST);
//echo "<p>FILES:</p>\n";
//print_r($_FILES);
$questionID = $_POST["questionID"];
//Escape the description
echo "<p>Question description: " . $_POST["comments"] . "</p>\n";
$questionDescription = addslashes($_POST["comments"]);
$result = $mysqli->query("INSERT INTO questions (description) VALUES ('$questionDescription');");
if(!$result) {
	echo "<p>ERROR: Could not make new question.</p>\n";
} else {
	echo  "<p>SUCCESS: Question created: " . $questionID . "</p>\n";
}
for ($i = 1; $i <= $_SESSION['numberOfQuestionMRVs']; $i++) {
	$questionMRVFilePath = "./questionMRVs/q" . $questionID . "." . $i . ".mrv";
	$fileName = "questionMRV" . $i;
	echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
	if ($_FILES[$fileName]['error'] > 0) {
		echo "<p>ERROR: " . $_FILES[$fileName]['error'] . "</p>\n";
	} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0){
		move_uploaded_file($_FILES[$fileName]['tmp_name'], $questionMRVFilePath);
		$result = $mysqli->query("INSERT INTO questionMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$i', '$questionMRVFilePath')");
		if(!$result) {
			echo "<p>ERROR: " . $fileName . ": Could not insert this file.</p>\n";
		} else {
			echo "<p>SUCCESS: " . $fileName . ": File inserted</p>\n";
		}
	} else {
		echo "<p>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
	}

	for ($j = 1; $j <= $_SESSION['maxNumberOfCorrectMRVs']; $j++) {
		$fileName = "correctMRV" . $i . "_" . $j;
		echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
		if ($_FILES[$fileName]['error'] > 0) {
			echo "<p>correctMRV" . $i . "." . $j . ": Error: " . $_FILES[$fileName]['error'] . "</p>\n";
		} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0) {
			$correctMRVFilePath = "./correctMRVs/q" . $questionID . ".correct." . $i . "." . $j . ".mrv";
			move_uploaded_file($_FILES[$fileName]['tmp_name'], $correctMRVFilePath);
			$result = $mysqli->query("INSERT INTO correctMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$i', '$correctMRVFilePath');");
			if(!$result) {
				echo "<p>ERROR: " . $fileName . ": Could not insert this file.</p>\n";
			} else {
				echo  "<p>SUCCESS: " . $fileName . ": File inserted</p>\n";
			}
		} else {
			echo "<p>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
		}
	}
	
	for ($j = 1; $j <= $_SESSION['maxNumberOfFeedbackMRVs']; $j++) {
		$fileName = "feedbackMRV" . $i . "_" . $j;
		echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
		if ($_FILES[$fileName]['error'] > 0) {
			echo "<p>feedbackMRV" . $i . "." . $j . ": Error: " . $_FILES[$fileName]['error'] . "</p>\n";
		} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0){
			$feedbackMRVFilePath = "./feedbackMRVs/q" . $questionID . ".feedback." . $i . "." . $j . ".mrv";
			move_uploaded_file($_FILES[$fileName]['tmp_name'], $feedbackMRVFilePath);
			$feedbackDescriptionID = "feedbackDescription" . $i . "_" . $j;
			echo "<p>This is the feedback description: " .  $_POST[$feedbackDescriptionID] . "</p>\n";
			$feedbackDescription = addslashes($_POST[$feedbackDescriptionID]);
			$result = $mysqli->query("INSERT INTO feedbackMRVs (questionID, questionIndex, filepath, feedback) VALUES ('$questionID', '$i', '$feedbackMRVFilePath', '$feedbackDescription');");
			if(!$result) {
				echo "<p>ERROR: " . $fileName . ": Could not insert this file.</p>\n";
			} else {
				echo  "<p>SUCCESS: " . $fileName . ": File inserted</p>\n";
			}
		} else {
			echo "<p>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
		}
	}
}
//Print out the total number of questionMRVs that were inserted.
$result = $mysqli->query("SELECT * FROM questionMRVs WHERE questionID = '$questionID';");
$resultSize = $result->num_rows;
echo "<p>" . $resultSize . " question MRVs inserted for " . $questionID . ".</p>\n";
//Print out the total number of correctMRVs that were inserted.
$result = $mysqli->query("SELECT * FROM correctMRVs WHERE questionID = '$questionID';");
$resultSize = $result->num_rows;
echo "<p>" . $resultSize . " correct MRVs inserted for " . $questionID . ".</p>\n";
//Print out the total number of feedbackMRVs that were inserted.
$result = $mysqli->query("SELECT * FROM feedbackMRVs WHERE questionID = '$questionID';");
$resultSize = $result->num_rows;
echo "<p>" . $resultSize . " feedback MRVs inserted for " . $questionID . ".</p>\n";
//Clear the files array
$_FILES = array();
//print_r($_FILES);
include 'footer.php';
?>