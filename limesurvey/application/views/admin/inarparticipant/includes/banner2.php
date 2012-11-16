<html lang ='en'>
<head>
<title>
Indian Autism Registry
</title>

 <meta charset="UTF-8" /> 
<link rel="stylesheet" type="text/css" HREF="includes/head.css" />
</head>
<body>
    <div class="header">
		<div class="logo">
			<img src="includes/ourlogo.jpg">
		</div>
		
		
		<div class ="text">	
			Indian National Autism Registry
			<div style="font-size:20px"> Enabling Autism Research</div>
		</div>
	</div>
		<div id="navigation">
			<a href="welcome.php">Home</a>
                         <?php 
                         if ($qls->user_info['username'] != 'inar2012') 
                         {
			 echo '<a href="surveys.php">View Surveys</a>';
                         $u_name=$qls->user_info['username'];
                         }
                         else
                         {
                         echo '<a href="admin_surveys.php">View Surveys</a>';
                         echo '<a href="participants.php">View Participants</a>';
                         echo '<a href="alter_reg.php">Change Registration</a>';
                         $u_name="Admin";
                         }
                         ?>
			<a href="about_us.php">About Us</a>
                         <div style="float:right;">
                          <?php echo 'Hi, '; echo $u_name;?> 
                          <a href="logout.php">Logout</a>
                         </div>
		</div>

</body>
</html>