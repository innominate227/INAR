
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
.col2
{
float:left;
width:65%;
min-height:350px;
border-left:0.5em solid #ccc;
padding:0.5%;
}
.col1
{
float:left;
width:32%;
padding:0.5%;
}
.col1 p,ul,li
{
text-align:left;
line-height:1.4em;
padding:0.5em;
margin: 0.4em;
}
div#clear
{
clear:both;
}
#hor-minimalist-a
{
	background: #fff;
	width: 65%;
	border-collapse: collapse;
	text-align: left;
        font-size:100%;
        margin:auto;
        padding:5%;
        font-family:sans-serif;
}
#hor-minimalist-a th
{       font-weight:normal;
	padding:2% 2.2%;
        border-top: 1px solid navy;
	border-bottom: 1px solid navy;
        font-size:105%;
}
#hor-minimalist-a td
{
	color: black;
	padding: 9px 8px 0px 8px;
        padding: 2% 1.5% 0.5% 1.5%;
}
#hor-minimalist-a tbody tr:hover td
{
	background: #d7ecff;
}
#hor-minimalist-a caption
{
font-size:112%;
font-weight:bold;
padding:2%;
}
.success 
{       
	margin:auto;
        color:DarkGreen;
        text-align:center;
}
.col1 label{
display:block;
margin-bottom:0.2em;
color:black;
}
.col1 form
{
text-align:left;
padding:0.5em;
}
.col1 label span
{
display:block;
float:left;
padding-right:0.5em;
width:11em;
text-align:right;
}
</style>
</head>		<div id="wrapper">
		<div id="header">
                  
				  
			
							<ul>														
							<li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>">View Participants</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inaralterreg/"); ?>">Configure Registration</a></li>
                                                        <li><a class="active" href="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveys/"); ?>">Assign Participants To Surveys</a></li>
							</ul>
                          
						 
					
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">




<div class="col1">

<h3>Survey Name: <?php echo $survey_name; ?></h3>
<li> Do you want to assign participants to this survey? </li>
<p>
Then, Search for participants
<form action="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveysassign/"); ?>?sid=<?=$survey_id?>" method="post">
<input type="hidden" name="process_search_users" value="yes" />
<label><span> Using email address: </span>
<input type="text" name="user_name_search" value="<?php echo $_POST['user_name_search'] ?>">
</label> 
<div style ="margin: 0px auto 0px auto; text-align: center; padding:3px;">
 <input type="submit" value="Go!" />
</div>
</form>
</p>
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
        <tfoot> <tr><td></td>  <td> <input type="submit" value="Update" /> </td> <td> </td></tr> </tfoot>
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
</html>