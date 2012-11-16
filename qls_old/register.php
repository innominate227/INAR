<?php
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
require_once('includes/banner.php');
?>


<?php
/*** *** *** *** *** ***
* @package Quadodo Login Script
* @file    register.php
* @start   July 25th, 2007
* @author  Douglas Rennehan
* @license http://www.opensource.org/licenses/gpl-license.php
* @version 1.0.1
* @link    http://www.quadodo.com
*** *** *** *** *** ***
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*** *** *** *** *** ***
* Comments are always before the code they are commenting.
*** *** *** *** *** ***/

	$created_account = false;

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
			echo 'You entered the security image wrong, try again.';
		}
		else
		{
			// Try to register the participant
			$participant_created = $qls->Surveys->create_new_participant($_POST['email']);
			
			if ($participant_created == false) 
			{			
				echo 'Unable to register, have you already registered?';			
			}
			else 
			{				
				echo 'You are regiested check your email for survey invitations';
				$created_account = true;				
			}
		}
	}
	
	if ($created_account == false)
	{
		// Get the random id for use in the form
		$random_id = $qls->Security->generate_random_id();
		require_once('html/register_form.php');
	}

?>