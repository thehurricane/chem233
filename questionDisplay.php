<?php
/*
This page displays a given question to the user. The question displayed is specified by the GET variable "q".
*/
include 'accessControl.php';
$pageTitle = "Assignment: " . $_SESSION['assignmentID'] . ", Question: " . $_GET['q'];
include 'header.php';

//print_r($_POST);
//print_r($_SESSION);

$uID = $_SESSION['uID'];

//Set the question id as a $_SESSION variable so it can be easily called from other files in the same session
$_SESSION['question'] = $_GET['q'];
$questionID = $_GET['q'];

//Check to see if the student has already completed the assignment or given up
$completedOrGivenUp = false;
$submittedAnswersResult = $mysqli->query("SELECT * FROM submittedAnswers WHERE uID = $uID AND questionID = $questionID");
$submittedAnswersResultSize = $submittedAnswersResult->num_rows;
for ($i = 0; $i < $submittedAnswersResultSize; $i++) {
	$currentRow = $submittedAnswersResult->fetch_assoc();
	if ((strcmp($currentRow['status'], "complete") == 0) || (strcmp($currentRow['status'], "given up") == 0)) {
		$completedOrGivenUp = true;
	}
}
if (!$completedOrGivenUp) {
	echo "<p>Try to figure out what's wrong with each intermediate. Some intermediates may be already correct.</p>\n";
	echo "<p>Press the 'Submit' button (bottom of the page) when you are ready to have your answer evaluated. If you can't figure out what to do, you can press the 'Give Up' button below to view the correct mechanism. Note that once you give up, you can no longer make submissions.</p>\n";
}

//Set the start time and pass this time to the questionProcessing page
$startTime = time();

//Print out the question description
$questionsResult = $mysqli->query("SELECT * FROM questions WHERE questionID = $questionID");
$questionArray = $questionsResult->fetch_assoc();
//Strip the slashes that were inserted into the description
echo "<h5>Question Description:</h5>\n";
echo "<p>" . $questionArray['description'] . "</p>\n";

if ((isset($_SESSION['answerEvaluated'])) && ($_SESSION['answerEvaluated'] == true)) {
	echo "<p><b>Please view your feedback for each intermediate below.</b></p>\n";
} else if ($completedOrGivenUp) {
	echo "<p><b>Please view the correct mechanism below.</b></p>\n";
}

echo "<table>\n";
//Get all the questionMRV files to provide data for the MarvinSketch windows
$questionMRVsResult = $mysqli->query("SELECT * FROM questionMRVs WHERE questionID = $questionID");
$questionMRVsResultSize = $questionMRVsResult->num_rows;
//Print out a row of intermediates that just contain the original structure (for reference)
echo "<tr>\n";
for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
	//Make an empty cell to accomodate for the arrows in the table
	echo "<td></td>\n";
	echo "<th>$i</th>\n";
}
echo "</tr>\n";
echo "<tr>\n";
for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
	$currentRow = $questionMRVsResult->fetch_assoc();
	if ($i != 1 ) {
		//Make an arrow between MarvinSketch windows
		echo "<td><img src = './images/equalArrowWhite.png' alt = '-->'/></td>\n";
	} else {
		echo "<th>For reference:</th>\n";
	}
	echo "<td>";
	//echo "<td>Display the question</td>\n";
	echo"
	<script type='text/javascript' SRC='marvin/marvin.js'></script>
	<script type='text/javascript'>
	mview_begin('marvin', 200, 200);
	mview_param('detach', 'hide');
	mview_param('dispQuality', '1');
	mview_param('undetachByX', 'false');
	mview_param('menubar', 'false');
	mview_param('mol', '$currentRow[filepath]');
	mview_param('autoscale', 'true');
	mview_param('legacy_lifecycle', 'false');
	mview_end();
	</script>
	";
	echo "</td>";
}
echo "</tr>\n";

