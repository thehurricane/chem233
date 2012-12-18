<?php
include 'accesscontrol.php';
$pageTitle = "Questions Administration";
include 'header.php';
//TODO: Make this page only accessible by administrators

$questionsQueryResult = mysql_query("SELECT * FROM questions");
$questionsQueryResultSize = mysql_num_rows($questionsQueryResult);
?>
<p>Use this page to create, delete, or modify questions.</p>
<table>
<tr>
<th>ID</th>
<th>Description</th>
</tr>
<?php
for ($i = 1; $i <= $questionsQueryResultSize; $i++) {
	$nextAssignmentRow = mysql_fetch_array($questionsQueryResult);
	$questionID = $nextAssignmentRow['assignmentID'];
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
</table>
<?php
include 'footer.php';
?>