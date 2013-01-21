<div id="navBar">
<ul>
<li><a href="assignments.php">assignments</a></li>
<li><a href="otherResources.php">resources</a></li>
<li><a href = "help.php">help</a></li>
<li><a href = "logout.php">log out</a></li>
<?php
if (isset($_SESSION['aID'])) {
?>
<li><a href = "addAssignmentsStep1.php">assignments admin</a></li>
<li><a href = "addQuestionsStep1.php">questions admin</a></li>
<?php
}
?>
</ul>
</div>