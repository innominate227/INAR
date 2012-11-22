<?php

/**
 * Contains all Survey functions
 */
class Surveys {
		
	/**
	 * @var object $this->qls - Will contain everything else
	 */
	var $sql;
	
	var $session_key;
	
	/* connection to lime rpc */
	var $lime_rpc;

	/**
	 * Construct class
	 * @param object $this->qls - Contains all other classes
	 * @return void
	 */
	function Surveys(&$sql) 
	{
		require_once('jsonRPCClient.php');	
		$this->sql = &$sql;
	}
	
	/* return the JSON RPC client used to talk to lime */
	private function get_lime_connection()
	{		
		if ($this->lime_rpc == null)
		{
			$this->lime_rpc = new jsonRPCClient('http://indiaautismregistry.org/limesurvey/admin/remotecontrol');
		}
		return $this->lime_rpc;
	}

	/* start a session with lime rpc, return the session key */
	function start_lime_session()
	{
		$lime_rpc = $this->get_lime_connection();
		$session_key = $lime_rpc->get_session_key('inar2012', 'Inar!2012');		
		return $session_key;
	}
		
	/* end the session with lime rpc */
	function end_lime_session()
	{
		$lime_rpc = $this->get_lime_connection();		
		$lime_rpc->release_session_key($session_key);				
	}
		
		
	
	
	
	
	/*************************
	 *     Participants      *  
	 *************************/
	 
	
	/* create a new participant, assign the participant to all survey marked auto assign */	 
	function create_new_participant($array1)
	{
		//make sure no one already registered that email
                $email = $array1['email'];
		$participant_row = $this->sql->select_one_simple('*', 'participants', array('email' => $email));
		if ($participant_row != null){ return false; }
	        

		//insert participant and get their id
                $date = date("Y-m-d");
                $array1['date']=$date;
		//$this->sql->insert_simple('participants', array('email' => $email,'date' => $date));
                
                $this->sql->insert_simple('participants', $array1);
		$participant_id = $this->sql->insert_id();
		
		//create_new_participant happens based on action the participant made (so no one is logged in at the time)
		//this means there will not be a lime session open at the time.  
		//So lets open one now, before we try and assign the new participant to surveys
   
		$this->start_lime_session();
    			
		//get all survey marked auto assign, and assign participant to those
		$survey_results = $this->sql->select_simple('id', 'surveys', array('auto_assign_new_participant' => 'Y'));			
		while ($survey_row = $this->sql->fetch_array($survey_results)) 
		{
			$this->assign_to_survey($participant_id, $survey_row['id']);
		}
		
		//end the lime session
		$this->end_lime_session();
		
		return true;
	}	
		
		
    /* Assign the participant with the id pass to the survey passed */
	function assign_to_survey($participant_id, $survey_id)
	{	
		//make sure participant is not already assigned
                //not actually doing anything, so what if the user was deleted and is registering again?
		if ($this->is_participant_assigned($participant_id, $survey_id)){ return; }		
	
		//get email for the participant with that id
		$participant_info = $this->sql->select_one_simple(array('email'), 'participants', array('id' => $participant_id));
		
		//create array of data for this participant to send to lime (could put first and last name in here too if we had them)
		$participant_data = array(
		'email' => $participant_info['email'],
		'emailstatus' => 'OK' //it will not send the email unless we set this too
		);	
		
		//get connection to lime
		$lime_rpc = $this->get_lime_connection();	
				
		//add the participant, get the token that was created for him (Note the method actually takes an array of partcipants)

		$add_participant_results = $lime_rpc->add_participants($session_key, $survey_id, array($participant_data) );
   		
		$token_id = $add_participant_results[0]['tid'];
		$token = $add_participant_results[0]['token'];
		
		
		//send a invite to the participant (the function sends invite to all that have not been reminded which should be just the new participant)
                //was throwing exception, but was still sending email...	
                try
                {	
		$invite_participant_results = $lime_rpc->invite_participants($session_key, $survey_id);	
                }
                catch(Exception $e) 
		{
		}							
		//TODO: if we are going to send more than one then we need to check results because i think it will only send X at a time 
		
											
		//add to our participant_surveys table		
		$this->sql->insert_simple('participant_surveys',
			array(
				'participant_id' => $participant_id,
				'survey_id' => $survey_id,
				'token_id' => $token_id,
				'token' => $token,
			)
		);		
	}
		
		
		
	
	/* get true or false if the participant passed is assigned to the survey passed */
	function is_participant_assigned($participant_id, $survey_id)
	{
		$resultrow = $this->sql->select_one_simple('*', 'participant_surveys',
			array(
				'participant_id' => $participant_id,
				'survey_id' => $survey_id				
			)
		);
				
		return ($resultrow != null);
	}	
	
		
	function search_participants_month($month,$yr)
	{
         $dates = array();
         $emails = array();
          $participants_table = 'inar_participants';
	  $sql3 = "SELECT date,email FROM `{$participants_table}` WHERE `date` BETWEEN '$yr-$month-01' AND '$yr-$month-31'";
         $result = $this->sql->query($sql3);
       if (!$result) 
       {
          die('Invalid query: ' . mysql_error());
       }
         
        while($row = $this->sql->fetch_array($result))
        {
         $dates[] = $row['date'];
         $emails[] = $row['email'];
         }
       return array($dates,$emails);
       }

}
	
?>