<?php
include 'adminAccessControl.php';
$pageTitle = "Add questions: Step 2";
include 'header.php';

function unsetSessionVariables() {
	unset($_SESSION['numberOfQuestionMRVs']);
	unset($_SESSION['maxNumberOfCorrectMRVs']);
	unset($_SESSION['maxNumberOfFeedbackMRVs']);
	echo "<p>Try again by clicking <a href='./addQuestionsStep1.php'>here</a></p>\n";
}

//Verify input from previous page
$numberOfQuestionMRVs = $_POST['numberOfQuestionMRVs'];
$maxNumberOfCorrectMRVs = $_POST['maxNumberOfCorrectMRVs'];
$maxNumberOfFeedbackMRVs = $_POST['maxNumberOfFeedbackMRVs'];
$_SESSION['numberOfQuestionMRVs'] = $numberOfQuestionMRVs;
$_SESSION['maxNumberOfCorrectMRVs'] = $maxNumberOfCorrectMRVs;
$_SESSION['maxNumberOfFeedbackMRVs'] = $maxNumberOfFeedbackMRVs;
$_SESSION['questionIndex'] = 1;
if (is_numeric($numberOfQuestionMRVs)) {
	$numberOfQuestionMRVs = floor($numberOfQuestionMRVs);
	//Limit number of question MRVs to 40
	if (($numberOfQuestionMRVs > 0) && ($numberOfQuestionMRVs < 20)) {
		echo "<h4>Adding question with $numberOfQuestionMRVs question MRV(s).</h4>\n";
	} else {
		$numberOfQuestionMRVs = null;
	}
} else {
	$numberOfQuestionMRVs = null;
}
if (is_numeric($maxNumberOfCorrectMRVs)) {
	$maxNumberOfCorrectMRVs = floor($maxNumberOfCorrectMRVs);
	//Limit number of correct MRVs to 20
	if (($maxNumberOfCorrectMRVs > 0) && ($maxNumberOfCorrectMRVs < 20)) {
		echo "<h4>Max number of correct MRVs is $maxNumberOfCorrectMRVs.</h4>\n";
	} else {
		$maxNumberOfCorrectMRVs = null;
	}
} else {
	$maxNumberOfCorrectMRVs = null;
}
if (is_numeric($maxNumberOfFeedbackMRVs)) {
	$maxNumberOfFeedbackMRVs = floor($maxNumberOfFeedbackMRVs);
	//Limit number of feedback MRVs to 20
	if (($maxNumberOfFeedbackMRVs > 0) && ($maxNumberOfFeedbackMRVs < 20)) {
		echo "<h4>Max number of feedback MRVs is $maxNumberOfFeedbackMRVs.</h4>\n";
	} else {
		$maxNumberOfFeedbackMRVs = null;
	}
} else {
	$maxNumberOfFeedbackMRVs = null;
}
//If the input checks out okay, populate the form. Otherwise print an error.
if (($numberOfQuestionMRVs != null) && ($maxNumberOfCorrectMRVs != null) && ($maxNumberOfFeedbackMRVs != null)) {
	$i = 1;
	//Get the next available question index to the be the primary key for the question to add
	$questionsQuery = $mysqli->query("SHOW TABLE STATUS WHERE name='questions'");
	$firstRow = $questionsQuery->fetch_assoc();
	$nextQuestionValue = $firstRow["Auto_increment"];
	?>
	<p>If this is okay, add the question description below.</p>
	<form action='addQuestionsStep3.php' enctype='multipart/form-data' method='post'>
	<p>Enter a description of the question here:</p>
	<p>
	<textarea name='questionDescription' rows=7 cols=50 maxlength=500></textarea>
	</p>
	<p>
	<input type="hidden" value="<?php echo $nextQuestionValue; ?>" name="questionID"/>
	</p>
	<p>
	<input type='submit' value='Upload'/>
	</p>
	</form>
	<?php
} else if ($numberOfQuestionMRVs == null) {
	echo "<h4 class='error'>Input error. Invalid number of question MRVs.</h4>\n";
	unsetSessionVariables();
} else if ($maxNumberOfCorrectMRVs == null) {
	echo "<h4 class='error'>Input error. Invalid maximum number of correct MRVs.</h4>\n";
	unsetSessionVariables();
} else {
	echo "<h4 class='error'>Input error. Invalid maximum number of feedback MRVs.</h4>\n";
	unsetSessionVariables();
}
include 'footer.php';
?>