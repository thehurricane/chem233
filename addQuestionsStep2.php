<?php
include 'accesscontrol.php';
$pageTitle = "Add questions: Step 2";
include 'header.php';

//TODO: Make this page only accessible by administrators

//Verify input from previous page
$numberOfQuestionMRVs = $_POST['numberOfQuestionMRVs'];
$maxNumberOfCorrectMRVs = $_POST['maxNumberOfCorrectMRVs'];
$maxNumberOfFeedbackMRVs = $_POST['maxNumberOfFeedbackMRVs'];
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
	$questionsQuery = mysql_query("SHOW TABLE STATUS WHERE name='questions'");
	$firstRow = mysql_fetch_array($questionsQuery);
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
	<?php
	for ($i = 1; $i <= $numberOfQuestionMRVs; $i++) {
	?>
		<tr>
		<th>
		<?php echo $i; ?>
		</th>
		<td>
		Question file:
		</td>
		<td>
		<!-- MAX_FILE_SIZE must precede the file input field -->
		<input type='hidden' name='MAX_FILE_SIZE' value='10000' />
		<input type='file' name='questionMRV<?php echo $i; ?>' size='14'/>
		</td>
		<td>
		Number of correct answers for this intermediate:
		</td>
		<td>
		<script>
		function updateNumberOfCorrect<?php echo $i; ?>() {
			var numberSelectedCorrect = document.getElementById("correctDropDown<?php echo $i; ?>").value;
			//alert(numberSelectedCorrect);
			<?php
			for ($j = 1; $j <= $maxNumberOfCorrectMRVs; $j++) {
			?>
				if (<?php echo $j; ?> <= numberSelectedCorrect) {
					document.getElementById("correctMRV<?php echo $i . '.' . $j; ?>").hidden = false;
				} else {
					document.getElementById("correctMRV<?php echo $i . '.' . $j; ?>").hidden = true;
				}
			<?php
			}
			?>
		}
		</script>
		<select id="correctDropDown<?php echo $i; ?>" onchange="updateNumberOfCorrect<?php echo $i; ?>()">
		<?php
		for ($j = 1; $j <= $maxNumberOfCorrectMRVs; $j++) {
			if ($j == 1) {
		?>
				<option value="<?php echo $j; ?>" selected="selected"><?php echo $j; ?></option>
		<?php
			} else {
		?>
				<option value="<?php echo $j; ?>"><?php echo $j; ?></option>
		<?php
			}
		}
		?>
		</select>
		</td>
		<td>
		Correct answer file(s):
		</td>
		<td>
		<?php
		//Input from these form elements will be stored in $_POST variables. They will be named in this format: "correctMRV1.1"
		for ($j = 1; $j <= $maxNumberOfCorrectMRVs; $j++) {
			if ($j == 1) {
		?>
			<input id='correctMRVLimit<?php echo $i . "." . "$j"; ?>' type='hidden' name='MAX_FILE_SIZE' value='10000' />
			<input id='correctMRV<?php echo $i . "." . $j; ?>' type='file' name='correctMRV<?php echo $i . "." . $j; ?>' size='14'/>
		<?php
			} else {
		?>
			<input id='correctMRVLimit<?php echo $i . "." . "$j"; ?>' type='hidden' name='MAX_FILE_SIZE' value='10000' />
			<input id='correctMRV<?php echo $i . "." . $j; ?>' type='file' hidden='true' name='correctMRV<?php echo $i . "." . $j; ?>' size='14'/>
		<?php
			}
		}
		?>
		</td>
		<td>
		Number of feedback files for this intermediate:
		</td>
		<td>
		<script>
		function updateNumberOfFeedback<?php echo $i; ?>() {
			var numberSelectedFeedback = document.getElementById("feedbackDropDown<?php echo $i; ?>").value;
			//alert(numberSelectedFeedback);
			<?php
			for ($j = 1; $j <= $maxNumberOfFeedbackMRVs; $j++) {
			?>
				if (<?php echo $j; ?> <= numberSelectedFeedback) {
					document.getElementById("feedbackMRV<?php echo $i . '.' . $j; ?>").hidden = false;
				} else {
					document.getElementById("feedbackMRV<?php echo $i . '.' . $j; ?>").hidden = true;
				}
			<?php
			}
			?>
		}
		</script>
		<select id="feedbackDropDown<?php echo $i; ?>" onchange="updateNumberOfFeedback<?php echo $i; ?>()">
		<?php
		for ($j = 1; $j <= $maxNumberOfFeedbackMRVs; $j++) {
			if ($j == 1) {
		?>
				<option value="<?php echo $j; ?>" selected="selected"><?php echo $j; ?></option>
		<?php
			} else {
		?>
				<option value="<?php echo $j; ?>"><?php echo $j; ?></option>
		<?php
			}
		}
		?>
		</select>
		</td>
		<td>
		Feedback file(s):
		</td>
		<?php
		//Input from these form elements will be stored in $_POST variables. They will be named in this format: "feedbackMRV1.1"
		for ($j = 1; $j <= $maxNumberOfFeedbackMRVs; $j++) {
			if ($j == 1) {
		?>
			<td>
			<input id='feedbackMRVLimit<?php echo $i . "." . "$j"; ?>' type='hidden' name='MAX_FILE_SIZE' value='10000' />
			<input id='feedbackMRV<?php echo $i . "." . $j; ?>' type='file' name='feedbackMRV<?php echo $i . "." . $j; ?>' size='14'/>
			</td>
			<td>
			Description of feedback:
			</td>
			<td>
			<textarea name='feedbackDescription<?php echo $i . "." . "$j"; ?>' rows=7 cols=50 maxlength=500></textarea>
			</td>
		<?php
			} else {
		?>
			<td>
			<input id='feedbackMRVLimit<?php echo $i . "." . "$j"; ?>' type='hidden' name='MAX_FILE_SIZE' value='10000' />
			<input id='feedbackMRV<?php echo $i . "." . $j; ?>' type='file' hidden='true' name='feedbackMRV<?php echo $i . "." . $j; ?>' size='14'/>
			</td>
			<td>
			Description of feedback:
			</td>
			<td>
			<textarea name='feedbackDescription<?php echo $i . "." . "$j"; ?>' hidden='true' rows=7 cols=50 maxlength=500></textarea>
			</td>
		<?php
			}
		}
		?>
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
} else if ($numberOfQuestionMRVs == null) {
	echo "<h4>Input error. Invalid number of question MRVs.</h4>\n";
} else if ($maxNumberOfCorrectMRVs == null) {
	echo "<h4>Input error. Invalid maximum number of correct MRVs.</h4>\n";
} else {
	echo "<h4>Input error. Invalid maximum number of feedback MRVs.</h4>\n";
}
include 'footer.php';
?>