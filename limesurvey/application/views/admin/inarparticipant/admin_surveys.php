<?php
include("includes/pChart2.1.3/class/pDraw.class.php"); 
include("includes/pChart2.1.3/class/pImage.class.php"); 
include("includes/pChart2.1.3/class/pData.class.php"); 
?>

<html>
<head>
<style>
div#header
{
width:100%;
background:#F0F8FF;
overflow: hidden;
margin:0;
}
div#header ul{	
	  
		width:100%;
		list-style:none;
		margin:0;
		padding-left:1%;
                border-top:1px solid #F6EAFA;
                border-bottom:1px solid grey;

		
	}
	div#header ul li {
		display:inline;
		list-style:none;
		margin:0;
		padding:0;
                line-height:1.4em;
	}

	div#header ul li a 
	{
		margin:0;
                padding: 0.05em 1em;
		text-align:center;
		font-size:115%;
	        background:#d7ecff;
		text-decoration:none;
		line-height:1.4em;
                color:black;
                font-weight:bold;
	}
       div#header ul li a:hover
	{
	background:#FFFFE0;
	color:maroon;
	}
       div#header ul li a.active
	   {
        background:#FFFFE0;
        color:DarkGreen;
        }

#content_inner_wrapper label{
display:block;
margin-bottom:0.2em;
color:black;
}
#content_inner_wrapper form
{
text-align:center;
padding:0.5em;
}
#content_inner_wrapper label span
{
padding-right:0.5em;
}
.errr
{
text-align:center;
padding:0.5em;
color:maroon;
}
</style>
</head>	

		<div id="wrapper">
		<div id="header">
                        
                        
			
							<ul>														
							<li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>">View Participants</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inaralterreg/"); ?>">Configure Registration</a></li>
                                                        <li><a  class="active" href="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveys/"); ?>">Assign Participants To Surveys</a></li>
							</ul>
                          
						 
					
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">




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



<form action="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveys/"); ?>" method="post">
<input type="hidden" name="process_update_auto_assign" value="yes" />
<input type="hidden" name="surveys_count" value="<?=count($surveys)?>" />
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

$survey_num=-1;

foreach($surveys as $survey)
{		
	$survey_id = $survey['id'];
    $survey_name = $survey['name'];
	$survey_auto_assign = $survey['auto_assign'];	
	$survey_participant_count = $survey['participants'];
	$survey_response_count = $survey['responses'];		
	$survey_num++;
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
	