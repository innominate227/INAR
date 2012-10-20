<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
$qls->Security->check_auth_page('admin_surveys.php'); 
?>

<html>
<head>
<link rel="stylesheet" type="text/css" HREF="html/form.css" />
</head>
<?php
// Look in the USERGUIDE.html for more info
if ($qls->user_info['username'] != '') 
{
require_once('includes/banner2.php');
?>


<?php

	$auto_assign_updated = false;

	//process changes made
	if (isset($_POST['process_update_auto_assign'])) 
	{
		//start atomic operation
		$qls->SQL->transaction("START TRANSACTION");
	
		//update each surveys auto assign setting
		$edits_count = $_POST['surveys_count'];
		for ($edit_num = 0; $edit_num < $edits_count; $edit_num++) 
		{
			$survey_id = $_POST['survey_id_' . $edit_num];
			$survey_auto_assign = ($_POST['survey_auto_' . $edit_num] == 'true');
			$qls->Surveys->set_survey_auto_assign($survey_id, $survey_auto_assign);
		}
		
		//commit atomic operation
		$qls->SQL->transaction("COMMIT");
		
		$auto_assign_updated = true;
	}
?>



<?php

	//get info for all the surveys
	list ($survey_ids, $survey_names, $survey_auto_assigns, $survey_participant_counts, $survey_response_counts) = $qls->Survey->get_survey_info();		
?>



<?php
if ($auto_assign_updated)
{
?>
	<h3> Surveys Auto Assign Settings Updated</h3>
<?php
}
?>

<form action="admin_surveys.php" method="post">
<input type="hidden" name="process_update_auto_assign" value="yes" />
<input type="hidden" name="surveys_count" value="<?=count($survey_names)?>" />
<fieldset>
<legend> Surveys </legend>
<table style="border:1px solid black;border-collapse:collapse;">
<tr>
<th style="border:1px solid black;"><b>Name</b></th>
<th style="border:1px solid black;"><b>Auto Assign New Participants</b></th>
<th style="border:1px solid black;"><b>Participants</b></th>
<th style="border:1px solid black;"><b>Assign Participants</b></th>
<th style="border:1px solid black;"><b>Responses</b></th>
</tr>

<?php
for ($survey_num = 0; $survey_num < count($survey_names); $survey_num++) 
{
	$survey_id = $survey_ids[$survey_num];
    $survey_name = $survey_names[$survey_num];
	$survey_auto_assign = $survey_auto_assigns[$survey_num];	
	$survey_participant_count = $survey_participant_counts[$survey_num];
	$survey_response_count = $survey_response_counts[$survey_num];
?>

<tr>
<td style="border:1px solid black;"><?=$survey_name?></td>
<td style="border:1px solid black;">
<input type="hidden"    name="survey_id_<?=$survey_num?>"    value="<?=$survey_id?>" />
<input type="checkbox"  name="survey_auto_<?=$survey_num?>"  value="true"  <?php if($survey_auto_assign){ echo 'checked'; } ?> />
</td>
<td style="border:1px solid black;"><?php=$survey_participant_count?></td>
<td style="border:1px solid black;">
<a href="admin_survey_assign.php?sid=<?php=$survey_id;?>">Assign</a></td>
</td>
<td style="border:1px solid black;"><?php=$survey_response_count?></td>
</tr>

<?php	
}
?>

</table>
<br>
<input type="submit" value="Update" />
</form>
</fieldset>


</div>
<?php
}
else {
?>

You are currently not logged in.

<?php
}
?>
</html>