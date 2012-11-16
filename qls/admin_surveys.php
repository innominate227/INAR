<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
$qls->Security->check_auth_page('admin_surveys.php'); 
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB">
<head>
<link href='http://fonts.googleapis.com/css?family=Convergence' rel='stylesheet' type='text/css'>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="description" content="I.N.A.R.">
<meta name="keywords" content="I.N.A.R , A.F.A">
<title>INAR</title>
<link rel="stylesheet" type="text/css" href="cleanstickyfooter.css" media="screen" charset="utf-8" /> 
<link rel="stylesheet" type="text/css" href="gen_styles.css" media="screen" charset="utf-8" /> 
<link rel="stylesheet" type="text/css" HREF="html/table.css" />
  </head> 
	<body>
		<div id="wrapper">
		<div id="header">
                        <span><a href="http://www.autism-india.org">Action For Autism </a></span>
                        <div style="float:right;">
			                <span>Hi Admin!<a href="logout.php"> Logout</a> </span> 
		                     </div>
			<div id="heading">
				<h1> Indian National Autism Registry </h1>
			</div>
							<ul>							
							
							<li><a href="register.php"><span>Register</span></a></li>
							<li><a href="participants.php">View Participants</a></li>
                                                        <li><a href="alter_reg.php">Configure Registration</a></li>
                                     <li><a href='http://www.indiaautismregistry.com/TEST2B/limesurvey/index.php/admin/authentication/login?user=inar2012&password=Inar!2012&subm=1'>Manage Survey Questions/Data</a></li>
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
	<td ><a href="admin_survey_assign.php?sid=<?=$survey_id;?>"><?=$survey_name?></a></td>
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
		
		<div id="footer_wrapper">
		
			<div id="footer_inner_wrapper">
				<a href="welcome.php"><span>Home</a>
				<a href="contactus.php">Contact Us </a> 
				<a href="http://ianproject.com">I.A.N </a> 
				
				
			
			</div>
		
		</div>
	
	</body>
	
</html>