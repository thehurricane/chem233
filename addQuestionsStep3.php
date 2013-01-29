<?php
include 'adminAccessControl.php';
$pageTitle = "Add questions: Step 2";
//$internalStyle = "table, th, tr, td {border: 2px solid white;}";
include 'header.php';

//Check if the administrator came to this page from step 2
if (isset($_POST["questionID"])) {
	//The admin came here from Step 2
	$questionID = $_POST["questionID"];
	$_SESSION['questionID'] = $questionID;
	//Escape the description
	$questionDescription = addslashes($_POST["questionDescription"]);
	$result = $mysqli->query("INSERT INTO questions (description) VALUES ('$questionDescription');");
	if(!$result) {
		echo "<p class='error'>ERROR: Could not make new question. Contact your administrator.</p>\n";
		include 'footer.php';
		exit;
	} else {
		echo  "<p class='success'>SUCCESS: Question created (ID = " . $questionID . ")</p>\n";
		echo "<p>Question description: " . $_POST["questionDescription"] . "</p>\n";
	}
	$_SESSION['questionIndex'] = 1;
} else {
	//The admin came here from Step 3
	//Debug statements
	//echo "<p>POST:</p>\n";
	//print_r($_POST);
	//echo "<p>FILES:</p>\n";
	//print_r($_FILES);
	$questionID = $_SESSION['questionID'];
	$questionIndex = $_SESSION['questionIndex'];
	
	//Add the question MRV file to the system
	//The location where the file will be saved on the server
	$questionMRVFilePath = "./questionMRVs/q" . $questionID . "." . $questionIndex . ".mrv";
	
	//The file name as it is stored in the $_FILES variable
	$fileName = "questionMRV" . $questionIndex;
	
	//Print out the temp file name as it exists on the server
	//echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
	
	//If the file doesn't exist, then there is a problem. TODO: Handle this error better
	if ($_FILES[$fileName]['error'] > 0) {
		echo "<p class='error'>ERROR: " . $_FILES[$fileName]['error'] . "</p>\n";
	} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0){
		//Save the file onto disk
		move_uploaded_file($_FILES[$fileName]['tmp_name'], $questionMRVFilePath);
		//Insert the file's record into the database
		$result = $mysqli->query("INSERT INTO questionMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$questionIndex', '$questionMRVFilePath')");
		//Check to make sure the database write was successful
		if(!$result) {
			//A database error occurred
			//echo "<p class='error'>ERROR: " . $fileName . ": Could not insert this file.</p>\n";
		} else {
			//The file was added okay.
			//echo "<p class='success'>SUCCESS: Question MRV number " . $questionIndex . " was inserted okay.</p>\n";
		}
	} else {
		//The file wasn't found
		//echo "<p class='error'>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
	}
	
	//Add the correct MRV file(s) to the system
	for ($j = 1; $j <= $_SESSION['maxNumberOfCorrectMRVs']; $j++) {
		$fileName = "correctMRV" . $questionIndex . "_" . $j;
		//echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
		if ($_FILES[$fileName]['error'] > 0) {
			//The file wasn't found
			//echo "<p class='error'>correctMRV" . $questionIndex . "." . $j . ": Error: " . $_FILES[$fileName]['error'] . "</p>\n";
		} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0) {
			//The location where the file will be saved on the server
			$correctMRVFilePath = "./correctMRVs/q" . $questionID . ".correct." . $questionIndex . "." . $j . ".mrv";
			move_uploaded_file($_FILES[$fileName]['tmp_name'], $correctMRVFilePath);
			$result = $mysqli->query("INSERT INTO correctMRVs (questionID, questionIndex, filepath) VALUES ('$questionID', '$questionIndex', '$correctMRVFilePath');");
			if(!$result) {
				//A database error occurred
				//echo "<p class='error'>ERROR: Correct MRV number " . $questionIndex . "." . $j . " could not be inserted.</p>\n";
			} else {
				//The file was added okay.
				//echo "<p class='success'>SUCCESS: Correct MRV number " . $questionIndex . "." . $j . " was inserted okay.</p>\n";
			}
		} else {
			//The file wasn't found
			//echo "<p class='error'>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
		}
	}
	
	//Add the feedback MRV file(s) to the system
	for ($j = 1; $j <= $_SESSION['maxNumberOfFeedbackMRVs']; $j++) {
		$fileName = "feedbackMRV" . $questionIndex . "_" . $j;
		//echo "<p>" . $fileName . ": _FILES[fileName][tmp_name]: " . $_FILES[$fileName]['tmp_name'] . "</p>\n";
		if ($_FILES[$fileName]['error'] > 0) {
			//The file wasn't found
			//echo "<p class='error'>feedbackMRV" . $questionIndex . "." . $j . ": Error: " . $_FILES[$fileName]['error'] . "</p>\n";
		} else if (strcmp($_FILES[$fileName]['tmp_name'], "") != 0) {
			//The location where the file will be saved on the server
			$feedbackMRVFilePath = "./feedbackMRVs/q" . $questionID . ".feedback." . $questionIndex . "." . $j . ".mrv";
			move_uploaded_file($_FILES[$fileName]['tmp_name'], $feedbackMRVFilePath);
			$feedbackDescriptionID = "feedbackDescription" . $questionIndex . "_" . $j;
			//echo "<p>This is the feedback description: " .  $_POST[$feedbackDescriptionID] . "</p>\n";
			$feedbackDescription = addslashes($_POST[$feedbackDescriptionID]);
			$result = $mysqli->query("INSERT INTO feedbackMRVs (questionID, questionIndex, filepath, feedback) VALUES ('$questionID', '$questionIndex', '$feedbackMRVFilePath', '$feedbackDescription');");
			if(!$result) {
				//A database error occurred
				//echo "<p class='error'>ERROR: " . $fileName . ": Could not insert this file.</p>\n";
			} else {
				//The file was added okay.
				//echo  "<p class='success'>SUCCESS: " . $fileName . ": File inserted</p>\n";
			}
		} else {
			//The file wasn't found
			//echo "<p class='error'>ERROR: Couldn't insert " . $fileName . ": " . $_FILES[$fileName]['error'] . "</p>\n";
		}
	}
	
	$uploadError = false;
	//Check the number of questionMRVs that were inserted (this should be exactly 1)
	$result = $mysqli->query("SELECT * FROM questionMRVs WHERE questionID = '$questionID' AND questionIndex = '$questionIndex';");
	$resultSize = $result->num_rows;
	if ($resultSize == 1) {
		echo "<p class='success'>SUCCESS: questionMRV added okay.</p>\n";
	} else {
		echo "<p class='error'>ERROR: a questionMRV was not uploaded.</p>\n";
		$uploadError = true;
	}
	//Check the number of correctMRVs that were inserted (this should be 1 or more)
	$result = $mysqli->query("SELECT * FROM correctMRVs WHERE questionID = '$questionID' AND questionIndex = '$questionIndex';");
	$resultSize = $result->num_rows;
	if ($resultSize > 0) {
		echo "<p class='success'>SUCCESS: " . $resultSize . " correctMRVs added okay.</p>\n";
	} else {
		echo "<p class='error'>ERROR: no correctMRVs were added.</p>\n";
		$uploadError = true;
	}
	//Check the number of feedbackMRVs that were inserted (this could be 0 or more)
	$result = $mysqli->query("SELECT * FROM feedbackMRVs WHERE questionID = '$questionID' AND questionIndex = '$questionIndex';");
	$resultSize = $result->num_rows;
	if ($resultSize > 0) {
		echo "<p class='success'>SUCCESS: " . $resultSize . " feedbackMRVs added okay.</p>\n";
	} else {
		echo "<p>No feedbackMRVs were added.</p>\n";
	}
	//Clear the files array
	$_FILES = array();
	//print_r($_FILES);
	//If there was an upload error, reload the current intermediate's upload page.
	if (!$uploadError) {
		$_SESSION['questionIndex'] = $_SESSION['questionIndex'] + 1;
	}
}

