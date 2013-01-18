<?php
include 'adminAccessControl.php';
$pageTitle = "Add questions: Step 2";
include 'header.php';
//TODO: Make this page only accessible by administrators

//Verify input from previous page
$numberOfQuestionMRVs = $_POST['numberOfQuestionMRVs'];
$maxNumberOfCorrectMRVs = $_POST['maxNumberOfCorrectMRVs'];
$maxNumberOfFeedbackMRVs = $_POST['maxNumberOfFeedbackMRVs'];
$_SESSION['numberOfQuestionMRVs'] = $numberOfQuestionMRVs;
$_SESSION['maxNumberOfCorrectMRVs'] = $maxNumberOfCorrectMRVs;
$_SESSION['maxNumberOfFeedbackMRVs'] = $maxNumberOfFeedbackMRVs;
if (is_numeric($numberOfQuestionMRVs)) {
	$numberOfQuestionMRVs = floor($numberOfQuestionMRVs);
	//Limit number of question MRVs to 40
	if (($numberOfQuestionMRVs > 0) && ($numberOfQuestionMRVs < 40)) {
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
	//Get the next available question index to the be the primary key for the question to add
	$questionsQuery = $mysqli->query("SHOW TABLE STATUS WHERE name='questions'");
	$firstRow = $questionsQuery->fetch_assoc();
	$nextQuestionValue = $firstRow["Auto_increment"];
	?>
	<!--Add questions and their answers to the system-->
	<p>Use this page to add questions into the system</p>
	<form action='addQuestionsResult.php' enctype='multipart/form-data' method='post'>
	<p>Enter a description of the question here:</p>
	<p>
	<textarea name='comments' rows=7 cols=50 maxlength=500></textarea>
	</p>
	<p>
	<input type="hidden" value="<?php echo $nextQuestionValue; ?>" name="questionID"/>
	</p>
	
	<table>
	<tr>
	<?php
	for ($i = 1; $i <= $numberOfQuestionMRVs; $i++) {
	?>
	<td>
		<table>
			<tr>
				<th>Intermediate <?php echo $i; ?>:</th>
			</tr>
			<tr>
				<td>Question file:</td>
				<td>
				<!-- MAX_FILE_SIZE must precede the file input field -->
				<input type='hidden' name='MAX_FILE_SIZE' value='10000' />
				<input type='file' name='questionMRV<?php echo $i; ?>' size='14'/>
				</td>
			</tr>
			<tr>
				<td>
				Correct answer file(s):
				</td>
				<td>
			</tr>
			<?php
			//Input from these form elements will be stored in $_POST variables. They will be named in this format: "correctMRV1.1"
			for ($j = 1; $j <= $maxNumberOfCorrectMRVs; $j++) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "$j: ";
				echo "<input id='correctMRVLimit$i.$j' type='hidden' name='MAX_FILE_SIZE' value='10000' />";
				echo "<input id='correctMRV$i.$j' type='file' name='correctMRV$i.$j' size='14'/>";
				echo "</td>\n";
				echo "</tr>\n"; 
			}
			?>
			<tr>
				<td>
				Feedback file(s):
				</td>
			</tr>
			<?php
			//Input from these form elements will be stored in $_POST variables. They will be named in this format: "feedbackMRV1.1"
			for ($j = 1; $j <= $maxNumberOfFeedbackMRVs; $j++) {
				echo "<tr>\n";
				echo "<td>\n";
				echo "$j: ";
				echo "<input id='feedbackMRVLimit$i.$j' type='hidden' name='MAX_FILE_SIZE' value='10000' />";
				echo "<input id='feedbackMRV$i.$j' type='file' name='feedbackMRV$i.$j' size='14'/>";
				echo "</td>\n";
				echo "<td>\n";
				echo "Description of feedback: ";
				echo "</td>\n";
				echo "<td>\n";
				echo "<textarea id='feedbackDescription$i.$j' name='feedbackDescription$i.$j' rows=3 cols=20 maxlength=100></textarea>";
				echo "</td>\n";
				echo "</tr>\n"; 
			}
			?>
		</table>
	</td>
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