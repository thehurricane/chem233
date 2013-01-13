<?php
include 'accesscontrol.php';
$pageTitle = "Assignments";
include 'header.php';
?>
<table>
<tr>
<th>Assignment</th>
<th>Opened</th>
<th>Due</th>
</tr>
<?php
$currentTime = time();
//TODO: Change this to accomodate for the control group type once the database is restructured
$assignmentResult = $mysqli->query("SELECT * FROM assignments");
$assignmentResultSize = $assignmentResult->num_rows;
for ($i = 0; $i < $assignmentResultSize; $i++) {
	$currentRow = $assignmentResult->fetch_assoc();
	echo "<tr>\n";
	if ((strtotime($currentRow['startDateTime']) > $currentTime)) {
		echo "<td>Assignment: " . $currentRow['assignmentID'] . "</td>\n";
	} else {
		echo "<td><a href = 'questionMenu.php?assignment=" . $currentRow['assignmentID']."'>Assignment: " . $currentRow['assignmentID'] . "</a></td>\n";
	}
	echo "<td><a href = 'questionMenu.php?assignment=" . $currentRow['assignmentID']."'>" . $currentRow['startDateTime'] . "</a></td>\n";
	if ((strtotime($currentRow['dueDateTime']) <= $currentTime)) {
		echo "<td><a href = 'questionMenu.php?assignment=" . $currentRow['assignmentID']."'>" . $currentRow['dueDateTime'] . " (LATE)</a></td>\n";
	} else {
		echo "<td><a href = 'questionMenu.php?assignment=" . $currentRow['assignmentID']."'>" . $currentRow['dueDateTime'] . "</a></td>\n";
	}
	echo "</tr>\n";
}
echo "</table>";
include 'footer.php';
?>