<?php
include 'accesscontrol.php';
$pageTitle = "Add questions: Step 1";
include 'header.php';
//TODO: Make this page only accessible by administrators

?>
<form method="post" action="addQuestionsStep2.php">
<p>Are you trying to add some questions to the system? If so, you're in the right place. First, please answer these quick questions:</p>
<p>How many MRVs will this question consist of? (how many steps are there in the mechanism)</p>
<p><input type="text" name="numberOfQuestionMRVs"/></p>
<p>What is the largest number of correct MRVs there will be for any given question? (look at the number of correct MRVs there are for each question, and put in the maximum number here)</p>
<p><input type="text" name="maxNumberOfCorrectMRVs"/></p>
<p>What is the largest number of feedback MRVs there will be for any given question? (look at the number of feedback MRVs there are for each question, and put in the maximum number here)</p>
<p><input type="text" name="maxNumberOfFeedbackMRVs"/></p>
<p><input type="submit" value="Go" /></p>
</form>
<?php
include 'footer.php';
?>