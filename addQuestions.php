<?php
include 'accesscontrol.php';
$pageTitle = "Add questions";
include 'header.php';

//Get the next available question index to the be the primary key for the question to add
$questionsQuery = mysql_query("SHOW TABLE STATUS WHERE name='questions'");
$firstRow = mysql_fetch_array($questionsQuery);
$nextQuestionValue = $firstRow["Auto_increment"];
?>
<!--Add questions and their answers to the system-->
<p>Use this page to add questions into the system</p>
<form action='addQuestionsResult.php' enctype='multipart/form-data' method='post'>
<p>Enter a description of the question here:</p>
<p>
<textarea name='comments' rows=7 cols=50 maxlength=500></textarea>
</p>
<p>
<input type="hidden" value="<?php echo $nextQuestionValue; ?>" name="questionID"/>
</p>
<table>
<?php
for ($i = 1; $i <= 8; $i++) {
?>
	<tr>
	<td>
	<?php echo $i; ?>
	</td>
	<td>
	Question file:
	</td>
	<td>
	<!-- MAX_FILE_SIZE must precede the file input field -->
	<input type='hidden' name='MAX_FILE_SIZE' value='10000' />
	<input type='file' name='questionMRV<?php echo $i; ?>' size='20'/>
	</td>
	<td>
	Correct answer file:
	</td>
	<td>
	<input type='hidden' name='MAX_FILE_SIZE' value='10000' />
	<input type='file' name='correctMRV<?php echo $i; ?>' size='20'/>
	</td>
	</tr>
<?php
}
?>
</table>
<p>
<input type='submit' value='Upload'/>
</p>
</form>
<?php
include 'footer.php';
?>