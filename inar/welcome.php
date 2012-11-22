<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-GB">
<head>
<link href='http://fonts.googleapis.com/css?family=Convergence' rel='stylesheet' type='text/css'>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="robots" content="nofollow">
<meta name="googlebot" content="noindex">
<title>INAR</title>
<link rel="stylesheet" type="text/css" href="cleanstickyfooter.css" media="screen" charset="utf-8" /> 
<link rel="stylesheet" type="text/css" href="gen_styles.css" media="screen" charset="utf-8" /> 
  </head> 
	<body>
		<div id="wrapper">
                <div class="head">
		<div id="header">
                       
			<span> <a href="http://www.autism-india.org">Action For Autism </a> </span>
                         <?php if ($qls->user_info['username'] == 'inar2012') 
                         {                          echo '<div style="float:right"> ';
						   echo '<span>Hi Admin!<a href="logout.php"> Logout</a> </span> ';
						    echo '</div>';
				
							
                          }
                            else
                               { ?>
					<div style="float:right"> 
					<a href="login.php"><span>Staff Login</span></a> 
					</div> 
                            <?php } ?>
                           <div id="heading">
				<h1> Indian National Autism Registry </h1> 
				     
			    </div>		
		
		</div>	 
                </div>
			<div id="content_wrapper">
				<div id="content_inner_wrapper">
					<div>
					<h2> Welcome to INAR! </h2>
					<!-- <p>Enable Autism Research. Empower Autistic Community</p> -->
				<div class="btn">
                                <a href="register.php"><input type="submit" class="bClass" value="Register"/> </a> 
                                </div>
                                <div class="btn">
                                <a href="about_us.php"><input type="submit" class="bClass" value="About Us"/></a>
                                </div>
					</div>
				
				</div>
			
			</div>
		
		</div>
		
		<div id="footer_wrapper">
		
			<div id="footer_inner_wrapper">
				<a href="stats.php">INAR Statistics</a>
				<a href="contactus.php">Contact Us </a> 
				<a href="http://ianproject.com">IAN </a> 

				
				
			
			</div>
		
		</div>
	
	</body>
	
</html>