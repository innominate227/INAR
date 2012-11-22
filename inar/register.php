<?php
require_once('Security.class.php');
require_once('Surveys.class.php');
require_once('SQL.class.php');

$sql = new SQL();
$security = new Security($sql);
$surveys = new Surveys($sql);

?>

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
			<span><a href="http://www.autism-india.org">Action For Autism </a></span>
                         <?php if (false) 
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
                                
<?php
	$created_account = false;
        $invalid_email = false;
        $err_num=0;
	if (isset($_POST['process'])) 
	{
		//check if they pass the security image test
		$pass_security_check = false;		
		
		// The random id of the image
		$random_id = (isset($_POST['random_id']) && preg_match('/^[a-fA-F0-9]{40}$/', $_POST['random_id'])) ? $security->make_safe($_POST['random_id']) : false;

		// The security code entered by the user
		$security_code = (isset($_POST['security_code']) && preg_match('/[a-zA-Z1-9]{5,8}/', $_POST['security_code'])) ? $_POST['security_code'] : false;
		
		//check if that was the correct code
		if ($security->check_security_image($random_id, $security_code)) 
		{
			$pass_security_check = true;
		}
		
		
		//make sure they did security image good
		if ($pass_security_check == false)
		{
                      $err_num=1;         
		}
		else
		{
			// Try to register the participant
          
			//First check if email-id is valid
                        if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) == false)
                        {
                         $invalid_email = true; 
                        }
                        else
                        {
                          $invalid_email = false;
                          $array2 = array('process' => true,'random_id' => 1759,'security_code' => QMnTb);
                          $array3 = array();

                          //hidden values like process,random_id and other validation fields like security code don't 
                          //need to be entered into the database. So we get array3() which has only required values

                           $array3 = array_diff_key($_POST, $array2);
                        
                           foreach ($array3 as $k => $v) 
                           {
                            if (strpos($k, "_") !== false) 
                             {
                              $array3[str_replace("_"," ",$k)] = $v;
                              unset($array3[$k]);
                             }                                            
                           }
                         $invalid_email = false;
                          $participant_created = $surveys->create_new_participant($array3);
                          //echo '<pre>'; print_r($array3); echo '</pre>'; 
                        }
                     if($invalid_email !== false)
                      {
                      
                      $err_num=2;
                      }
		     else if ($participant_created == false) 
		      {	
                      $err_num=3;         			
		      }
                       
			else 
			{
                                
		     echo '<div style="font-weight:bold";>'; 
                     echo '<h3> You have successfully registered! </h3>';   			
		     echo '<p> A survey invitation has been sent to your email address.</p>';
                      echo '<p> Check your inbox and click on the invitation to access the survey.</p>';
                     echo '</div>';
                     echo '<br />';
                     echo '<br />';
                     echo '<div>';
                     echo '<p> Want to to register a new participant? </p>';
                     echo '<div class="btn">';
                     echo '<a href="register.php"><input class="bClass" value="Register"/> </a>';
                     echo '</div>';
                     echo '</div>';
		     $created_account = true;		
                      		
			}
		}
	}
	
	if ($created_account == false)
	{
		// Get the random id for use in the form
		$random_id = $security->generate_random_id();
                echo '<div class="col2" style="border-left:none;border-right:0.5em solid #ccc;">';
                if ($err_num==1)
                 {
                   echo '<div class="error">';
                   echo '<h4> Oops! </h4>';
                   echo 'You entered the security code wrong, try again. You just need to type whatever text you see displayed';
                   echo '</div>';
                 
   
                 ?>
                
                <?php } else if ($err_num==2)
                        {
                        echo '<div class="error">';
                        echo '<h4> Oops! Validation Error </h4>';
                        echo "Your email address is not of the right format. Please fill in a valid email address";
                        echo '</div>';

                       }
                       else if ($err_num==3)
                        {
                        echo '<div class="error">';
                        echo '<h4> Oops! Possible duplication of email-id </h4>';
			echo 'Unable to register you! Have you already registered?';
                        echo '</div>';
                       }
		require_once('html/custom_reg.php');
                echo '</div>';
                echo '<div class="col1">'; 
                    ?>
               <p> Welcome! To register, you will need to enter a valid email address </p>
                  <p> You will also need to verify that you are human by entering the text visible in the picture, into the field provided. Please feel free to refresh the page if the text is unclear! </p>
                  </div> 
               
        
             <div id="clear"> </div>
	<?php }?>
                             </div>
			
			</div>
		
		</div>
		
		<div id="footer_wrapper">
		
			<div id="footer_inner_wrapper">
				<a href="welcome.php">Home</a>
                                <a href="about_us.php">About Us</a>
				<a href="stats.php">INAR Statistics</a> 
				<a href="http://ianproject.com">IAN </a> 
				
			</div>
		
		</div>
	
	</body>
	
</html>