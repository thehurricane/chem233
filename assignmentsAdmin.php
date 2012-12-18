<?php
include 'accesscontrol.php';
$pageTitle = "Assignments Administration";
include 'header.php';
//TODO: Make this page only accessible by administrators

$assignmentsQueryResult = mysql_query("SELECT * FROM assignments");
$assignmentsQueryResultSize = mysql_num_rows($assignmentsQueryResult);
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
	$nextAssignmentRow = mysql_fetch_array($assignmentsQueryResult);
	$assignmentID = $nextAssignmentRow['assignmentID'];
	$assignmentQuestionsQueryResult = mysql_query("SELECT * FROM assignmentQuestions WHERE assignmentID = '$assignmentID'");
	$assignmentQuestionsQueryResultSize = mysql_num_rows($assignmentQuestionsQueryResult);
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