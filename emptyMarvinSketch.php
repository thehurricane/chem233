<?php
/*
This page isn't REALLY necessary, but is a convenience in case the administrator wants to have marvin sketch immediately accessible.
*/
include 'adminAccessControl.php';
$pageTitle = "Empty Marvin Sketch Window";
include 'header.php';
?>
<p>
Use this tool to create MRV files for questions.
</p>
<p>
<script type='text/javascript' src='marvin/marvin.js'></script>
<script type='text/javascript'>
msketch_begin('marvin', 300, 300);
msketch_param('detach', 'hide');
msketch_param('undetachByX', 'false');
msketch_param('menubar', 'true');
msketch_param('autoscale', 'true');
msketch_param('legacy_lifecycle', 'false');
msketch_end();
</script>
</p>
<?php
include 'footer.php';
?>