//Check if the question has been submitted for evaluation. If it hasn't then print out the questionMRVs as a starting point. If it has, then print out the most recently submittedMRVs.
//HOWEVER, if $completedOrGivenUp is true then just print out the correct MRVs in marvin sketch windows
if ($completedOrGivenUp) {
	$correctMRVsResult = $mysqli->query("SELECT * FROM correctMRVs WHERE questionID = $questionID");
	$correctMRVsResultSize = $correctMRVsResult->num_rows;
	echo "<tr>\n";
	for ($i = 1; $i <= $correctMRVsResultSize; $i++) {
		$currentRow = $correctMRVsResult->fetch_assoc();
		if ($i != 1 ) {
			//Make an arrow between MarvinSketch windows
			echo "<td><img src = './images/equalArrowWhite.png' alt = '-->'/></td>\n";
		} else {
			echo "<th>Correct mechanism:</th>\n";
		}
		echo "<td>";
		//echo "<td>Display the question</td>\n";
		echo"
		<script type='text/javascript' SRC='marvin/marvin.js'></script>
		<script type='text/javascript'>
		mview_begin('marvin', 200, 200);
		mview_param('detach', 'hide');
		mview_param('dispQuality', '1');
		mview_param('undetachByX', 'false');
		mview_param('menubar', 'false');
		mview_param('mol', '$currentRow[filepath]');
		mview_param('autoscale', 'true');
		mview_param('legacy_lifecycle', 'false');
		mview_end();
		</script>
		";
		echo "</td>";
	}
	echo "</tr>\n";
} else if ((!isset($_SESSION['answerEvaluated'])) || ($_SESSION['answerEvaluated'] != true)) {
	//A question hasn't been submitted. Display the starting point.
	$questionMRVsResult = $mysqli->query("SELECT * FROM questionMRVs WHERE questionID = $questionID");
	echo "<tr>\n";
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		$currentRow = $questionMRVsResult->fetch_assoc();
		if ($i != 1 ) {
			//Make an arrow between MarvinSketch windows
			echo "<td><img src = './images/equalArrowWhite.png' alt = '<-->'/></td>\n";
		} else {
			//Print this once
			echo "<th>Your answers:</th>\n";
		}
		$mSketchWord = "MSketch";
		$mSketchWordAndIndex = $mSketchWord.$i;
		echo "<td>";
		echo"
		<script type='text/javascript' SRC='marvin/marvin.js'></script>
		<script type='text/javascript'>
		msketch_name = '". $mSketchWordAndIndex ."';
		
		msketch_begin('marvin', 200, 200);
		msketch_param('detach', 'hide');
		msketch_param('undetachByX', 'false');
		msketch_param('menubar', 'true');
		msketch_param('mol', '$currentRow[filepath]');
		msketch_param('autoscale', 'true');
		msketch_param('legacy_lifecycle', 'false');
		msketch_end();
		</script>
		";
		echo "</td>";
	}
	echo "</tr>\n";
} else {
	//A question has been submitted. Display the submitted response, and feedback for those responses.
	$maxAttemptResult = $mysqli->query("SELECT MAX(attemptNumber) FROM submittedMRVs WHERE questionID = $questionID AND uID = $uID;");
	$maxAttemptArray = $maxAttemptResult->fetch_assoc();
	$maxAttemptValue = $maxAttemptArray['MAX(attemptNumber)'];
	
	$submittedMRVsResult = $mysqli->query("SELECT * FROM submittedMRVs WHERE questionID = $questionID AND questionIndex = $i AND uID = $uID AND attemptNumber = $maxAttemptValue");
	echo "<tr>\n";
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		$submittedMRVsResult = $mysqli->query("SELECT * FROM submittedMRVs WHERE questionID = $questionID AND questionIndex = $i AND uID = $uID AND attemptNumber = $maxAttemptValue");
		$currentRow = $submittedMRVsResult->fetch_assoc();
		if ($i != 1 ) {
			//Make an arrow between MarvinSketch windows
			echo "<td><img src = './images/equalArrowWhite.png' alt = '-->'/></td>\n";
		} else {
			//Print this once
			echo "<th>Your answers:</th>\n";
		}
		$mSketchWord = "MSketch";
		$mSketchWordAndIndex = $mSketchWord.$i;
		echo "<td>";
		echo"
		<script type='text/javascript' SRC='marvin/marvin.js'></script>
		<script type='text/javascript'>
		msketch_name = '". $mSketchWordAndIndex ."';
		
		msketch_begin('marvin', 200, 200);
		msketch_param('detach', 'hide');
		msketch_param('undetachByX', 'false');
		msketch_param('menubar', 'true');
		msketch_param('mol', '$currentRow[filepath]');
		msketch_param('autoscale', 'true');
		msketch_param('legacy_lifecycle', 'false');
		msketch_end();
		</script>
		";
		echo "</td>";
	}
	echo "</tr>\n";
	//Print out the results of processing this question (if it has been submitted)
	echo "<tr>\n";
	for ($i = 0; $i < $questionMRVsResultSize; $i++) {
		if ($i == 0) {
			//Make the header for the row only the first time
			echo "<th>Feedback:</th>\n";
			echo "<td>" . $_SESSION['evaluationResult'][$i] . "</td>\n";
		} else {
			//Make an empty cell to accomodate for the arrows in the table
			echo "<td></td>\n";
			echo "<td>" . $_SESSION['evaluationResult'][$i] . "</td>\n";
		}
	}
	echo "</tr>\n";
	$_SESSION['answerEvaluated'] = false;
}

