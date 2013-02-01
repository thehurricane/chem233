<?php
include 'adminAccessControl.php';
$pageTitle = "Questions Administration";
include 'header.php';

$questionsQueryResult = $mysqli->query("SELECT * FROM questions");
$questionsQueryResultSize = $questionsQueryResult->num_rows;
?>
<p>Use this page to create, delete, or modify questions.</p>
<p><b><a href = "addQuestionsStep1.php">ADD A NEW QUESTION</a></b></p>
<table>
<tr>
<th>ID</th>
<th>Description</th>
<th>Number of Intermediates</th>
<th>Used by Assignment(s)</th>
</tr>
<?php
for ($i = 1; $i <= $questionsQueryResultSize; $i++) {
	$nextQuestionRow = $questionsQueryResult->fetch_assoc();
	$questionID = $nextQuestionRow['questionID'];;
	$assignmentQuestionsQueryResult = $mysqli->query("SELECT * FROM assignmentQuestions WHERE questionID = '$questionID'");
	$assignmentQuestionsQueryResultSize = $assignmentQuestionsQueryResult->num_rows;
	$questionMRVsQueryResult = $mysqli->query("SELECT * FROM questionMRVs WHERE questionID = '$questionID'");
	$questionMRVsQueryResultSize = $questionMRVsQueryResult->num_rows;
?>
<tr>
<td><?php echo $questionID; ?></td>
<td><?php echo stripslashes($nextQuestionRow['description']); ?></td>
<td><?php echo $questionMRVsQueryResultSize; ?></td>
<td>
<?php
	if ($assignmentQuestionsQueryResultSize == 0) {
		echo "None";
	}
	for ($j = 1; $j <= $assignmentQuestionsQueryResultSize; $j++) {
		$nextAssignmentQuestionRow = $assignmentQuestionsQueryResult->fetch_assoc();
		if ($j == 1) {
			echo $nextAssignmentQuestionRow['assignmentID'];
		} else {
			echo ", " . $nextAssignmentQuestionRow['assignmentID'];
		}
	}
?>
</td>
</tr>
<?php
}
?>
<tr>
<th><a href = "addQuestionsStep1.php">NEW</a></th>
</tr>
</table>
<?php
include 'footer.php';
?>