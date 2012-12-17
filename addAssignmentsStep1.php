<?php
include 'accesscontrol.php';
$pageTitle = "Add questions: Step 1";
include 'header.php';

//TODO: Make this page only accessible by administrators
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