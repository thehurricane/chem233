<?php
/*
This file contains the first step an administrator must take to create a new assignment.
*/
include 'adminAccessControl.php';
$pageTitle = "Add Assignments: Step 1";
include 'header.php';

?>
<form method="post" action="addAssignmentsStep2.php">
<p>Are you trying to add an assignment to the system? If so, you're in the right place. First, please answer this quick questions:</p>
<p>How many questions will this assignment consist of?</p>
<p><input type="text" name="numberOfQuestions"/></p>
<p><input type="submit" value="Go" /></p>
</form>
<?php
include 'footer.php';
?>