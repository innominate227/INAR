<?php

<?php
require_once('Surveys.class.php');
require_once('SQL.class.php');

$sql = new SQL();
$surveys = new Surveys($sql);
?>

include("includes/pChart2.1.3/class/pDraw.class.php"); 
include("includes/pChart2.1.3/class/pImage.class.php"); 
include("includes/pChart2.1.3/class/pData.class.php"); 
?>


<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB">
<head>
<link href='http://fonts.googleapis.com/css?family=Convergence' rel='stylesheet' type='text/css'>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>INAR</title>
<link rel="stylesheet" type="text/css" href="cleanstickyfooter.css" media="screen" charset="utf-8" /> 
<link rel="stylesheet" type="text/css" href="gen_styles.css" media="screen" charset="utf-8" /> 
<link rel="stylesheet" type="text/css" HREF="html/table.css" />
<link rel="stylesheet" type="text/css" HREF="form1.css" />
  </head> 
	<body>
		<div id="wrapper">
		 <div class="head">
		<div id="header">
                  <a href="http://www.autism-india.org"> <span> Action For Autism </span> </a>
				    <div style="float:right;">
			                <span>Hi Admin!<a href="logout.php"> Logout</a> </span> 
		                     </div>
			<div id="heading">
				<h1> Indian National Autism Registry </h1> 
				
			</div>
							<ul>							
							<li><a href="register.php"><span>Register</span></a></li>
							<li><a class="active" href="stats.php">Statistics</a></li>
							</ul>
                          
						 
		</div>			
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">


<?php
			
	
	if (isset($_POST['process_reports'])) 
	{	
              $s_term = $_POST['month'];
              $yr = $_POST['year'];
              $mons = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
              $month_name = $mons[$s_term];
              list($dates_reg,$emails) = $surveys->search_participants_month($s_term,$yr);
	}
?>

<div class="col1">

<p>View Statistics of INAR Survey Takers</p>
<form action="stats.php" method="post">
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
<label for="year"><span> Year: </span> 
<select id="year" name="year">
        <option value="2019">2019</option>
	<option value="2018">2018</option>
	<option value="2017">2017</option>
	<option value="2016">2016</option>
	<option value="2015">2015</option>
	<option value="2014">2014</option>
	<option value="2013">2013</option>
	<option selected="selected" value="2012">2012</option>
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
 $myData->addPoints(array("1-7","8-14","15-21","22-28","28-Month End"),"Labels");
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
$myPicture->drawText(300,35,"Participants Registered in $month_name $yr",array("FontSize"=>15,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));
$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>TRUE,"GridR"=>200,"GridG"=>200,"GridB"=>200,"DrawSubTicks"=>TRUE,"CycleBackground"=>TRUE);
 $myPicture->drawScale($scaleSettings);
 $myPicture->drawLineChart(array("DisplayValues"=>TRUE,"DisplayColor"=>DISPLAY_AUTO));

 $myPicture->Render("example.png");
?>
 <img src="example.png" alt="My rendered image"> </img> 
 <div>Total Number of people who registered in the INAR Registry in <?php echo $month_name." ".$yr." = ".$cnt ?>  </div>
  
<?php     } else if(isset($_POST['process_reports']))
         {
          echo "<p> Sorry! No participants registered in $month_name $yr </p>";
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
				<a href="about_us.php">About Us </a> 
				<a href="http://ianproject.com">I.A.N </a> 
				
				
				
			
			</div>
		
		</div>
	
	</body>
	
</html>