<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
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
  </head> 
	<body>
		<div id="wrapper">
		<div id="header">
						<span><a href="http://www.autism-india.org">Action For Autism </a></span>
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
			<div id="content_wrapper">
				<div id="content_inner_wrapper">
				<?php 
                if ($qls->user_info['username'] == 'inar2012') 
                { ?>
					<div id ="error">
                                         <h3> Uh-oh! </h3>
					<?php echo LOGIN_ALREADY_LOGGED; ?>
					</div>
				<?php } 
				else { 
				      require_once('html/login_form.php');
					  } ?>
				
				</div>
			
			</div>
		
		</div>
		
		<div id="footer_wrapper">
		
			<div id="footer_inner_wrapper">
				<a href="welcome.php">Home</a> 
				<a href="contactus.php">Contact Us </a> 
				<a href="http://ianproject.com">I.A.N </a> 
				
				
			
			</div>
		
		</div>
	
	</body>
	
</html>