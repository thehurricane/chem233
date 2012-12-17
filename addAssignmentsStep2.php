<?php
include 'accesscontrol.php';
$pageTitle = "Add assignments: Step 2";
include 'header.php';

//TODO: Make this page only accessible by administrators

//Verify input from previous page
$numberOfQuestions = $_POST['numberOfQuestions'];
if (is_numeric($numberOfQuestions)) {
	$numberOfQuestions = floor($numberOfQuestions);
	//Limit number of questions to 20
	if (($numberOfQuestions > 0) && ($numberOfQuestions < 20)) {
		echo "<h4>Adding assignment with $numberOfQuestions questions.</h4>\n";
		$_SESSION['numberOfQuestions'] = $numberOfQuestions;
	} else {
		$numberOfQuestions = null;
	}
} else {
	$numberOfQuestions = null;
}
//If the input checks out okay, populate the form. Otherwise print an error.
if ($numberOfQuestions != null) {
	//Get the next available question index to the be the primary key for the question to add
	$assignmentsQueryResult = mysql_query("SHOW TABLE STATUS WHERE name='assignments'");
	$firstRow = mysql_fetch_array($assignmentsQueryResult);
	$nextAssignmentID = $firstRow["Auto_increment"];
	?>
	<!--Add an assignment with questions to the system-->
	<p>Use this page to add assignments into the system</p>
	<form action='addAssignmentsResult.php' enctype='multipart/form-data' method='post'>
	<p>
	<input type="hidden" value="<?php echo $nextAssignmentID; ?>" name="assignmentID"/>
	</p>
	<table>
	<?php
	for ($i = 1; $i <= $numberOfQuestions; $i++) {
		$questionsQueryResult = mysql_query("SELECT * FROM questions");
		$questionsQueryResultSize = mysql_num_rows($questionsQueryResult);
	?>
	<tr>
	<th>Question <?php echo $i; ?>:</th>
	<td>
		<select name ='questionSelect<?php echo $i; ?>'>
		<?php
		for ($j = 1; $j <= $questionsQueryResultSize; $j++) {
			$nextQuestionRow = mysql_fetch_array($questionsQueryResult);
			$nextQuestionID = $nextQuestionRow['questionID'];
			$nextQuestionDescription = stripslashes($nextQuestionRow['description']);
			if (strlen($nextQuestionDescription) > 40) {
				$nextQuestionDescription = substr($nextQuestionDescription, 0, 38) . "...";
			}
			echo "<option value='$nextQuestionID'>$nextQuestionID: $nextQuestionDescription</option>\n";
		}
		?>
		</select>
	</td>
	</tr>
	<?php
	}
	?>
	</table>
	<p>
	<input type='submit' value='Upload'/>
	</p>
	</form>
	<?php
} else {
	echo "<h4>Input error. Invalid number of questions.</h4>\n";
}
include 'footer.php';
?>