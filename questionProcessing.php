<?php
/*
This page should never actually be displayed to the user. It just evaluates their answers and then goes back to questionDisplay.
*/
include 'accessControl.php';
if (isset($_POST['submit'])) {
	//Set this variable to be false because we are evaluating the question that was submitted.
	$questionID = $_SESSION['question'];
	include 'moleculeClasses.php';
	//print_r($_POST);
	//print_r($_SESSION);
	
	$uID = $_SESSION['uID'];
	$questionID = $_SESSION['question'];
	$questionMRVsResult = $mysqli->query("SELECT * FROM questionMRVs WHERE questionID = $questionID");
	$questionMRVsResultSize = $questionMRVsResult->num_rows;
	$maxAttemptResult = $mysqli->query("SELECT MAX(attemptNumber) FROM submittedMRVs WHERE questionID = $questionID AND uID = $uID;");
	if ($maxAttemptResult->num_rows == 0) {
		$currentAttemptValue = 1;
	} else {
		$maxAttemptArray = $maxAttemptResult->fetch_assoc();
		$maxAttemptValue = $maxAttemptArray['MAX(attemptNumber)'];
		$currentAttemptValue = $maxAttemptValue + 1;
	}
	//Save all the molecules to the database.
	//-Create a separate submittedMRV record for every molecule file submitted.
	//-Save each file on the server in a directory called "submittedMRVs".
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		if (isset($_POST["mol" . $i])) {
			//Save the file to disk first
			$myFile = "./submittedMRVs/q" . $questionID . "."  . $i . ".user" . $uID . ".attempt" . $currentAttemptValue . ".mrv";
			$myFilePointer = fopen($myFile, 'w') or die('cannot open file');
			fwrite($myFilePointer, $_POST["mol" . $i]);
			fclose($myFilePointer);
			//Insert this file into the database
			$result = $mysqli->query("INSERT INTO submittedMRVs (questionID, questionIndex, uID, filepath, attemptNumber) VALUES ('$questionID', '$i', '$uID', '$myFile', '$currentAttemptValue')");
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
		$submittedMRVFile = $mysqli->query( "SELECT * FROM submittedMRVs WHERE questionID = $questionID AND questionIndex = $i AND uID = $uID AND attemptNumber = $currentAttemptValue" );
		$nextRow = $submittedMRVFile->fetch_assoc();
		//Get the file that is saved on the server by looking at the filepath
		$file = $nextRow['filepath'];
		//Create a new molecule using the Molecule class constructor (see moleculeClasses.php)
		$submittedMoleculeArray[$i-1] = new Molecule($file);
	}
	
	//TODO: Add functionality for the case where there are multiple correct answers
	//Populate the correct answer array
	for ($i = 1; $i <= $questionMRVsResultSize; $i++) {
		//Get the next correct answer
		$correctMRVsResult = $mysqli->query("SELECT * FROM correctMRVs WHERE questionID = $questionID AND questionIndex = $i");
		$nextRow = $correctMRVsResult->fetch_assoc();
		//Get the file that is saved on the server by looking at the filepath
		$file = $nextRow['filepath'];
		//Create a new molecule using the Molecule class constructor (see moleculeClasses.php)
		$correctMoleculeArray[$i-1] = new Molecule($file);
	}
	
	//Evaluate all the files submitted
	for ($i = 0; $i < $questionMRVsResultSize; $i++) {
		$index = $i + 1;
		$correctResult = $submittedMoleculeArray[$i]->equals($correctMoleculeArray[$i]);
		if (strcmp($correctResult, "equal") == 0) {
			//The answer is correct
			$_SESSION['evaluationResult'][$i] = "Fine";
			//echo "<p>$index: Correct!!!</p>\n";
		} else {
			//The answer is incorrect
			//Check the submitted file against the feedbackMRVs for this question
			$alternateFeedbackFound = false;
			$feedbackMRVsResult = $mysqli->query("SELECT * FROM feedbackMRVs WHERE questionID = $questionID AND questionIndex = $index");
			$feedbackMRVsResultSize = $feedbackMRVsResult->num_rows;
			$j = 0;
			while (($j < $feedbackMRVsResultSize) && ($alternateFeedbackFound == false)) {
				$nextRow = $feedbackMRVsResult->fetch_assoc();
				//Get the file that is saved on the server by looking at the filepath
				$file = $nextRow['filepath'];
				//Create a new molecule using the Molecule class constructor (see moleculeClasses.php)
				$feedbackMolecule = new Molecule($file);
				$feedbackResult = $submittedMoleculeArray[$i]->equals($feedbackMolecule);
				if (strcmp($feedbackResult, "equal") == 0) {
					$_SESSION['evaluationResult'][$i] = $nextRow['feedback'];
					$alternateFeedbackFound = true;
				}
				$j++;
			}
			if ($alternateFeedbackFound == false) {
				$_SESSION['evaluationResult'][$i] = $correctResult;
			}
			//Fill up the rest of the array with empty strings to the student only gets feedback on the first wrong answer and nothing after that.
			for ($i = $i + 1; $i < $questionMRVsResultSize; $i++) {
				$_SESSION['evaluationResult'][$i] = "";
			}
			//echo "<p>$index: $correctResult</p>\n";
		}
	}
	//Check to see if the last intermediate evaluated to "Fine". If it did, then that means all the previous intermediates were also fine, thus the submission is correct.
	$answerDescription = addslashes($_POST["comments"]);
	$timeToComplete = $_POST['timeToComplete'];
	//Insert the submittedAnswer into the database with status "complete" if the submission is correct or "incomplete" if the submission is not correct
	if (strcmp($_SESSION['evaluationResult'][$questionMRVsResultSize - 1], "Fine") == 0) {
		$mysqli->query("INSERT INTO submittedAnswers (questionID, uID, attemptNumber, description, timeToComplete, status) VALUES ('$questionID', '$uID', '$currentAttemptValue', '$answerDescription', '$timeToComplete', 'complete')");
	} else {
		$mysqli->query("INSERT INTO submittedAnswers (questionID, uID, attemptNumber, description, timeToComplete, status) VALUES ('$questionID', '$uID', '$currentAttemptValue', '$answerDescription', '$timeToComplete', 'incomplete')");
	}
	$_SESSION['answerEvaluated'] = true;
	$questionDisplayURL = "questionDisplay.php?q=" . $_SESSION['question'];
	header("location: $questionDisplayURL");
} else if ($_POST['giveUp'] == true) {
	$uID = $_SESSION['uID'];
	$questionID = $_SESSION['question'];
	$maxAttemptResult = $mysqli->query("SELECT MAX(attemptNumber) FROM submittedMRVs WHERE questionID = $questionID AND uID = $uID;");
	if ($maxAttemptResult->num_rows == 0) {
		$currentAttemptValue = 1;
	} else {
		$maxAttemptArray = $maxAttemptResult->fetch_assoc();
		$maxAttemptValue = $maxAttemptArray['MAX(attemptNumber)'];
		$currentAttemptValue = $maxAttemptValue + 1;
	}
	//Insert the submittedAnswer into the database with status "given up"
	$mysqli->query("INSERT INTO submittedAnswers (questionID, uID, attemptNumber, description, timeToComplete, status) VALUES ('$questionID', '$uID', '$currentAttemptValue', '', '0', 'given up')");
	$questionDisplayURL = "questionDisplay.php?q=" . $_SESSION['question'];
	header("location: $questionDisplayURL");
} else {
	$questionID = $_SESSION['question'];
	$pageTitle = "Processing Question " . $questionID;
	include 'header.php';
	//print_r($_SESSION);
	echo "<p>It doesn't look like you've answered a question. You probably came here by accident.</p>\n";
	include 'footer.php';
}
?>