if ($_SESSION['questionIndex'] <= $_SESSION['numberOfQuestionMRVs']) {
	$i = $_SESSION['questionIndex'];
	//Get the next available question index to the be the primary key for the question to add
	$questionsQuery = $mysqli->query("SHOW TABLE STATUS WHERE name='questions'");
	$firstRow = $questionsQuery->fetch_assoc();
	$nextQuestionValue = $firstRow["Auto_increment"];
	?>
	<p>Add the MRV files for this step.</p>
	<form action='addQuestionsStep3.php' enctype='multipart/form-data' method='post'>
	<h3>Intermediate <?php echo $i; ?>:</h3>
	<table>
		<tr>
			<th>Question file:</th>
		</tr>
		<tr>
			<td>
			</td>
			<td>
			</td>
			<td>
			<!-- MAX_FILE_SIZE must precede the file input field -->
			<input type='hidden' name='MAX_FILE_SIZE' value='10000' />
			<input type='file' name='questionMRV<?php echo $i; ?>' size='14'/>
			</td>
		</tr>
		<tr>
		<td>
		</tr>
		<tr>
			<th>
			Correct answer file(s):
			</th>
		</tr>
		<?php
		//Input from these form elements will be stored in $_POST variables. They will be named in this format: "correctMRV1.1"
		for ($j = 1; $j <= $_SESSION['maxNumberOfCorrectMRVs']; $j++) {
			echo "<tr>\n";
			echo "<td>\n";
			echo "</td>\n";
			echo "<td>\n";
			echo "$j: ";
			echo "</td>\n";
			echo "<td>\n";
			echo "<input id='correctMRVLimit$i.$j' type='hidden' name='MAX_FILE_SIZE' value='10000' />";
			echo "<input id='correctMRV$i.$j' type='file' name='correctMRV$i.$j' size='14'/>";
			echo "</td>\n";
			echo "</tr>\n"; 
		}
		?>
		<tr>
			<th>
			Feedback file(s):
			</th>
		</tr>
		<?php
		//Input from these form elements will be stored in $_POST variables. They will be named in this format: "feedbackMRV1.1"
		for ($j = 1; $j <= $_SESSION['maxNumberOfFeedbackMRVs']; $j++) {
			echo "<tr>\n";
			echo "<td></td>\n";
			echo "<td>\n";
			echo "$j: ";
			echo "</td>\n";
			echo "<td>\n";
			echo "<input id='feedbackMRVLimit$i.$j' type='hidden' name='MAX_FILE_SIZE' value='10000' />";
			echo "<input id='feedbackMRV$i.$j' type='file' name='feedbackMRV$i.$j' size='14'/>";
			echo "</td>\n";
			echo "</tr>\n";
			echo "<tr>\n";
			echo "<td>Description of feedback:</td>\n";
			echo "<td></td>\n";
			echo "<td>\n";
			echo "<textarea id='feedbackDescription$i.$j' name='feedbackDescription$i.$j' rows=3 cols=20 maxlength=100></textarea>";
			echo "</td>\n";
			echo "</tr>\n"; 
		}
		?>
	</table>
	<p>
	<input type='submit' value='Next'/>
	</p>
	</form>
	<?php
} else {
	//At this point, check to make sure the all the files for the questions were added properly.
	//Print out the total number of questionMRVs that were inserted.
	$result = $mysqli->query("SELECT * FROM questionMRVs WHERE questionID = '$questionID';");
	$resultSize = $result->num_rows;
	echo "<p>" . $resultSize . " question MRVs inserted for " . $questionID . ".</p>\n";
	//Print out the total number of correctMRVs that were inserted.
	$result = $mysqli->query("SELECT * FROM correctMRVs WHERE questionID = '$questionID';");
	$resultSize = $result->num_rows;
	echo "<p>" . $resultSize . " correct MRVs inserted for " . $questionID . ".</p>\n";
	//Print out the total number of feedbackMRVs that were inserted.
	$result = $mysqli->query("SELECT * FROM feedbackMRVs WHERE questionID = '$questionID';");
	$resultSize = $result->num_rows;
	echo "<p>" . $resultSize . " feedback MRVs inserted for " . $questionID . ".</p>\n";
	unset($_SESSION['numberOfQuestionMRVs']);
	unset($_SESSION['maxNumberOfCorrectMRVs']);
	unset($_SESSION['maxNumberOfFeedbackMRVs']);
	unset($_SESSION['questionIndex']);
}
include 'footer.php';
?>