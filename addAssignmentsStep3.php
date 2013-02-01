<?php
/*
This file contains the third and final step an administrator must take to create a new assignment.
*/
include 'adminAccessControl.php';
$pageTitle = "Add Assignments: Step 3";
include 'header.php';

if (isset($_SESSION['numberOfQuestions'])) {
	$assignmentID = $_POST["assignmentID"];
	//Default start date is December 1, 2012
	if (isset($_POST['startDate'])) {
		$startDate = $_POST["startDate"];
		$startDate = strtotime($startDate);
		$startDate = date("Y-m-d H:i:s", $startDate);
	} else {
		$startDate = strtotime("1 December 2012");
		$startDate = date("Y-m-d H:i:s", $startDate);
	}
	//Default due date is December 1, 2016
	if (isset($_POST['dueDate'])) {
		$dueDate = $_POST["dueDate"];
		$dueDate = strtotime($dueDate);
		$dueDate = date("Y-m-d H:i:s", $dueDate);
	} else {
		$dueDate = strtotime("1 December 2016");
		$dueDate = date("Y-m-d H:i:s", $dueDate);
	}
	//Insert the assignment into the database
	$result = $mysqli->query("INSERT INTO assignments (startDateTime, dueDateTime) VALUES ('$startDate', '$dueDate');");
	if(!$result) {
		echo "<p class='error'>Error: could not make a new assignment.</p>\n";
	} else {
		echo "<p class='success'>Assignment created: " . $assignmentID . "</p>\n";
		echo "<p>Open at: " . $startDate . "</p>\n";
		echo "<p>Due on: " . $dueDate . "</p>\n";
	}
	for ($i = 1; $i <= $_SESSION['numberOfQuestions']; $i++) {
		$dropDownBoxName = "questionSelect" . $i;
		$currentQuestionID = $_POST[$dropDownBoxName];
		$result = $mysqli->query("INSERT INTO assignmentQuestions (assignmentID, questionID, assignmentIndex, controlGroup) VALUES ('$assignmentID', '$currentQuestionID', '$i', 'a');");
		if(!$result) {
			echo "<p class='error'>question number " . $i . ": Could not add this question to the assignment.</p>\n";
		} else {
			echo "<p class='success'>question number " . $i . ": Added question to the assignment.</p>\n";
		}
	}
	unset($_SESSION['numberOfQuestions']);
} else {
	//This error will most likely be displayed if the user reloads this page.
	echo "<p class='error'>Can't add the same assignment again.</p>\n";
	echo "<p>To add a new assignment, click <a href='./addAssignmentsStep1.php'>here</a>.</p>\n";
}
include 'footer.php';
?>