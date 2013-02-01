<?php
/*
This page displays all the assignments in the system to the administrator.
*/
include 'adminAccessControl.php';
$pageTitle = "Assignments Administration";
include 'header.php';
//TODO: Add functionality to modify and delete assignments
$assignmentsQueryResult = $mysqli->query("SELECT * FROM assignments");
$assignmentsQueryResultSize = $assignmentsQueryResult->num_rows;
?>
<p>Use this page to create, delete, or modify assignments.</p>
<p><b><a href = "addAssignmentsStep1.php">ADD A NEW ASSIGNMENT</a></b></p>
<table>
<tr>
<th>ID</th>
<th>Opened</th>
<th>Due</th>
<th>Number of questions</th>
</tr>
<?php
for ($i = 1; $i <= $assignmentsQueryResultSize; $i++) {
	$nextAssignmentRow = $assignmentsQueryResult->fetch_assoc();
	$assignmentID = $nextAssignmentRow['assignmentID'];
	$assignmentQuestionsQueryResult = $mysqli->query("SELECT * FROM assignmentQuestions WHERE assignmentID = '$assignmentID'");
	$assignmentQuestionsQueryResultSize = $assignmentQuestionsQueryResult->num_rows;
?>
<tr>
<td><?php echo $assignmentID; ?></td>
<td><?php echo $nextAssignmentRow['startDateTime']; ?></td>
<td><?php echo $nextAssignmentRow['dueDateTime']; ?></td>
<td><?php echo $assignmentQuestionsQueryResultSize; ?></td>
</tr>
<?php
}
?>
<tr>
<th><a href = "addAssignmentsStep1.php">NEW</a></th>
</tr>
</table>
<?php
include 'footer.php';
?>