<?php
include 'accesscontrol.php';
$pageTitle = "Questions Administration";
include 'header.php';
//TODO: Make this page only accessible by administrators

$questionsQueryResult = mysql_query("SELECT * FROM questions");
$questionsQueryResultSize = mysql_num_rows($questionsQueryResult);
?>
<p>Use this page to create, delete, or modify questions.</p>
<p><b><a href = "addQuestionsStep1.php">ADD A NEW QUESTION</a></b></p>
<table>
<tr>
<th>ID</th>
<th>Description</th>
<th>Number of Intermediates</th>
<th>Using Assignments</th>
</tr>
<?php
for ($i = 1; $i <= $questionsQueryResultSize; $i++) {
	$nextQuestionRow = mysql_fetch_array($questionsQueryResult);
	$questionID = $nextQuestionRow['questionID'];;
	$assignmentQuestionsQueryResult = mysql_query("SELECT * FROM assignmentQuestions WHERE questionID = '$questionID'");
	$assignmentQuestionsQueryResultSize = mysql_num_rows($assignmentQuestionsQueryResult);
	$questionMRVsQueryResult = mysql_query("SELECT * FROM questionMRVs WHERE questionID = '$questionID'");
	$questionMRVsQueryResultSize = mysql_num_rows($questionMRVsQueryResult);
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
		$nextAssignmentQuestionRow = mysql_fetch_array($assignmentQuestionsQueryResult);
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