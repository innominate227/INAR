<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
$qls->Security->check_auth_page('participants.php'); ?>



<?php
// Look in the USERGUIDE.html for more info
if ($qls->user_info['username'] != '') 
{
require_once('includes/banner2.php');
?>

<?php
			
			
	
	//process search made
	if (isset($_POST['process_search_users'])) 
	{		
		$search_term = $_POST['user_name_search'];	
		list($participant_ids, $participant_emails, $participant_surveyss) = $qls->Surveys->search_participants($search_term, $survey_id);	
	}
	
	
?>
<html>
<body>
<div style ="background-color:linen;margin-left:20px;margin-right:20px;padding:25px;">

<h1>Participants:</h1><br>
<form action="participants.php" method="post">
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
	<table style="border:1px solid black;border-collapse:collapse;">
	<tr>
	<th style="border:1px solid black;"><b>Participant</b></th>
	<th style="border:1px solid black;"><b>Surveys Assigned</b></th>	
	</tr>

	<?php
	for ($participant_num = 0; $participant_num < count($participant_ids); $participant_num++) 
	{
		$participant_id = $participant_ids[$participant_num];
		$participant_email = $participant_emails[$participant_num];
		$participant_surveys = $participant_surveyss[$participant_num];		
	?>

	<tr>
	<td style="border:1px solid black;"><?=$participant_email;?></td>	
	<td style="border:1px solid black;"><?=implode(", ", $participant_surveys);?></td>		
	</tr>

	<?php	
	}
	?>

	</table>
	
	
	<br>
	<br>
	</div>
<?php
}
?>
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