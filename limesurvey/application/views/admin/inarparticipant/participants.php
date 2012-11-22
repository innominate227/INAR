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
							<li><a  class="active" href="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>">View Participants</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inaralterreg/"); ?>">Configure Registration</a></li>
                                                        <li><a href="<?php echo Yii::app()->getController()->createUrl("admin/inarsurveys/"); ?>">Assign Participants To Surveys</a></li>
							</ul>
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">


<div class="col1">
<p>Search for Participants </p>
<form action="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>" method="post">
<input type="hidden" name="process_search_users" value="yes" />
<label for="user_name_search"> <span> Using Email Address:</span> <input type="text" name="user_name_search" value="<?php echo $_POST['user_name_search'] ?>"> </label>
<label for="from_date"><span> From Date: </span> <input type="date" name="from_date" value="<?php echo $_POST['from_date'] ?>"> </label>
<label for="to_date"><span> To Date: </span> <input type="date" name="to_date" value="<?php echo $_POST['to_date'] ?>"> </label>
<div style ="margin: 0px auto 0px auto; text-align: center;">
 <input type="submit" class="myClass" value="Go!" />
</div>
</form>

<p>Generate Reports of Participants</p>
<form action="<?php echo Yii::app()->getController()->createUrl("admin/inarparticipants/"); ?>" method="post">
<input type="hidden" name="process_reports" value="yes" />
<label for="month"><span> Month: </span> 
<select name="month">
<option value="1">January</option>
<option value="2">February</option>
<option value="3">March</option>
<option value="4">April</option>
<option value="5">May</option>
<option value="5">June</option>
<option value="7">July</option>
<option value="8">August</option>
<option value="9">September</option>
<option value="10">October</option>
<option value="11">November</option>
<option value="12">December</option>
</select>
</label>
<div style ="margin: 0px auto 0px auto; text-align: center;">
 <input type="submit" class="myClass" value="Go!" />
</div>
</form>
</div>
<div class="col2">
<br />
<?php 
if (count($participant_ids))
{
?>
	<table id="hor-minimalist-a">
        <caption> Results </caption>
        <thead>
	<tr>
	<th>Participant</th>
	<th>Surveys Assigned</th>	
	</tr>
        </thead>
        <tbody>
	<?php
	for ($participant_num = 0; $participant_num < count($participant_ids); $participant_num++) 
	{
		$participant_id = $participant_ids[$participant_num];
		$participant_email = $participant_emails[$participant_num];
		$participant_surveys = $participant_surveyss[$participant_num];		
	?>

	<tr>
	<td><?=$participant_email;?></td>	
	<td><?=implode(", ", $participant_surveys);?></td>		
	</tr>
	<?php	
	}
	?>
         </tbody>
	</table>

<?php } else if(isset($_POST['process_search_users']))
         {
          echo "<p> Sorry! No participants matching your criterion. </p>";
          }
 
      if (count($dates_reg))
        {
         $week=array(0,0,0,0,0);
         $cnt = count($dates_reg);
         for($i=0;$i<$cnt;$i++)
         {
          switch(ceil( date( 'j', strtotime( $dates_reg[$i] ) ) / 7 ))
           {
             case 1:
             $week[0]++;
             break;
             case 2:
             $week[1]++;
              break;
              case 3:
             $week[2]++;
             break;
             case 4:
             $week[3]++;
              break;
             case 5:
             $week[4]++;
             break;
           }
         }

 $myData = new pData(); 
 
 /* Add data in your dataset */ 
 $myData->addPoints($week);
 $myData->addPoints(array("Week1","Week2","Week3","Week4","Week5"),"Labels");
$myData->setSerieDescription("Labels","Weeks");
$myData->setAbscissa("Labels");
$myData->setAxisName(0,"Number");
$myData->setAxisName(1,"Weeks");
 /* Create a pChart object and associate your dataset */ 
 $myPicture = new pImage(750,280,$myData);

 /* Choose a nice font */
 $myPicture->setFontProperties(array("FontName"=>"includes/pChart2.1.3/fonts/verdana.ttf","FontSize"=>13));

 /* Define the boundaries of the graph area */
 $myData->loadPalette("includes/pChart2.1.3/palettes/navy.color", TRUE);
 //setColorPalette(0,255,0,0); 
 $myPicture->setGraphArea(60,40,670,190);
$myPicture->drawText(300,35,"Participants Registered in $month_name",array("FontSize"=>15,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);
 $myPicture->drawLineChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO));

 $myPicture->Render("example.png");
?>
 <img src="example.png" alt="My rendered image"> </img> 
 <div>Total Number of participants who registered in <?php echo $month_name." = ".$cnt ?>  </div>
  
<?php     } else if(isset($_POST['process_reports']))
         {
          echo "<p> Sorry! No participants registered in $month_name </p>";
          }?>

</div>
<div id="clear"> 
<br />
</div>
</div>
			
			</div>
		
		</div>
		
