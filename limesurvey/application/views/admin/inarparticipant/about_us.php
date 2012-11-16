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
              <span><a href="http://www.autism-india.org"> Action For Autism </a> </span>
                   <div style="float:right;">
                       <?php if ($qls->user_info['username'] == 'inar2012') 
                               {  
				echo '<span>Hi Admin!<a href="logout.php"> Logout</a> </span> ';
                               }
                              else
                                 { echo '<a href="login.php"><span>Staff Login</span></a>';}
                          ?>      
		   </div>
			<div id="heading">
				<h1> Indian National Autism Registry </h1>				                                                          
			</div>
							<ul>
                                                        							
							<li><a href="welcome.php"><span>Home</span></a></li>
							<li><a href="register.php"><span>Register</span></a></li>
							</ul>
                       
		</div>	 
			<div id="content_wrapper">
				<div id="content_inner_wrapper">

					<div id= "inner_text">
				<h2>The I.N.A.R Project: A Collaborative Partnership with the Interactive Autism Network </h2>

					<h4> Background </h4>
<p>Given the rapid spread of awareness about Autism Spectrum Disorder (ASD), the condition is being identified in an 
ever-increasing number of countries throughout the world. In 2011 alone, studies were published from countries diverse 
as Brazil, China, Colombia, Croatia, Egypt, India, Iran, Libya, Nepal, Nigeria, Oman, and Pakistan.  In addition, 
organizations exist in over 120 countries. Among all of these, India has emerged as a leader for progress in raising 
awareness among professionals, promoting policies inclusive of ASD, and establishing autism-specific services for children
and families. In part as a result of this high level of activity in India, the number of children now receiving a diagnosis of
autism is also rising; Daley & Singhal (2010) documented a threefold increase in the reported number of cases of ASD diagnosed
by pediatricians between 1998 and 2008. Autism organizations and autism-specific schools now exist in over 20 cities, and 
there are more than a dozen autism-specific organizations. In addition, research on ASD in India is rapidly maturing, with 
more than 40 peer-reviewed studies published in 2009 and 2010 alone. </p>
 
<p>This growth places India in a unique but challenging situation. India’s population of over 1.21 billion currently 
constitutes more than a sixth of the world’s population, and any systematic data on ASD in India therefore have the 
potential to inform both policy and practice for millions of families. However, the geographic, cultural and logistic 
challenges associated with conducting multi-site studies have prevented such data from being collected to date, and have 
led to a fragmented understanding of the condition in India. </p> 
<p>The increased level of autism awareness among parents and professionals and burgeoning research field in India now offers 
a level of activity that can be channeled into an innovative and unprecedented project: A registry for children and adults 
with autism in India, called the Indian National Autism Registry (INAR).  We propose to launch this project as an 
international partner project of an existing registry, the Interactive Autism Network (IAN; operated by the Kennedy Krieger 
Institute). Established in 2006, IAN is a straightforward yet powerful web-based system to obtain data on a range of topics 
from family members and adults with autism.  To date, more than 40,000 people within the U.S. have participated in IAN. While 
IAN has capability to work with other registries and senior staff members are willing to provide technical assistance to 
leverage the resources of IAN to interested and suitable countries, the task of developing a compatible system rests on the 
partner country. </p> 
<p>To this end, AFA is pleased that the INAR has been accepted by Georgia Tech University as a project for their “
Computing for Good” (C4G) course, starting in August 2012.  A group of three to four students will be assigned the task 
of helping AFA develop an effective system, compatible with IAN, to meet the needs of Indian families and stakeholders.
The involvement of Georgia Tech will be for a limited time (one semester), and AFA is seeking funds to support this process. 
Specifically, we would like to employ one full time individual for 18 months to oversee the development of INAR, the initial 
publicity effort, and the first year of operation. During this time, AFA staff will develop the capacity to maintain the 
system so that the project can be sustainable beyond the involvement of the C4G students and be integrated into regular AFA
functions. </p>  
<h4> Goals </h4>
<p>Under this project, AFA will develop a protocol for an Indian autism registry to meet the following specific goals:</p>
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
		<p>Once the INAR partner project is effectively established, data collection will allow:</p>
		<ul>
		<li>De-identified data from INAR to be offered to qualified research studies; and </li>
		<li>Participant recruitment into research studies that will foster the expansion and quality of research on autism 
			in India.
		</li>
		</ul>

					</div>
				
				</div>
			
			</div>
		
		</div>
		
		<div id="footer_wrapper">
		
			<div id="footer_inner_wrapper">
				
				<a href="contactus.php">Contact Us </a> 
				<a href="http://ianproject.com">IAN </a> 
				
				
			
			</div>
		
		</div>
	
	</body>
	
</html>