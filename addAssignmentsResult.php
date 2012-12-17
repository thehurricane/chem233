<?php
include 'accesscontrol.php';
$pageTitle = "Add questions";
include 'header.php';

//Debug statements
echo "<p>POST:</p>\n";
print_r($_POST);
echo "<p>SESSION:</p>\n";
print_r($_SESSION);
$assignmentID = $_POST["assignmentID"];
//Escape the description
//TODO: Don't hardcode the dates
$startDate = strtotime("1 December 2012");
$startDate = date("Y-m-d H:i:s", $startDate);
$dueDate = strtotime("1 December 2016");
$dueDate = date("Y-m-d H:i:s", $dueDate);
$result = mysql_query("INSERT INTO assignments (startDateTime, dueDateTime) VALUES ('$startDate', '$dueDate');");
if(!$result) {
	echo "<p>Error: could not make a new assignment.</p>\n";
} else {
	echo  "<p>Assignment created: " . $assignmentID . "</p>\n";
}
for ($i = 1; $i <= $_SESSION['numberOfQuestions']; $i++) {
	$dropDownBoxName = "questionSelect" . $i;
	$currentQuestionID = $_POST[$dropDownBoxName];
	echo "<p>HELLO: " . $currentQuestionID . "</p>\n";
	//$result = mysql_query("INSERT INTO assignmentQuestions (assignmentID, questionID, assignmentIndex, controlGroup) VALUES ('$assignmentID', '$dueDate');");
}
//Print out the total number of questionMRVs that were inserted.
//$result = mysql_query("SELECT * FROM questionMRVs WHERE questionID = '$questionID';");
//$resultSize = mysql_num_rows($result);
//echo "<p>" . $resultSize . " question MRVs inserted for " . $questionID . ".</p>\n";
include 'footer.php';
?>