<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
include("includes/pChart2.1.3/class/pDraw.class.php"); 
include("includes/pChart2.1.3/class/pImage.class.php"); 
include("includes/pChart2.1.3/class/pData.class.php"); 
$qls->Security->check_auth_page('participants.php'); ?>


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
<link rel="stylesheet" type="text/css" HREF="form1.css" />
  </head> 
	<body>
		<div id="wrapper">
		<div id="header">
                <a href="http://www.autism-india.org"><span> Action For Autism </span></a>
                  <div style="float:right;">
			                <span>Hi Admin!<a href="logout.php"> Logout</a> </span> 
		                     </div>
			<div id="heading">
				<h1> Indian National Autism Registry </h1> 							   
			</div>
							<ul>							
							<li><a href="register.php"><span>Register</span></a></li>
                                                        <li><a href="alter_reg.php">Configure Registration</a></li>
                                                        <li><a href="admin_surveys.php">View Surveys</a></li>
<li><a href='http://www.indiaautismregistry.com/TEST2B/limesurvey/index.php/admin/authentication/login?user=inar2012&password=Inar!2012&subm=1'>Manage Survey Questions/Data</a></li>
							</ul>
                          
						 
					
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">


<?php
			
			
	
	//process search made
	if (isset($_POST['process_search_users'])) 
	{		
		$search_term = $_POST['user_name_search'];
                $term2 = $_POST['from_date'];	
                $term3 = $_POST['to_date'];
		list($participant_ids, $participant_emails, $participant_surveyss) = $qls->Surveys->search_participants($search_term, $term2,$term3);	
	}
	
	if (isset($_POST['process_reports'])) 
	{	
              $s_term = $_POST['month'];
              $mons = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
              $month_name = $mons[$s_term];
              list($dates_reg,$emails) = $qls->Surveys->search_participants_month($s_term);
	}
?>

<div class="col1">
<p>Search for Participants </p>
<form action="participants.php" method="post">
<input type="hidden" name="process_search_users" value="yes" />
<label for="user_name_search"> <span> Using Email Address:</span> <input type="text" name="user_name_search" value="<?php echo $_POST['user_name_search'] ?>"> </label>
<label for="from_date"><span> From Date: </span> <input type="date" name="from_date" value="<?php echo $_POST['from_date'] ?>"> </label>
<label for="to_date"><span> To Date: </span> <input type="date" name="to_date" value="<?php echo $_POST['to_date'] ?>"> </label>
<div style ="margin: 0px auto 0px auto; text-align: center;">
 <input type="submit" class="myClass" value="Go!" />
</div>
</form>

<p>Generate Reports of Participants</p>
<form action="participants.php" method="post">
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
		
		<div id="footer_wrapper">
		
			<div id="footer_inner_wrapper">
				<a href="welcome.php"><span>Home</span></a>
				<a href="contactus.php">Contact Us </a> 
				<a href="http://ianproject.com">I.A.N </a> 
				
				
				
			
			</div>
		
		</div>
	
	</body>
	
</html>