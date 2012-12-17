<?php
include 'accesscontrol.php';
$pageTitle = "Add questions: Step 2";
include 'header.php';

//TODO: Make this page only accessible by administrators

//Verify input from previous page
$numberOfQuestions = $_POST['numberOfQuestions'];
if (is_numeric($numberOfQuestions)) {
	$numberOfQuestionMRVs = floor($numberOfQuestionMRVs);
	//Limit number of questions to 20
	if (($numberOfQuestionMRVs > 0) && ($numberOfQuestionMRVs < 20)) {
		echo "<h4>Adding question with $numberOfQuestionMRVs question MRV(s).</h4>\n";
		$_SESSION['numberOfQuestions'] = $numberOfQuestionMRVs;
	} else {
		$numberOfQuestionMRVs = null;
	}
} else {
	$numberOfQuestionMRVs = null;
}
//If the input checks out okay, populate the form. Otherwise print an error.
if ($numberOfQuestions != null) {
	//Get the next available question index to the be the primary key for the question to add
	$assignmentsQuery = mysql_query("SHOW TABLE STATUS WHERE name='assignments'");
	$firstRow = mysql_fetch_array($assignmentsQuery);
	$nextAssignmentID = $firstRow["Auto_increment"];
	?>
	<!--Add an assignment with questions to the system-->
	<p>Use this page to add assignments into the system</p>
	<form action='addAssignmentsResult.php' enctype='multipart/form-data' method='post'>
	<table>
	<tr>
	<?php
	for ($i = 1; $i <= $numberOfQuestions; $i++) {
	?>
	<th>Question <?php echo $i; ?>:</th>
	
	<?php
	}
	?>
	</tr>
	</table>
	<p>
	<input type='submit' value='Upload'/>
	</p>
	</form>
	<?php
} else if ($numberOfQuestionMRVs == null) {
	echo "<h4>Input error. Invalid number of question MRVs.</h4>\n";
} else if ($maxNumberOfCorrectMRVs == null) {
	echo "<h4>Input error. Invalid maximum number of correct MRVs.</h4>\n";
} else {
	echo "<h4>Input error. Invalid maximum number of feedback MRVs.</h4>\n";
}
include 'footer.php';
?>