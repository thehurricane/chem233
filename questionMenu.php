<?php
include 'accesscontrol.php';
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
	//TODO: Change this once the database has been restructured
	$assignmentQuestionsResult = mysql_query("SELECT * FROM assignmentQuestions WHERE assignmentID = '$assignmentID'");
	$assignmentQuestionsResultSize = mysql_num_rows($assignmentQuestionsResult);
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
			$currentAssignmentQuestion = mysql_fetch_array($assignmentQuestionsResult);
			$questionID = $currentAssignmentQuestion['questionID'];
			$questionsResult = mysql_query("SELECT * FROM questions WHERE questionID = '$questionID'");
			$currentQuestion = mysql_fetch_array($questionsResult);
			$questionDescription = $currentQuestion['description'];
			$maxAttemptResult = mysql_query("SELECT MAX(attemptNumber) FROM submittedAnswers WHERE questionID = $questionID AND uID = $uID;");
			if (mysql_num_rows($maxAttemptResult) == 0) {
				$attemptValue = 0;
			} else {
				$maxAttemptArray = mysql_fetch_array($maxAttemptResult);
				$maxAttemptValue = $maxAttemptArray['MAX(attemptNumber)'];
				$attemptValue = $maxAttemptValue;
			}
			echo "<tr>\n";
			echo "<td><a href = 'questionDisplay.php?q=" . $questionID . "'>" . $currentAssignmentQuestion['assignmentIndex'] . "</a></td>\n";
			echo "<td><a href = 'questionDisplay.php?q=" . $questionID . "'>" . $questionDescription . "</a></td>\n";
			echo "<td><a href = 'questionDisplay.php?q=" . $questionID . "'>" . $attemptValue . "</a></td>\n";
			$submittedAnswersResult = mysql_query("SELECT * FROM submittedAnswers WHERE questionID = $questionID AND uID = $uID;");
			$submittedAnswersResultSize = mysql_num_rows($submittedAnswersResult);
			$statusFound = false;
			$status = "No";
			$j = 0;
			while (($j < $submittedAnswersResultSize) && (!$statusFound)) {
				$currentRow = mysql_fetch_array($submittedAnswersResult);
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
			//TODO: Add a data entry that contains the question's description (or at least a shortened version of it)
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