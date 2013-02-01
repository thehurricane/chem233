<?php
/*
This page displays all the questions contained in a given assignment to the user.
*/
include 'accessControl.php';
if ($_GET['assignment'] != NULL) {
	$pageTitle = "Assignment: " . $_GET['assignment'] . " Questions";
} else {
	$pageTitle = "Assignment not found";
}
include 'header.php';
//Set the variable $assignmentID to hold the assignment selected that is stored in the URL
$assignmentID = $_GET['assignment'];
//If assignmentID is not equal null, then try to look into the database for the questions
//that belong to that assignment
if($assignmentID != NULL) {
	//Set this session variable for use in questionDisplay.php (it will be used in the title of the page)
	$_SESSION['assignmentID'] = $assignmentID;
	$assignmentQuestionsResult = $mysqli->query("SELECT * FROM assignmentQuestions WHERE assignmentID = '$assignmentID'");
	$assignmentQuestionsResultSize = $assignmentQuestionsResult->num_rows;
	if ($assignmentQuestionsResultSize == 0) {
		//There is no assignment with that ID (user must have entered in a non-existant assignment number)
		echo "<p>This assignment does not contain any questions.</p>\n";
	} else {
		//Display a table that will contain the question numbers
		echo "<table>\n";
		echo "<tr>\n";
		echo "<th>Number</th>";
		echo "<th>Description</th>";
		echo "<th>Attempts</th>";
		echo "<th>Completed?</th>";
		echo "</tr>\n";
		//Print out the list of questions for this assignment
		for ($i = 0; $i < $assignmentQuestionsResultSize; $i++) {
			$uID = $_SESSION['uID'];
			$currentAssignmentQuestion = $assignmentQuestionsResult->fetch_assoc();
			$questionID = $currentAssignmentQuestion['questionID'];
			$questionsResult = $mysqli->query("SELECT * FROM questions WHERE questionID = '$questionID'");
			$currentQuestion = $questionsResult->fetch_assoc();
			$questionDescription = $currentQuestion['description'];
			$maxAttemptResult = $mysqli->query("SELECT MAX(attemptNumber) FROM submittedAnswers WHERE questionID = $questionID AND uID = $uID;");
			$attemptValue = 0;
			if ($maxAttemptResult) {
				$maxAttemptArray = $maxAttemptResult->fetch_assoc();
				$maxAttemptValue = $maxAttemptArray['MAX(attemptNumber)'];
				if ($maxAttemptValue != null) {
					$attemptValue = $maxAttemptValue;
				}
			}
			echo "<tr>\n";
			echo "<td><a href = 'questionDisplay.php?q=" . $questionID . "'>" . $currentAssignmentQuestion['assignmentIndex'] . "</a></td>\n";
			//TODO: Limit the length of the description displayed on this page to some reasonable number of characters
			echo "<td><a href = 'questionDisplay.php?q=" . $questionID . "'>" . $questionDescription . "</a></td>\n";
			echo "<td><a href = 'questionDisplay.php?q=" . $questionID . "'>" . $attemptValue . "</a></td>\n";
			$submittedAnswersResult = $mysqli->query("SELECT * FROM submittedAnswers WHERE questionID = $questionID AND uID = $uID;");
			if (!$submittedAnswersResult) {
				$submittedAnswersResultSize = 0;
			} else {
				$submittedAnswersResultSize = $submittedAnswersResult->num_rows;
			}
			$statusFound = false;
			$status = "No";
			$j = 0;
			while (($j < $submittedAnswersResultSize) && (!$statusFound)) {
				$currentRow = $submittedAnswersResult->fetch_assoc();
				if (strcmp($currentRow['status'], "complete") == 0) {
					$statusFound = true;
					$status = "Yes";
				} else if (strcmp($currentRow['status'], "given up") == 0) {
					$statusFound = true;
					$status = "Given Up";
				}
				$j++;
			}
			echo "<td><a href = 'questionDisplay.php?q=" . $questionID . "'>" . $status . "</a></td>\n";
			echo "</tr>\n";
		}
		echo "</table>\n";
	}
} else if($assignmentID == NULL) {
	//The provided assignment doesn't exist
	echo "<p>This assignment doesn't exist</p>\n";	
}
include 'footer.php';
?>