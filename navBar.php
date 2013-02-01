<div id="navBar">
<ul>
<li><a href="assignments.php">assignments</a></li>
<li><a href="otherResources.php">resources</a></li>
<li><a href = "help.php">help</a></li>
<li><a href = "logout.php">log out</a></li>
<?php
if (isset($_SESSION['aID'])) {
?>
<li><a href = "adminAssignments.php">assignments admin</a></li>
<li><a href = "adminQuestions.php">questions admin</a></li>
<li><a href = "emptyMarvinSketch.php">marvin sketch</a></li>
<?php
}
?>
</ul>
</div>