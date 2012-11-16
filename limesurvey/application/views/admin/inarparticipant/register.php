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
	$created_account = false;
        $invalid_email = false;
        $err_num=0;
	if (isset($_POST['process'])) 
	{
		//check if they pass the security image test
		$pass_security_check = false;		
		if ($qls->config['security_image'] == 'yes') 
		{
			// The random id of the image
			$random_id = (isset($_POST['random_id']) && preg_match('/^[a-fA-F0-9]{40}$/', $_POST['random_id'])) ? $qls->Security->make_safe($_POST['random_id']) : false;

			// The security code entered by the user
			$security_code = (isset($_POST['security_code']) && preg_match('/[a-zA-Z1-9]{5,8}/', $_POST['security_code'])) ? $_POST['security_code'] : false;
			
			//check if that was the correct code
			if ($qls->Security->check_security_image($random_id, $security_code)) 
			{
				$pass_security_check = true;
			}
		}
		else 
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
                          $participant_created = $qls->Surveys->create_new_participant($array3);
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
                                
		     echo '<div class="success">'; 
                     echo '<h3> Congratulations!</h3>';   			
		     echo 'You have successfully registered! A survey invitation has been sent your email id. ';
                     echo 'Click  <a href="register.php"> here </a> to register a new participant';
		     $created_account = true;		
                      echo '</div>';		
			}
		}
	}
	
	if ($created_account == false)
	{
		// Get the random id for use in the form
		$random_id = $qls->Security->generate_random_id();
                echo '<div class="col2" style="border-left:none;border-right:0.5em solid #ccc;">';
		require_once('html/custom_reg.php');
                echo '</div>';
                echo '<div class="col1">'; 
                 if ($err_num==1)
                 {
                   echo '<div class="error">';
                   echo '<h3> Oops! </h3>';
                   echo 'You entered the security code wrong, try again. You just need to type whatever text you see displayed';
                   echo '</div>';
                   echo '</div>';
                 ?>
                
                <?php } else if ($err_num==2)
                        {
                        echo '<div class="error">';
                        echo '<h3> Oops! Validation Error </h3>';
                        echo "Your email id is not of the right format. Please fill in a valid email-id";
                        echo '</div>';
                        echo '</div>';
                       }
                       else if ($err_num==3)
                        {
                        echo '<div class="error">';
                        echo '<h3> Oops! Possible duplication of email-id </h3>';
			echo 'Unable to register you! Have you already registered?';
                        echo '</div>';
                        echo '</div>';
                       }
                      else
                        {
                    ?>
               <p> Welcome! Please take a few moments to register by filling out the simple form below. After registering, you will be emailed an invitation to a survey.</p>
                  <p> Open the invitation , Fill out the linked survey and you are done! </p>
                  </div> 
               
                 <?php } ?>
        
             <div id="clear"> </div>
	<?php }?>
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