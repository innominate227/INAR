<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
$qls->Security->check_auth_page('admin_survey_assign.php'); ?>



<?php
// Look in the USERGUIDE.html for more info
if ($qls->user_info['username'] != '') 
{
require_once('includes/banner2.php');
?>

<?php
	$sid = $_GET['sid'];
	$results_language = 'en';
	
	$surveys_lang_result = $qls->SQL->query("SELECT `surveyls_title` FROM `{$qls->config['lime_sql_prefix']}surveys_languagesettings` WHERE `surveyls_survey_id`='{$sid}' AND `surveyls_language`='{$results_language}'");					
	$surveys_lang_row = $qls->SQL->fetch_array($surveys_lang_result);
	if ($surveys_lang_row == null)
	{
		$survey_name = 'title not translated to selected language';
	}
	else
	{
		$survey_name = $surveys_lang_row['surveyls_title'];		
	}

	$paticipants_updated = false;
	
	//process user assigned changes
	if (isset($_POST['process_assign_participants'])) 
	{	
		//start atomic operation
		$qls->SQL->transaction("START TRANSACTION");
		
		//look at each participant assigned/unassigned
		$edits_count = $_POST['assign_participant_count'];
		for ($edit_num = 0; $edit_num < $edits_count; $edit_num++) 
		{
			$participant_id = $_POST['participant_id_' . $edit_num];
			$participant_assigned = $_POST['participant_assigned_' . $edit_num];
			
			if ($participant_assigned == 'true')
			{
				$qls->Surveys->assign_to_survey($participant_id, $sid);
			}
			else
			{
				$qls->Surveys->unassign_to_survey($participant_id, $sid);
			}
		}
		
		//commit atomic operation
		$qls->SQL->transaction("COMMIT");
		
		$paticipants_updated = true;
	}
	
	

	//lime id of all participants searched for
	$participant_ids = array();	
	//name of all participants searched for
	$participant_names = array();
	//has the participant completed the survey
	$participant_completeds = array();
	//is the participant assign to the survey
	$participant_assigneds = array();

	//process search made
	if (isset($_POST['process_search_users'])) 
	{
		
		$users_results = $qls->SQL->query("SELECT `{$qls->config['sql_prefix']}users`.`lime_participant_id`, `{$qls->config['sql_prefix']}users`.`username` FROM `{$qls->config['sql_prefix']}users`, `{$qls->config['sql_prefix']}groups` WHERE `{$qls->config['sql_prefix']}users`.`username` LIKE '%{$_POST['user_name_search']}%' AND `{$qls->config['sql_prefix']}users`.`group_id` = `{$qls->config['sql_prefix']}groups`.`id` AND `{$qls->config['sql_prefix']}groups`.`name` = 'Respondents'");	
		while ($users_rows = $qls->SQL->fetch_array($users_results)) 
		{
			array_push($participant_ids, $users_rows['lime_participant_id']);
			array_push($participant_names, $users_rows['username']);
								
			//get if the survey has been completed by this participant
			$surveys_link_result = $qls->SQL->query("SELECT `date_completed` FROM `{$qls->config['lime_sql_prefix']}survey_links` WHERE `survey_id`='{$sid}' AND `participant_id`='{$users_rows['lime_participant_id']}'");					
			$surveys_link_row = $qls->SQL->fetch_array($surveys_link_result);
			if ($surveys_link_row == null)
			{
				array_push($participant_assigneds, '');
				array_push($participant_completeds, '');
			}
			else
			{
				array_push($participant_assigneds, 'checked');
				array_push($participant_completeds, $surveys_link_row['date_completed']);
			}			
		}
	}
	
	
	
?>
<html>
<body>
<div style ="background-color:linen;margin-left:20px;margin-right:20px;padding:25px;">
<?php
if ($paticipants_updated)
{
?>
	<h3> Participant List For Survey Updated</h3>
<?php
}
?>

<h1>Assign to <?php echo $survey_name; ?>:</h1><br>
<form action="admin_survey_assign.php?sid=<?php echo $sid; ?>" method="post">
<input type="hidden" name="process_search_users" value="yes" />
Search: 
<input type="text" name="user_name_search" value="<?php echo $_POST['user_name_search'] ?>"> 
<input type="submit" value="Search" />
</form>

<br>
<br>


<?php 
if (count($participant_ids))
{
?>


	<h1>Participants:</h1><br>
         <div style="font-size:18px">
	<form action="admin_survey_assign.php?sid=<?php echo $sid; ?>" method="post">
	<input type="hidden" name="process_search_users" value="yes" />
	<input type="hidden" name="user_name_search" value="<?php echo $_POST['user_name_search'] ?>"> 	
	<input type="hidden" name="process_assign_participants" value="yes" />
	<input type="hidden" name="assign_participant_count" value="<?php echo count($participant_ids) ?>" />
	
	<table style="border:1px solid black;border-collapse:collapse;">
	<tr>
	<th style="border:1px solid black;"><b>Participant</b></th>
	<th style="border:1px solid black;"><b>Assigned</b></th>
	<th style="border:1px solid black;"><b>Completed</b></th>
	</tr>

	<?php
	for ($participant_num = 0; $participant_num < count($participant_ids); $participant_num++) 
	{
		$participant_id = $participant_ids[$participant_num];
		$participant_name = $participant_names[$participant_num];
		$participant_completed = $participant_completeds[$participant_num];
		$participant_assigned = $participant_assigneds[$participant_num];				
	?>

	<tr>
	<td style="border:1px solid black;"><?php echo $participant_name; ?></td>	
	<td style="border:1px solid black;">	
	<input type="hidden" name="participant_id_<?php echo $participant_num; ?>" value="<?php echo $participant_id; ?>" />	
	<input type="checkbox" name="participant_assigned_<?php echo $participant_num; ?>" <?php echo $participant_assigned; ?> value="true" />		
	</td>
	<td style="border:1px solid black;"><?php echo $participant_completed; ?></td>
	</tr>

	<?php	
	}
	?>

	</table>
	<br>
	<input type="submit" value="Update" />
	</form>
	<br>
	<br>
	</div>
<?php
}
?>
</div>
<div style ="background-color:lightgrey;margin-left:20px;margin-right:20px;padding:25px;">>
You are logged in as <?php echo $qls->user_info['username']; ?><br />
Your email address is set to <?php echo $qls->user_info['email']; ?><br />
There have been <b><?php echo $qls->hits('members.php'); ?></b> visits to this page.<br />
<br />
</div>

<?php
}
else {
?>

You are currently not logged in.

<?php
}
?>
</body>
</html>