<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
if ($qls->user_info['username'] != '')
require_once('includes/banner2.php');
else
require_once('includes/banner.php');
$qls->Security->check_auth_page('surveys.php'); ?>



<?php
// Look in the USERGUIDE.html for more info
if ($qls->user_info['username'] != '') {
?>

<?php

	//get info for all the surveys this user is a partcipant in	
	list ($survey_ids, $survey_names, $survey_tokens, $survey_completes) = $qls->Surveys->get_survey_info_for_user($qls->user_info['id']);
		
?>

<div>
<h1>Surveys:</h1>

</div>
<table style="text-align:center;margin:auto">


<?php
for ($survey_num = 0; $survey_num < count($survey_names); $survey_num++) 
{
	$survey_id = $survey_ids[$survey_num];
    $survey_name = $survey_names[$survey_num];		
	$survey_token = $survey_tokens[$survey_num];
	$survey_complete = $survey_completes[$survey_num];
?>

	<tr><td>
	
	<?php
	//only show link if user has not already completed the survey
	if ($survey_complete == '')
	{	
	?>
		<a href="../limesurvey/index.php/<?=$survey_id?>/tk-<?=$survey_token?>">  <?=$survey_name?> </a>		
	<?php
	}
	else
	{
	?>
		<?=$survey_name?> - Completed on <?=$survey_complete?>
	<?php
	}
	?>
			
	</td></tr>

<?php
}
?>
	
	
</table>
<br>
<br>
<br>


<?php
}
else {
?>
<div style="text-align:center; width:80%;height:100px; margins:10px 0;padding:10px background-color:LightCyan; color:red;font-size:22px; border:2px solid black;">
<p>You are currently not logged in. Log in to view available surveys<p>
<p> If you haven't registered, signup <a href= "register.php"> here </a> </p>
</div>
<?php
}
?>