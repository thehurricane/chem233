<?php
include 'adminAccessControl.php';
$pageTitle = "Add questions";
include 'header.php';

//Debug statements
//echo "<p>POST:</p>\n";
//print_r($_POST);
//echo "<p>FILES:</p>\n";
//print_r($_FILES);
$questionID = $_SESSION['questionID'];
$questionIndex = $_SESSION['questionIndex'];

//Add the question MRV file to the system
//The location where the file will be saved on the server
$questionMRVFilePath = "./questionMRVs/q" . $questionID . "." . $questionIndex . ".mrv";
//The file name as it is stored in the $_FILES variable
$fileName = "questionMRV" . $questionIndex;
echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
//If the file doesn't exist, then there is a problem. TODO: Handle this error better
if ($_FILES[$fileName]['error'] > 0) {
	echo "<p>ERROR: " . $_FILES[$fileName]['error'] . "</p>\n";
} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0){
	//Save the file onto disk
	move_uploaded_file($_FILES[$fileName]['tmp_name'], $questionMRVFilePath);
	//Insert the file's record into the database
	$result = $mysqli->query("INSERT INTO questionMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$questionIndex', '$questionMRVFilePath')");
	//Check to make sure the database write was successful
	if(!$result) {
		echo "<p>ERROR: " . $fileName . ": Could not insert this file.</p>\n";
	} else {
		echo "<p>SUCCESS: " . $fileName . ": File inserted</p>\n";
	}
} else {
	echo "<p>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
}

//Add the correct MRV file(s) to the system
for ($j = 1; $j <= $_SESSION['maxNumberOfCorrectMRVs']; $j++) {
	$fileName = "correctMRV" . $questionIndex . "_" . $j;
	echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
	if ($_FILES[$fileName]['error'] > 0) {
		echo "<p>correctMRV" . $questionIndex . "." . $j . ": Error: " . $_FILES[$fileName]['error'] . "</p>\n";
	} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0) {
		//The location where the file will be saved on the server
		$correctMRVFilePath = "./correctMRVs/q" . $questionID . ".correct." . $questionIndex . "." . $j . ".mrv";
		move_uploaded_file($_FILES[$fileName]['tmp_name'], $correctMRVFilePath);
		$result = $mysqli->query("INSERT INTO correctMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$questionIndex', '$correctMRVFilePath');");
		if(!$result) {
			echo "<p>ERROR: " . $fileName . ": Could not insert this file.</p>\n";
		} else {
			echo  "<p>SUCCESS: " . $fileName . ": File inserted</p>\n";
		}
	} else {
		echo "<p>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
	}
}

//Add the feedback MRV file(s) to the system
for ($j = 1; $j <= $_SESSION['maxNumberOfFeedbackMRVs']; $j++) {
	$fileName = "feedbackMRV" . $questionIndex . "_" . $j;
	echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
	if ($_FILES[$fileName]['error'] > 0) {
		echo "<p>feedbackMRV" . $questionIndex . "." . $j . ": Error: " . $_FILES[$fileName]['error'] . "</p>\n";
	} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0) {
		//The location where the file will be saved on the server
		$feedbackMRVFilePath = "./feedbackMRVs/q" . $questionID . ".feedback." . $questionIndex . "." . $j . ".mrv";
		move_uploaded_file($_FILES[$fileName]['tmp_name'], $feedbackMRVFilePath);
		$feedbackDescriptionID = "feedbackDescription" . $questionIndex . "_" . $j;
		echo "<p>This is the feedback description: " .  $_POST[$feedbackDescriptionID] . "</p>\n";
		$feedbackDescription = addslashes($_POST[$feedbackDescriptionID]);
		$result = $mysqli->query("INSERT INTO feedbackMRVs (questionID, questionIndex, filepath, feedback) VALUES ('$questionID', '$questionIndex', '$feedbackMRVFilePath', '$feedbackDescription');");
		if(!$result) {
			echo "<p>ERROR: " . $fileName . ": Could not insert this file.</p>\n";
		} else {
			echo  "<p>SUCCESS: " . $fileName . ": File inserted</p>\n";
		}
	} else {
		echo "<p>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
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