<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
?>
<!DOCTYPE html>
<html>
<head>
<title>I.N.A.R Home Page</title>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
<meta name="generator" content="HAPedit 3.1">
<link rel="stylesheet" type="text/css" HREF="layout.css" />
</head>
<body>
<div id="container">
		<div id="header">
			<div class="head">
				<div class="logo">
				<img src="includes/autism.jpg">
				</div>
				
				<div class="logo">
				<img src="includes/autis.jpg">
				</div>
		
				<div class ="text">	
				Indian National Autism Registry(I.N.A.R)
				<div style="font-size:20px"> Enabling Autism Research </div>
				</div>
			</div>
			
			<div id="navig">
			<a href="welcome.php">Home</a>
                        <?php 
                         if ($qls->user_info['username'] == 'inar2012') 
			       echo '<a href="admin_surveys.php">View Surveys</a>';
                         else
                               echo '<a href="surveys.php">View Surveys</a>'; 
                        ?>
			<a href="">Contact Us</a>
			<a href="http://www.ianproject.org">I.A.N</a>
			<a href="#">A.F.A</a>
                        <?php 
                         if ($qls->user_info['username'] == '') 
                         {
                              echo '<a href="login.php">Login</a>';
                              echo '<a href="register.php" >Register</a>';
                       }       
                        else
                        echo '<a href="logout.php" >Logout</a>';
                        ?>
			</div>
				<h1>
				The I.N.A.R Project:
				A Collaborative Partnership with the Interactive Autism Network
				</h1>
				
			</div>
		<div id="wrapper">
			<div id="content">
			<a name="C1"><p> <strong>1) <u> Overview </u> </strong> </p></a>
			<p>
			Given the rapid spread of awareness about Autism Spectrum Disorder (ASD), the condition is 
			being identified in an ever-increasing number of countries throughout the world. In 2011 alone, 
			studies were published from countries diverse as Brazil, China, Colombia, Croatia, Egypt, India, 
			Iran, Libya, Nepal, Nigeria, Oman, and Pakistan.  In addition, organizations exist in over 120 
			countries. Among all of these, India has emerged as a leader for progress in raising awareness 
			among professionals, promoting policies inclusive of ASD, and establishing autism-specific 
			services for children and families. In part as a result of this high level of activity in India, 
			the number of children now receiving a diagnosis of autism is also rising; Daley & Singhal 
			(2010) documented a threefold increase in the reported number of cases of ASD diagnosed by 
			pediatricians between 1998 and 2008. Autism organizations and autism-specific schools now 
			exist in over 20 cities, and there are more than a dozen autism-specific organizations. In addition, 
			research on ASD in India is rapidly maturing, with more than 40 peer-reviewed studies published 
			in 2009 and 2010 alone.</p> 
 
			<p>
		This growth places India in a unique but challenging situation. India's population of over 1.21 
		billion currently constitutes more than a sixth of the world's population, and any systematic data 
		on ASD in India therefore have the potential to inform both policy and practice for millions of 
		families. However, the geographic, cultural and logistic challenges associated with conducting 
		multi-site studies have prevented such data from being collected to date, and have led to a 
		fragmented understanding of the condition in India. 
		</p>
			<p>
			The increased level of autism awareness among parents and professionals and burgeoning 
			research field in India now offers a level of activity that can be channeled into an innovative and 
			unprecedented project: A registry for children and adults with autism in India, called the Indian 
			National Autism Registry (INAR).  We propose to launch this project as an international partner 
			project of an existing registry, the Interactive Autism Network (I.A.N - operated by the Kennedy 
			Krieger Institute). Established in 2006, IAN is a straightforward yet powerful web-based system 
			to obtain data on a range of topics from family members and adults with autism.  To date, more 
			than 40,000 people within the U.S. have participated in IAN. While IAN has capability to 
			work with other registries and senior staff members are willing to provide technical assistance 
			to leverage the resources of IAN to interested and suitable countries, the task of developing a 
			compatible system rests on the partner country.  
			</p>
			<a name="C2"><p> <strong>2) <u>Goals </u> </strong> </p></a>
			<p>Under this project, AFA will develop a protocol for an Indian autism registry to meet the 
			following specific goals:</p>
			<ul>
			<li>
			Promote scientific discovery by synthesizing demographic, developmental and behavioral 
			information on children and adults with autism, as well as obtaining systematic data on 
			diagnostic patterns, treatment practices and areas of need across all ages of the lifespan of 
			individuals in India; </li>
			<li>Use data obtained from families in India to inform important policy implications among 
			the medical and educational fields and the Indian central government; </li>
			<li>Broaden the IAN population to incorporate individuals from a middle income country;
			Once the INAR partner project is effectively established, data collection will allow:</li>
			<li>De-identified data from INAR to be offered to qualified research studies; and </li>
			<li>Participant recruitment into research studies that will foster the expansion and quality of 
			research on autism in India.</li>
			</ul>
			<a name ="C3"><p> <strong>3) <u> How Can You Contribute </u> </strong> </p></a>
			<p> If anyone in your family has autism, you can contribute by registering on this site and filling out a simple survey.</p>
			<ul>
			<li> Step 1 - Register here <a href="register.php"> NOW </a> </li>
			<li> Step 2 - After Registering, you will be able to log in <a href="login.php"> HERE</a> </li>
			<li> Step 3 - Fill out a Simple Survey and You are Done! </li>
			</ul>
			<a name ="C4"><p> <strong>4) <u>How Can You Access Data from Surveys for Research? </u></strong> </p></a>
			<p> De-identified Data is Made Available to Researchers. All they need to do is -</p>
			<ul>
			<li> Step 1 - Register here <a href="register.php"> NOW </a> </li>
			<li> Step 2 - After Registering, you will be able to log in <a href="login.php"> HERE</a> </li>
			<li> Step 3 - Export Data for Available(Closed) Surveys and export it or visualize it! </li>
			</ul>
		</div>
		
		</div>
		<div id="navigation">
		<p><strong>Navigation here.</strong> </p>
		<ul>
		<li><a href="#C2">Our Goals</a></li>
		<li><a href="#C3">How Can You Contribute?</a></li>
		<li><a href="#C4">How Can You Access Data?</a></li>
		</ul>
		</div>
		<div id="footer"><a href="#C1"><p> Go Back to the Top</p> </a></div>
</div>
</body>
</html>