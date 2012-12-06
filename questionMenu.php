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
		echo "</tr>\n";
		//Print out the list of questions for this assignment
		for ($i = 0; $i < $assignmentQuestionsResultSize; $i++) {
			$currentRow = mysql_fetch_array($assignmentQuestionsResult);
			echo "<tr>\n";
			echo "<td><a href = 'questionDisplay.php?q=" . $currentRow['questionID'] . "'>" . $currentRow['assignmentIndex'] . "</a></td>\n";
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