<?php
include 'db.php';
$pageTitle = "Initialize database";
include 'header.php';

$usersResult = $mysqli->query("TRUNCATE TABLE submittedMRVs");
$usersResult = $mysqli->query("DROP TABLE submittedMRVs");
$usersResult = $mysqli->query("TRUNCATE TABLE feedbackMRVs");
$usersResult = $mysqli->query("DROP TABLE feedbackMRVs");
$usersResult = $mysqli->query("TRUNCATE TABLE correctMRVs");
$usersResult = $mysqli->query("DROP TABLE correctMRVs");
$usersResult = $mysqli->query("TRUNCATE TABLE questionMRVs");
$usersResult = $mysqli->query("DROP TABLE questionMRVs");
$usersResult = $mysqli->query("TRUNCATE TABLE submittedAnswers");
$usersResult = $mysqli->query("DROP TABLE submittedAnswers");
$usersResult = $mysqli->query("TRUNCATE TABLE assignmentQuestions");
$usersResult = $mysqli->query("DROP TABLE assignmentQuestions");
$usersResult = $mysqli->query("TRUNCATE TABLE questions");
$usersResult = $mysqli->query("DROP TABLE questions");
$usersResult = $mysqli->query("TRUNCATE TABLE assignments");
$usersResult = $mysqli->query("DROP TABLE assignments");
$usersResult = $mysqli->query("TRUNCATE TABLE admins");
$usersResult = $mysqli->query("DROP TABLE admins");
$usersResult = $mysqli->query("TRUNCATE TABLE users");
$usersResult = $mysqli->query("DROP TABLE users");

$createUsersTable = "CREATE TABLE users (uID INT(8) NOT NULL PRIMARY KEY, firstName VARCHAR(65) NOT NULL, lastName VARCHAR(65) NOT NULL, controlGroup VARCHAR(1) NOT NULL)";

$createAdminsTable = "CREATE TABLE admins (aID INT(8) NOT NULL PRIMARY KEY, firstName VARCHAR(65) NOT NULL, lastName VARCHAR(65) NOT NULL, passwordHash CHAR(64) NOT NULL)";

$createAssignmentsTable = "CREATE TABLE assignments (assignmentID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, startDateTime DATETIME NOT NULL, dueDateTime DATETIME NOT NULL)";

$createQuestionsTable = "CREATE TABLE questions (questionID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, description VARCHAR(500) NOT NULL)";

$createAssignmentQuestionsTable = "CREATE TABLE assignmentQuestions (assignmentQuestionID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, assignmentID INT NOT NULL, questionID INT NOT NULL, assignmentIndex INT NOT NULL, controlGroup VARCHAR(1) NOT NULL, FOREIGN KEY (questionID) REFERENCES questions(questionID), FOREIGN KEY (assignmentID) REFERENCES assignments(assignmentID))";

$createSubmittedAnswersTable = "CREATE TABLE submittedAnswers (questionID INT NOT NULL, uID INT NOT NULL, attemptNumber INT NOT NULL, description VARCHAR(500), timeToComplete INT NOT NULL, status VARCHAR(20) NOT NULL, FOREIGN KEY (questionID) references questions(questionID), FOREIGN KEY (uID) references users(uID), PRIMARY KEY (questionID, uID, attemptNumber))";

$createQuestionMRVsTable = "CREATE TABLE questionMRVs (questionID INT NOT NULL, questionIndex INT NOT NULL, filepath VARCHAR(255) NOT NULL, PRIMARY KEY (questionID, questionIndex), FOREIGN KEY (questionID) references questions(questionID))";

$createCorrectMRVsTable = "CREATE TABLE correctMRVs (cMRVID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, questionID INT NOT NULL, questionIndex INT NOT NULL, filepath VARCHAR(255) NOT NULL, FOREIGN KEY (questionID, questionIndex) references questionMRVs(questionID, questionIndex))";

$createFeedbackMRVsTable = "CREATE TABLE feedbackMRVs (fMRVID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, questionID INT NOT NULL, questionIndex INT NOT NULL, filepath VARCHAR(255) NOT NULL, feedback VARCHAR(255) NOT NULL, FOREIGN KEY (questionID, questionIndex) references questionMRVs(questionID, questionIndex))";

