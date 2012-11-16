<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
include("includes/pChart2.1.3/class/pDraw.class.php"); 
include("includes/pChart2.1.3/class/pImage.class.php"); 
include("includes/pChart2.1.3/class/pData.class.php"); 
?>

		<div id="wrapper">
		<div id="header">
                        
                        
			
							<ul>							
							<li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>">View Participants</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inaralterreg/"); ?>">Configure Registration</a></li>                                     
							</ul>
                          
						 
					
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">


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
	list ($survey_ids, $survey_names, $survey_auto_assigns, $survey_participant_counts, $survey_response_counts) = $qls->Surveys->get_all_surveys_info();		
?>

<?php
if ($auto_assign_updated)
{
?>
<div class="success">	
Update Successful: Default Surveys Changed!
</div>
<?php
}
?>



<form action="admin_surveys.php" method="post">
<input type="hidden" name="process_update_auto_assign" value="yes" />
<input type="hidden" name="surveys_count" value="<?=count($survey_names)?>" />
<br />
<table id="hor-minimalist">
<caption> Current Surveys </caption>
<thead>
<tr>
<th>Name</th>
<th>Set: Default Survey</th>
<th>Participants</th>
</tr>
</thead>
<tfoot>
<tr>
<td> </td>
<td> <input class= "myClass" type="submit" value="Save Selection" /></td>
<td> </td>
</tfoot>
<tbody>
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
	<td ><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveysassign/"); ?>?sid=<?=$survey_id;?>"><?=$survey_name?></a></td>
	<td >
	<input type="hidden"    name="survey_id_<?=$survey_num?>"    value="<?=$survey_id?>" />
	<input type="checkbox"  name="survey_auto_<?=$survey_num?>"  value="true"  <?php if($survey_auto_assign){ echo 'checked'; } ?> />
	</td>
	<td ><?=$survey_participant_count?></td>
	</tr>
</tbody>
<?php	
}
?>

</table>
</form>




                                </div>
			
			</div>
		
		</div>
	