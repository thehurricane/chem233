<?php
include 'accesscontrol.php';
if ($_SESSION['questionAnswered'] == true) {
	$questionID = $_SESSION['question'];
	include 'moleculeClasses.php';
	//print_r($_POST);
	//print_r($_SESSION);
	
	$_SESSION['questionAnswered'] = false;
	$uID = $_SESSION['uID'];
	$questionID = $_SESSION['question'];
	$result = mysql_query($sql);
	$questionMRVsResult = mysql_query("SELECT * FROM questionMRVs WHERE questionID = $questionID");
	$questionMRVsResultSize = mysql_num_rows($questionMRVsResult);
	$maxAttemptResult = mysql_query("SELECT MAX(attemptNumber) FROM submittedMRVs WHERE questionID = $questionID AND uID = $uID;");
	if (mysql_num_rows($maxAttemptResult) == 0) {
		$currentAttemptValue = 1;
	} else {
		$maxAttemptArray = mysql_fetch_array($maxAttemptResult);
		$maxAttemptValue = $maxAttemptArray['MAX(attemptNumber)'];
		$currentAttemptValue = $maxAttemptValue + 1;
	}
	//Save the text response to the database
	$answerDescription = addslashes($_POST["comments"]);
	$timeToComplete = $_POST['timeToComplete'];
	$result = mysql_query("INSERT INTO submittedAnswers (questionID, uID, attemptNumber, description, timeToComplete) VALUES ('$questionID', '$uID', '$currentAttemptValue', '$answerDescription', '$timeToComplete')");
	if (!$result) {
		//echo "<p>Couldn't insert answer.</p>\n";
	} else {
		//echo  "<p>Answer inserted.</p>\n";
	}
	//Save all the molecules to the database
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		if (isset($_POST["mol" . $i])) {
			//Save the file to disk first
			$myFile = "./submittedMRVs/q" . $questionID . "."  . $i . "user" . $uID . ".mrv";
			$myFilePointer = fopen($myFile, 'w') or die('cannot open file');
			fwrite($myFilePointer, $_POST["mol" . $i]);
			fclose($myFilePointer);
			//Insert this file into the database
			$result = mysql_query("INSERT INTO submittedMRVs (questionID, questionIndex, uID, filepath, attemptNumber) VALUES ('$questionID', '$i', '$uID', '$myFile', '$currentAttemptValue')");
			if(!$result) {
				//echo "<p>Could not insert this file.</p>\n";
			} else {
				//echo  "<p>File inserted.</p>\n";
			}
		}
	}
	
	//Arrays to hold all the molecule data
	$submittedMoleculeArray = array();
	$correctMoleculeArray = array();
	
	//Populate the submitted array
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		//Get the next answered intermediate
		$submittedMRVFile = mysql_query( "SELECT * FROM submittedMRVs WHERE questionID = $questionID AND questionIndex = $i AND uID = $uID AND attemptNumber = $currentAttemptValue" );
		$nextRow = mysql_fetch_array($submittedMRVFile);
		//Get the file that is saved on the server by looking at the filepath
		$file = $nextRow['filepath'];
		//Create a new molecule using the Molecule class constructor (see moleculeClasses.php)
		$submittedMoleculeArray[$i-1] = new Molecule($file);
	}
	
	//Populate the correct answer array
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		//Get the next correct answer
		//TODO: Add functionality for the case where there are multiple correct answers
		$correctMRVsResult = mysql_query("SELECT * FROM correctMRVs WHERE questionID = $questionID AND questionIndex = $i");
		$nextRow = mysql_fetch_array($correctMRVsResult);
		//Get the file that is saved on the server by looking at the filepath
		$file = $nextRow[filepath];
		//Create a new molecule using the Molecule class constructor (see moleculeClasses.php)
		$correctMoleculeArray[$i-1] = new Molecule($file);
	}
	
	//Test all the files submitted
	for ($i = 0; $i < $questionMRVsResultSize; $i++) {
		$equalsResult = $submittedMoleculeArray[$i]->equals($correctMoleculeArray[$i]);
		$index = $i + 1;
		if (strcmp($equalsResult, "equal") == 0) {
			//The answer is correct
			$_SESSION['evaluationResult'][$i] = "Fine";
			//echo "<p>$index: Correct!!!</p>\n";
		} else {
			//The answer is incorrect
			//TODO: Check the submitted file against the feedbackMRVs for this question
			$_SESSION['evaluationResult'][$i] = $equalsResult;
			//Fill up the rest of the array with empty strings to the student only gets feedback on the first wrong answer and nothing after that.
			for ($i = $i + 1; $i < $questionMRVsResultSize; $i++) {
				$_SESSION['evaluationResult'][$i] = "";
			}
			//echo "<p>$index: $equalsResult</p>\n";
		}
	}
	$_SESSION['answerEvaluated'] = true;
	$questionDisplayURL = "questionDisplay.php?q=" . $_SESSION['question'];
	header("location: $questionDisplayURL");
} else {
	include 'accesscontrol.php';
	$questionID = $_SESSION['question'];
	$pageTitle = "Processing Question " . $questionID;
	include 'header.php';
	print_r($_SESSION);
	echo "<p>It doesn't look like you've answered a question. You probably came here by accident.</p>\n";
	include 'footer.php';
}
?>