$createSubmittedMRVsTable = "CREATE TABLE submittedMRVs (questionID INT NOT NULL, questionIndex INT NOT NULL, uID INT NOT NULL, filepath VARCHAR(255) NOT NULL, attemptNumber INT NOT NULL, FOREIGN KEY (questionID, questionIndex) references questionMRVs(questionID, questionIndex), FOREIGN KEY (uID) references users(uID), PRIMARY KEY (questionID, questionIndex, uID, attemptNumber))";

//Run a bunch of sql to initialize the database
if($mysqli->query($createUsersTable)) {
	echo  "<p>OK: users table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create users table.</p>\n";
}
if($mysqli->query($createAdminsTable)) {
	echo  "<p>OK: admins table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create admins table.</p>\n";
}
if($mysqli->query($createAssignmentsTable)) {
	echo  "<p>OK: assignments table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create assignments table.</p>\n";
}
if($mysqli->query($createQuestionsTable)) {
	echo  "<p>OK: questions table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create questions table.</p>\n";
}
if($mysqli->query($createAssignmentQuestionsTable)) {
	echo  "<p>OK: assignmentQuestions table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create assignmentQuestions table.</p>\n";
}
if($mysqli->query($createSubmittedAnswersTable)) {
	echo  "<p>OK: submittedAnswers table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create submittedAnswers table.</p>\n";
}
if($mysqli->query($createQuestionMRVsTable)) {
	echo  "<p>OK: questionMRVs table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create questionMRVs table.</p>\n";
}
if($mysqli->query($createCorrectMRVsTable)) {
	echo "<p>OK: correctMRVs table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create correctMRVs table.</p>\n";
}
if($mysqli->query($createFeedbackMRVsTable)) {
	echo "<p>OK: feedbackMRVs table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create feedbackMRVs table.</p>\n";
}
if($mysqli->query($createSubmittedMRVsTable)) {
	echo "<p>OK: submittedMRVs table created.</p>\n";
} else {
	echo "<p>ERROR: Couldn't create submittedMRVs table.</p>\n";
}

$result = $mysqli->query("INSERT INTO users (uID, firstName, lastName, controlGroup) VALUES (007, 'James', 'Bond', 'a')");
if($result) {
	echo "<p>OK: inserted James Bond, 007.</p>\n";
} else {
	echo "<p>ERROR: Couldn't insert first user.</p>\n";
}

$myPassword = hash('sha256', 'jackierocks');
$result = $mysqli->query("INSERT INTO admins (aID, firstName, lastName, passwordHash) VALUES (11110000, 'Admin', 'Istrator', '$myPassword')");
if($result) {
	echo "<p>OK: inserted Admin Istrator.</p>\n";
} else {
	echo "<p>ERROR: Couldn't insert first admin.</p>\n";
}

$usersResult = $mysqli->query("SELECT * FROM users");
$usersResultSize = $usersResult->num_rows;
echo $usersResultSize;
for ($i = 0; $i < $usersResultSize; $i++) {
	$currentUser = $usersResult->fetch_assoc();
	$firstName = $currentUser['firstName'];
	echo "<p>$firstName</p>\n";
}

$adminsResult = $mysqli->query("SELECT * FROM admins");
$adminsResultSize = $adminsResult->num_rows;
echo $adminsResultSize;
for ($i = 0; $i < $adminsResultSize; $i++) {
	$currentAdmin = $adminsResult->fetch_assoc();
	$firstName = $currentAdmin['firstName'];
	$password = $currentAdmin['passwordHash'];
	echo "<p>$firstName</p>\n";
	echo "<p>$password</p>\n";
}

/*
echo "<p><b>TABLES:</b></p>\n";
$tablesResult = $mysqli->query("SHOW TABLES");
$tablesResultSize = $tablesResult->num_rows;
for ($i = 0; $i < $tablesResultSize; $i++) {
	$currentTable = $tablesResult->fetch_array();
	$tableName = $currentTable[0];
	echo "<p>$tableName</p>\n";
}
*/
include 'footer.php';
?>