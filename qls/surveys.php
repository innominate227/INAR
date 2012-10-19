<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
require_once('includes/banner.php');
$qls->Security->check_auth_page('surveys.php'); ?>



<?php
// Look in the USERGUIDE.html for more info
if ($qls->user_info['username'] != '') {
?>

<div>
<h1>Surveys:</h1>
<div style="float:right;font-size:25px;margin-right:10px;"><a href="logout.php"> LOGOUT </a> </div>
</div>
<table style="text-align:center;margin:auto">
<?php

	//get all surveies this user is a partcipant in
	$survey_results = $qls->SQL->query("SELECT `token_id`, `survey_id`, `date_completed` FROM `{$qls->config['lime_sql_prefix']}survey_links` WHERE  `participant_id`='{$qls->user_info['lime_participant_id']}'");	
	while ($survey_row = $qls->SQL->fetch_array($survey_results)) 
	{
		//need to get row from survey lang table with title, and token table with the users token
		$surveys_lang_row = NULL;
		$token_row = NULL;
	
		//get name of survey from language table
		$surveys_lang_result = $qls->SQL->query("SELECT `surveyls_title` FROM `{$qls->config['lime_sql_prefix']}surveys_languagesettings` WHERE `surveyls_survey_id`='{$survey_row['survey_id']}' AND `surveyls_language`='en'");					
		$surveys_lang_row = $qls->SQL->fetch_array($surveys_lang_result);
						
		//get token number from the id
		$token_result = $qls->SQL->query("SELECT `token`, `completed` FROM `{$qls->config['lime_sql_prefix']}tokens_{$survey_row['survey_id']}` WHERE `tid`='{$survey_row['token_id']}'");					
		$token_row = $qls->SQL->fetch_array($token_result);
				
		//if we found both we can show the survey to the user
		if ($surveys_lang_row and $token_row)
		{
			echo '<tr><td>';
			//only show link if user has not already completed the survey
			if ($token_row['completed'] == 'N')
			{	
				echo '<a href="';
				echo '../limesurvey/index.php/' . $survey_row['survey_id'] . '/tk-' . $token_row['token'];
				echo '">';
				echo $surveys_lang_row['surveyls_title'];
				echo '</a>';				
			}
			else
			{
				echo $surveys_lang_row['surveyls_title'] . ' - Completed On ' . $survey_row['date_completed'];
			}
			echo'</td></tr>';
		}
	}
?>
</table>
<br>
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