//Code to actually extract the molecule from the applets
?>
<script type='text/javascript' src='marvin/marvin.js'></script>
<script language='JavaScript' src='marvin/js2java.js'></script>
<script type='text/javascript'>
function submitMolecules() {
<?php
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		echo "var moleculeFile = document.MSketch$i.getMol('mrv:P');\n";
		echo "document.moleculeForm.mol$i.value = moleculeFile;\n";
	}
	//$totalTime = time() - $startTime;
	echo "var currentDate = new Date();";
	echo "var currentTime = currentDate.getTime();";
	echo "currentTime = Math.ceil(currentTime/1000);";
	echo "document.moleculeForm.timeToComplete.value = currentTime - $startTime;\n";
?>
}
</script>
<?php
echo "</table>\n";
if (!$completedOrGivenUp) {
?>
	<table>
	<tr>
	<td>Enter a brief (less than 500 words) description of what happened in this reaction</td>
	</tr>
	<tr>
	<td>
	<form name='moleculeForm' method ='Post' onSubmit='JavaScript:submitMolecules()' action='questionProcessing.php'>
	<textarea name='comments' rows=7 cols=50></textarea>
	</td>
	</tr>
	<tr>
	<td>
	<?php
	//Make a bunch hidden forms that get the value of the molecule file (via the submitMolecules method)
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		echo "<input type='hidden' name='mol$i' value=''/>";
	}
	?>
	<input type='hidden' name='timeToComplete' value=''/>
	<input type='submit' name='submit' value='Submit' onClick='JavaScript:submitMolecules();'/>
	</form>
	</td>
	</tr>
	<tr>
	<td>
	<form name='Reset' method = 'Post' action = "<?php echo $_SERVER['PHP_SELF']; ?>?q=<?php echo $_SESSION['question']; ?>">
	<input type="submit" name = "Reset" value="Reset"/>
	</form>
	</td>
	</tr>
	<tr>
	<td>
	<!-- <form name='GiveUp' method = 'Post' action = "<?php echo $_SERVER['PHP_SELF']; ?>?q=<?php echo $_SESSION['question']; ?>"> -->
	<form name = 'GiveUp' method = 'Post' action = 'questionProcessing.php'>
	<input type="submit" name = "giveUp" value="Give Up"/>
	</form>
	</td>
	</tr>
	</table>
<?php
}
include 'footer.php';
?>