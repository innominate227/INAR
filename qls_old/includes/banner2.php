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
			<img src="includes/autism.jpg">
		</div>
		<div class="logo">
			<img src="includes/autis.jpg">
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
                         }
                         else
                         echo '<a href="admin_surveys.php">View Surveys</a>';
                         ?>
			<a href="#">Contact Us</a>
			<a href="http://www.ianproject.org">I.A.N</a>
			<a href="#">A.F.A</a>
                        <a href="logout.php">Logout</a>
		</div>

</body>
</html>