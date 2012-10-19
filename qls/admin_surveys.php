<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
$qls->Security->check_auth_page('admin_surveys.php'); ?>



<?php
// Look in the USERGUIDE.html for more info
if ($qls->user_info['username'] != '') {
?>


<?php

	$defaults_updated = false;

	//process changes made
	if (isset($_POST['process_default_surveys'])) 
	{
		//start atomic operation
		$qls->SQL->transaction("START TRANSACTION");
	
		//delete all surveys from default table
		$qls->SQL->query("DELETE FROM `{$qls->config['sql_prefix']}default_surveys`");
		
		//add back the surveys that were checked
		foreach($_POST as $surveyId => $isDefault) 
		{		
			if ($surveyId != 'process_default_surveys')
			{				
				$qls->SQL->query("INSERT INTO `{$qls->config['sql_prefix']}default_surveys` VALUES ('{$surveyId}')");
			}			
		}
		
		//commit atomic operation
		$qls->SQL->transaction("COMMIT");
		
		$defaults_updated = true;
	}
?>



<?php
	
	//id of each surevy
	$survey_ids = array();
	//name of each surevy
	$survey_names = array();	
	//"" if the survey is not a defult, or "checked" if it is
	$is_defaults = array();	
	//"" if the survey be selected as default, or the reason if it cant
	$survey_issues = array();	
	//number of participants assigned
	$participant_counts = array();
	//number of participants assigned
	$responses_counts = array();	
	
	//lanague to get survey names in
	$results_language = 'en';
	

	//get all non expired surveies
	$survey_results = $qls->SQL->query("SELECT `sid`, `active` FROM `{$qls->config['lime_sql_prefix']}surveys`");	
	while ($survey_row = $qls->SQL->fetch_array($survey_results)) 
	{	
		//id for survey
		array_push($survey_ids, $survey_row['sid']);
	
		//get name of survey from language table
		$surveys_lang_result = $qls->SQL->query("SELECT `surveyls_title` FROM `{$qls->config['lime_sql_prefix']}surveys_languagesettings` WHERE `surveyls_survey_id`='{$survey_row['sid']}' AND `surveyls_language`='{$results_language}'");					
		$surveys_lang_row = $qls->SQL->fetch_array($surveys_lang_result);
		if ($surveys_lang_row == null)
		{
			array_push($survey_names, 'title not translated to selected language');
		}
		else
		{
			array_push($survey_names, $surveys_lang_row['surveyls_title']);		
		}
		
				
		//get if the survey is selected in the defults list
		$surveys_is_defult_results = $qls->SQL->query("SELECT `sid` FROM `{$qls->config['sql_prefix']}default_surveys` WHERE `sid`='{$survey_row['sid']}'");					
		$surveys_is_defult_row = $qls->SQL->fetch_array($surveys_is_defult_results);
		if ($surveys_is_defult_row)
		{
			array_push($is_defaults, 'checked');
		}
		else
		{
			array_push($is_defaults, '');		
		}

		
		//check for issues that prevent adding user to the survey (no token table, not active)
		$survey_issue = '';
		$token_exsists_result = $qls->SQL->query("SHOW TABLES LIKE '{$qls->config['lime_sql_prefix']}tokens_{$survey_row['sid']}'");					
		if ($qls->SQL->num_rows($token_exsists_result) == 0)
		{		
			$survey_issue = $survey_issue . 'No Token Table, ';
		}
		if ($survey_row['active'] != 'Y')
		{
			$survey_issue = $survey_issue . 'Survey Not Active, ';	
		}
		array_push($survey_issues, trim($survey_issue, ", "));
		
		
		//get the count of users assigned to take the survey (setup to work even if user is assigned to same survey twice, if we decided to support that in the future)
		$surveys_participants_count_result = $qls->SQL->query("SELECT COUNT(DISTINCT `participant_id`) AS `count` FROM `{$qls->config['lime_sql_prefix']}survey_links` WHERE `survey_id`='{$survey_row['sid']}'");					
		$surveys_participants_count_row = $qls->SQL->fetch_array($surveys_participants_count_result);
		array_push($participant_counts, $surveys_participants_count_row['count']);
		
		
		//get the count of completed responses for this survey
		$surveys_response_count_result = $qls->SQL->query("SELECT COUNT(*) AS `count` FROM `{$qls->config['lime_sql_prefix']}survey_links` WHERE `survey_id`='{$survey_row['sid']}' AND `date_completed` IS NOT NULL");					
		$surveys_response_count_row = $qls->SQL->fetch_array($surveys_response_count_result);
		array_push($responses_counts, $surveys_response_count_row['count']);
				
	}
?>


<?php
if ($defaults_updated)
{
?>
	<h3> Default Surveys Updated</h3>
<?php
}
?>

<h1>Surveys:</h1><br>
<form action="admin_surveys.php" method="post">
<input type="hidden" name="process_default_surveys" value="yes" />
<table style="border:1px solid black;border-collapse:collapse;">
<tr>
<th style="border:1px solid black;"><b>Name</b></th>
<th style="border:1px solid black;"><b>Issues</b></th>
<th style="border:1px solid black;"><b>Default</b></th>
<th style="border:1px solid black;"><b>Paricipants</b></th>
<th style="border:1px solid black;"><b>Assign Paricipants</b></th>
<th style="border:1px solid black;"><b>Responses</b></th>
</tr>

<?php
for ($survey_num = 0; $survey_num < count($survey_names); $survey_num++) 
{
	$survey_id = $survey_ids[$survey_num];
    $survey_name = $survey_names[$survey_num];
	$is_default = $is_defaults[$survey_num];
	$survey_issue = $survey_issues[$survey_num];
	$participant_count = $participant_counts[$survey_num];
	$responses_count = $responses_counts[$survey_num];
?>

<tr>
<td style="border:1px solid black;"><?php echo $survey_name; ?></td>
<td style="border:1px solid black;"><?php echo $survey_issue; ?></td>
<td style="border:1px solid black;">
<?php if ($survey_issue == '') { ?>
    <input type="checkbox"  name="<?php echo $survey_id; ?>" <?php echo $is_default; ?> value="true" />
<?php } ?>
</td>
<td style="border:1px solid black;"><?php echo $participant_count; ?></td>
<td style="border:1px solid black;">
<?php if ($survey_issue == '') { ?>
    <a href="admin_survey_assign.php?sid=<?php echo $survey_id; ?>">Assign</a></td>
<?php } ?>
</td>
<td style="border:1px solid black;"><?php echo $responses_count; ?></td>
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

You are logged in as <?php echo $qls->user_info['username']; ?><br />
Your email address is set to <?php echo $qls->user_info['email']; ?><br />
There have been <b><?php echo $qls->hits('members.php'); ?></b> visits to this page.<br />
<br />
Currently online users (<?php echo count($qls->online_users()); ?>): <?php $qls->output_online_users(); ?>

<?php
}
else {
?>

You are currently not logged in.

<?php
}
?>