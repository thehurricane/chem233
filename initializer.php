<?php
include 'db.php';
$pageTitle = "Initialize database";
include 'header.php';

$query = "CREATE TABLE users (
		uID INT(8) NOT NULL PRIMARY KEY,
		firstName VARCHAR(65) NOT NULL,
		lastName VARCHAR(65) NOT NULL,
		controlGroup VARCHAR(1) NOT NULL
		);";
$query .= "CREATE TABLE admins (
		aID INT(8) NOT NULL PRIMARY KEY,
		firstName VARCHAR(65) NOT NULL,
		lastName VARCHAR(65) NOT NULL,
		passwordHash CHAR(64) NOT NULL
		);";
$query .= "CREATE TABLE assignments (
		assignmentID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		startDateTime DATETIME NOT NULL,
		dueDateTime DATETIME NOT NULL
		);";
$query .= "CREATE TABLE questions (
		questionID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		description VARCHAR(500) NOT NULL
		);";
$query .= "CREATE TABLE assignmentQuestions (
		assignmentQuestionID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		assignmentID INT NOT NULL,
		questionID INT NOT NULL,
		assignmentIndex INT NOT NULL,
		controlGroup VARCHAR(1) NOT NULL,
		FOREIGN KEY (questionID) REFERENCES questions(questionID),
		FOREIGN KEY (assignmentID) REFERENCES assignments(assignmentID)
		);";
$query .= "CREATE TABLE submittedAnswers (
		questionID INT NOT NULL,
		uID INT NOT NULL,
		attemptNumber INT NOT NULL,
		description VARCHAR(500),
		timeToComplete INT NOT NULL,
		status VARCHAR(20) NOT NULL,
		FOREIGN KEY (questionID) references questions(questionID),
		FOREIGN KEY (uID) references users(uID),
		PRIMARY KEY (questionID, uID, attemptNumber)
		);";
$query .= "CREATE TABLE questionMRVs (
		questionID INT NOT NULL,
		questionIndex INT NOT NULL,
		filepath VARCHAR(255) NOT NULL,
		PRIMARY KEY (questionID, questionIndex),
		FOREIGN KEY (questionID) references questions(questionID)
		);";
$query .= "CREATE TABLE correctMRVs (
		cMRVID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		questionID INT NOT NULL,
		questionIndex INT NOT NULL,
		filepath VARCHAR(255) NOT NULL,
		FOREIGN KEY (questionID, questionIndex) references questionMRVs(questionID, questionIndex)
		);";
$query .= "CREATE TABLE feedbackMRVs (
		fMRVID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
		questionID INT NOT NULL,
		questionIndex INT NOT NULL,
		filepath VARCHAR(255) NOT NULL,
		feedback VARCHAR(255) NOT NULL,
		FOREIGN KEY (questionID, questionIndex) references questionMRVs(questionID, questionIndex)
		);";
$query .= "CREATE TABLE submittedMRVs (
		questionID INT NOT NULL,
		questionIndex INT NOT NULL,
		uID INT NOT NULL,
		filepath VARCHAR(255) NOT NULL,
		attemptNumber INT NOT NULL,
		FOREIGN KEY (questionID, questionIndex) references questionMRVs(questionID, questionIndex),
		FOREIGN KEY (uID) references users(uID),
		PRIMARY KEY (questionID, questionIndex, uID, attemptNumber)
		);";

//Run a bunch of sql to initialize the database
//TODO: Not a robust way of checking whether the query was successful. Change this later.
if($mysqli->query($query)) {
	echo "<p>Error initializing database.</p>\n";
} else {
	echo  "<p>Database initialized.</p>\n";
}
include 'footer.php';
?>