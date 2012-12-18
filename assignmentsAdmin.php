<?php
include 'accesscontrol.php';
$pageTitle = "Assignments Administration";
include 'header.php';
//TODO: Make this page only accessible by administrators

$assignmentsQueryResult = mysql_query("SELECT * FROM assignments");
$assignmentsQueryResultSize = mysql_num_rows($assignmentsQueryResult);
?>
<p>Use this page to create, delete, or modify assignments.</p>
<table>
<tr>
<th>ID</th>
<th>Opened</th>
<th>Due</th>
</tr>
<?php
for ($i = 1; $i <= $assignmentsQueryResultSize; $i++) {
	$nextAssignmentRow = mysql_fetch_array($assignmentsQueryResult);
?>
<tr>
<td><?php echo $nextAssignmentRow['assignmentID']; ?></td>
<td><?php echo $nextAssignmentRow['startDateTime']; ?></td>
<td><?php echo $nextAssignmentRow['dueDateTime']; ?></td>
</tr>
<?php
}
?>
</table>
<?php
include 'footer.php';
?>