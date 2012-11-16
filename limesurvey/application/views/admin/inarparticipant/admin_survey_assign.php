<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
 ?>


<?php
	$survey_id = $_GET['sid'];
		
	//get info for the survey with the passed id
	list ($survey_id, $survey_name, $survey_auto_assign, $survey_participant_count, $survey_response_count) = $qls->Surveys->get_survey_info($survey_id);	
	

	$paticipants_updated = false;	
	//process user assigned changes
	if (isset($_POST['process_assign_participants'])) 
	{	
		//start operation
		$qls->SQL->transaction("START TRANSACTION");
		
		//look at each participant assigned/unassigned
		$edits_count = $_POST['assign_participant_count'];
		for ($edit_num = 0; $edit_num < $edits_count; $edit_num++) 
		{
			$participant_id = $_POST['participant_id_' . $edit_num];
			$participant_assigned = $_POST['participant_assigned_' . $edit_num];
			
			if ($participant_assigned == 'true')
			{
				$qls->Surveys->assign_to_survey($participant_id, $survey_id);
			}
			else
			{
				$qls->Surveys->unassign_to_survey($participant_id, $survey_id);
			}
		}
		
		//commit operation
		$qls->SQL->transaction("COMMIT");		
		$paticipants_updated = true;
	}
	
	
	//process search made
	if (isset($_POST['process_search_users'])) 
	{		
		$search_term = $_POST['user_name_search'];	
		list($participant_ids, $participant_emails, $participant_assigneds, $participant_completeds) = $qls->Surveys->search_participants_in_survey($search_term, $survey_id);	
	}
	
	
?>
		<div id="wrapper">
		<div id="header">
                  
				  
			
							<ul>														
							<li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>">View Participants</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inaralterreg/"); ?>">Configure Registration</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveys/"); ?>">View Surveys</a></li>
							</ul>
                          
						 
					
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">




<div class="col1">

<h3>Survey Name: <?php echo $survey_name; ?></h3>
<ul>
<li> Do you want to assign participants to this survey? </li>
<p>
Then, Search for participants
<form action="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveysassign/"); ?>?sid=<?=$survey_id?>" method="post">
<input type="hidden" name="process_search_users" value="yes" />
<label><span> Using email address: </span>
<input type="text" name="user_name_search" value="<?php echo $_POST['user_name_search'] ?>">
</label> 
<div style ="margin: 0px auto 0px auto; text-align: center;">
 <input type="submit" class="myClass" value="Go!" />
</div>
</form>
</p>
</ul>
</div>
<div class="col2">

<?php
if ($paticipants_updated)
{
?>
<div class="success">
Participant List For Survey Updated
</div>
<?php
}

if (count($participant_ids)&&(isset($_POST['process_search_users'])))
{
?>

	<form action="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveysassign/"); ?>?sid=<?=$survey_id?>" method="post">
	<input type="hidden" name="process_search_users" value="yes" />
	<input type="hidden" name="user_name_search" value="<?=$_POST['user_name_search']?>"> 	
	<input type="hidden" name="process_assign_participants" value="yes" />
	<input type="hidden" name="assign_participant_count" value="<?=count($participant_ids)?>" />
	
	<table id="hor-minimalist-a">
        <caption>Participants</caption>
        <thead>
	<tr>
	<th>Participant</th>
	<th>Assigned</th>
	<th>Completed</th>
	</tr>
        </thead>
        <tfoot> <tr><td></td> <td> </td> <td> <input type="submit" value="Update" /> </td></tr> </tfoot>
        <tbody>
	<?php
	for ($participant_num = 0; $participant_num < count($participant_ids); $participant_num++) 
	{
		$participant_id = $participant_ids[$participant_num];
		$participant_email = $participant_emails[$participant_num];
		$participant_completed = $participant_completeds[$participant_num];
		$participant_assigned = $participant_assigneds[$participant_num];				
	?>
   
	<tr>
	<td><?=$participant_email." ";?> </td>	
	<td>	
	<input type="hidden" name="participant_id_<?=$participant_num;?>" value="<?=$participant_id;?>" />
	<input type="checkbox" name="participant_assigned_<?=$participant_num;?>" <?php if ($participant_assigned){ echo 'checked'; }?> value="true" />		
	</td>
	<td><?=$participant_completed;?></td>
	</tr>

	<?php	
	}
	?>
       </tbody>
       
	</table>
        
	</form>
	
         <?php	
	}
        else if(isset($_POST['process_search_users']))
         {
          echo "<p> Sorry! No participants meet your search criterion </p>";
         }
	?>

</div>
<div id="clear"> <br /> </div>


</div>
			
			</div>
		
		</div>
		