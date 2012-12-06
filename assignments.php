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
$assignmentResult = mysql_query("SELECT * FROM assignments");
$assignmentResultSize = mysql_num_rows($assignmentResult);
for ($i = 0; $i < $assignmentResultSize; $i++) {
	$currentRow = mysql_fetch_array($assignmentResult);
	echo "<tr>\n";
	if ((strtotime($currentRow['startDateTime']) > $currentTime)) {
		echo "<td>Assignment: " . $currentRow['assignmentID'] . "</td>\n";
	} else {
		echo "<td><a href = 'questionMenu.php?assignment=" . $currentRow['assignmentID']."'>Assignment: " . $currentRow['assignmentID'] . "</a></td>\n";
	}
	echo "<td>" . $currentRow['startDateTime'] . "</td>\n";
	if ((strtotime($currentRow['dueDateTime']) <= $currentTime)) {
		echo "<td>" . $currentRow['dueDateTime'] . " (LATE)</td>\n";
	} else {
		echo "<td>" . $currentRow['dueDateTime'] . "</td>\n";
	}
	echo "</tr>\n";
}
echo "</table>";
include 'footer.php';
?>