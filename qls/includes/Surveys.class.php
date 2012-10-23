<?php

if (!defined('QUADODO_IN_SYSTEM')) {
exit;	
}

/**
 * Contains all Survey functions
 */
class Surveys {
		
	/**
	 * @var object $this->qls - Will contain everything else
	 */
	var $qls;
	
	/* connection to lime rpc */
	var $lime_rpc;

	/**
	 * Construct class
	 * @param object $this->qls - Contains all other classes
	 * @return void
	 */
	function Surveys(&$qls) 
	{
		require_once('jsonRPCClient.php');	
		$this->qls = &$qls;
	}
	
	/* return the JSON RPC client used to talk to lime */
	private function get_lime_connection()
	{		
		if ($this->lime_rpc == null)
		{
			$this->lime_rpc = new jsonRPCClient($this->qls->config['lime_location']);
		}
		return $this->lime_rpc;
	}

	/* start a session with lime rpc, return the session key */
	function start_lime_session()
	{
		$lime_rpc = $this->get_lime_connection();
		$sessionKey = $lime_rpc->get_session_key($this->qls->config['lime_username'], $this->qls->config['lime_password']);		
		return $sessionKey;
	}
		
	/* end the session with lime rpc */
	function end_lime_session()
	{
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];		
		$lime_rpc->release_session_key($lime_session_key);				
	}
		
		
		
		
	/*************************
	 *        Surveys        *  
	 *************************/
		
		
	/* create a new survey */
	function create_survey($name, $data)
	{
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];		
		
		//create the survey
		$new_survey_id = $lime_rpc->import_survey($lime_session_key, $data, 'lss', $name);
				
		//set properties for the survey
		$survey_properties = array(
			'usetokens' => 'Y',
			'tokenanswerspersistence' => 'Y',
			'allowsave' => 'N',
			'adminemail' => 'noreply@indiaautismregistry.com',
			'htmlemail' => 'N');
		$response = $lime_rpc->set_survey_properties($lime_session_key, $new_survey_id, $survey_properties);
						
						
		//set language properties for the survey		
		$invite_email  = 'Dear INAR Participant,' . '\r\n';
		$invite_email .= '\r\n';
		$invite_email .= 'You have been invited to participate in a survey.' . '\r\n';
		$invite_email .= 'The survey is titled: {SURVEYNAME}' . '\r\n';
		$invite_email .= 'To participate, please click on the link below.' . '\r\n';
		$invite_email .= '\r\n';
		$invite_email .= 'Sincerely,' . '\r\n';
		$invite_email .= 'INAR' . '\r\n';
		$invite_email .= '\r\n';
		$invite_email .= '----------------------------------------------' . '\r\n';
		$invite_email .= 'Click here to do the survey:' . '\r\n';
		$invite_email .= '{SURVEYURL}' . '\r\n';		
		
		$reminder_email  = 'Dear INAR Participant,' . '\r\n';
		$reminder_email .= '\r\n';
		$reminder_email .= 'Recently we invited you to participate in a survey.' . '\r\n';
		$reminder_email .= 'We note that you have not yet completed the survey, and wish to remind you that the survey is still available should you wish to take part.' . '\r\n';
		$reminder_email .= 'The survey is titled: {SURVEYNAME}' . '\r\n';
		$reminder_email .= 'To participate, please click on the link below.' . '\r\n';
		$reminder_email .= '\r\n';
		$reminder_email .= 'Sincerely,' . '\r\n';
		$reminder_email .= 'INAR' . '\r\n';
		$reminder_email .= '\r\n';
		$reminder_email .= '----------------------------------------------' . '\r\n';
		$reminder_email .= 'Click here to do the survey:' . '\r\n';
		$reminder_email .= '{SURVEYURL}' . '\r\n';
		
		$confirm_email  = 'Dear INAR Participant,' . '\r\n';
		$confirm_email .= '\r\n';
		$confirm_email .= 'This email is to confirm that you have completed the survey titled {SURVEYNAME} and your response has been saved. Thank you for participating.' . '\r\n';		
		$confirm_email .= '\r\n';
		$confirm_email .= 'Sincerely,' . '\r\n';
		$confirm_email .= 'INAR' . '\r\n';		
				
		$survey_language_properties = array(
			'surveyls_email_invite_subj' =>    'Invitation to participate in a survey',
			'surveyls_email_invite' =>         $invite_email,
			'surveyls_email_remind_subj' =>    'Reminder to participate in a survey',
			'surveyls_email_remind' =>         $reminder_email,
			'surveyls_email_confirm_subj' =>   'Confirmation of your participation in our survey',
			'surveyls_email_confirm' =>        $confirm_email);
		$response = $lime_rpc->set_language_properties($lime_session_key, $new_survey_id, $survey_language_properties, 'en');
		
				
		//set the survey to use tokens
		$response = $lime_rpc->activate_tokens($lime_session_key, $new_survey_id);
		
		//activate the survey
		$response = $lime_rpc->activate_survey($lime_session_key, $new_survey_id);
						
		//insert the new survey on our side
		$this->qls->SQL->insert_simple('surveys',
			array(
				'id' => $new_survey_id,
				'name' => $name
			)
		);
	}
		
	
	/* export a survey to csv */
	function export_survey($survey_id)
	{
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];			
	
		//survey info
		list ($survey_id, $survey_name, $survey_auto_assign, $survey_participant_count, $survey_response_count) = $this->get_survey_info($survey_id);
		
		//export parameters
		$langauage = 'en';
		$completion = 'show';       //'complete', 'incomplete' or 'show' (all)
		$heading_type = 'full';     //'code', 'full' or 'abbreviated'
		$response_type = 'long';    //'short' or 'long'		
		$from_record_num=0;         //record # to start from
		$to_record_num=$survey_response_count-1; //record # to end at
		$fields=null;               //field to export not sure if this is question id maybe, havent tried yet. (or null for all)
				
		//get responses
		$data = $lime_rpc->export_responses($lime_session_key, $survey_id, 'csv', $langauage, $completion, $heading_type, $response_type, $from_record_num, $to_record_num, $fields);
						
		//return data
		return $data;
	}
	
	
	/* make a survey be auto matically assigned to a new participant */
	function set_survey_auto_assign($survey_id, $auto_assign)
	{
		$auto_assign_char = 'N';
		if ($auto_assign) { $auto_assign_char = 'Y'; }
	
		//update the survey in database
		$this->qls->SQL->update_simple('surveys',
			array('auto_assign_new_participant' => $auto_assign_char),
			array('id' => $survey_id)
		);	
	}
	
	
	/* get survey info for all surveys */
	function get_all_surveys_info()
	{
		//get lime connection
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	
				
		$ids = array();
		$names = array();
		$auto_assigns = array();
		$participants = array();
		$responses = array();
	
		//get all surveys
		$surveys_results = $this->qls->SQL->select('*', 'surveys');				
		while ($survey_row = $this->qls->SQL->fetch_array($surveys_results)) 
		{
			$ids[] = $survey_row['id'];
			$names[] =  $survey_row['name'];
			$auto_assigns[] = ($survey_row['auto_assign_new_participant'] == 'Y');
			
			$participant_surveys_table = $this->qls->config['sql_prefix'] . 'participant_surveys';
			$survey_participant_count_results = $this->qls->SQL->query("SELECT COUNT(*) FROM `{$participant_surveys_table}` WHERE `survey_id`={$survey_row['id']}");
			$survey_participant_count_row = $this->qls->SQL->fetch_array($survey_participant_count_results);
			$survey_participant_count = $survey_participant_count_row['COUNT(*)'];
			$participants[] = $survey_participant_count;
						
			//ask lime how many people have responded (seems to have issue if at least one participant is not assigned)
			$response_count = 0;
			if ($survey_participant_count > 0)
			{
				$response_count = $lime_rpc->get_summary($lime_session_key, $survey_row['id'], 'full_responses');	
			}
			$responses[] = $response_count;
		}
								
		//return all the info
		return array($ids, $names, $auto_assigns, $participants, $responses);
	}

	
	/* get survey info for one surveys */
	function get_survey_info($survey_id)
	{				
		//TODO: lots of replicated code between this and get_all_surveys_info.
				
		//get lime connection
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	
	
		$participant_surveys_table = $this->qls->config['sql_prefix'] . 'participant_surveys';
		$survey_participant_count_results = $this->qls->SQL->query("SELECT COUNT(*) FROM `{$participant_surveys_table}` WHERE `survey_id`={$survey_id}");
		$survey_participant_count_row = $this->qls->SQL->fetch_array($survey_participant_count_results);
		$survey_participant_count = $survey_participant_count_row['COUNT(*)'];
		$participants = $survey_participant_count;
		
		//ask lime how many people have responded (seems to have issue if at least one participant is not assigned)
		$response_count = 0;
		if ($survey_participant_count > 0)
		{
			$response_count = $lime_rpc->get_summary($lime_session_key, $survey_id, 'full_responses');	
		}
		$responses = $response_count;
		
	
		$survey_row = $this->qls->SQL->select_one_simple('*', 'surveys', array('id' => $survey_id));			
		if ($survey_row != null) 
		{
			return array($survey_row['id'], $survey_row['name'], ($survey_row['auto_assign_new_participant'] == 'Y'), $participants, $responses);			
		}
		return null;		
	}	
	
	
	
	
	
	
	
	/*************************
	 *     Participants      *  
	 *************************/
	 
	
	/* create a new participant, assign the participant to all survey marked auto assign */	 
	function create_new_participant($email)
	{
		//make sure no one already registered that email
		$participant_row = $this->qls->SQL->select_one_simple('*', 'participants', array('email' => $email));
		if ($participant_row != null){ return false; }
	
		//insert participant and get their id
		$this->qls->SQL->insert_simple('participants', array('email' => $email));
		$participant_id = $this->qls->SQL->insert_id();
		
		//create_new_participant happens based on action the participant made (so no one is logged in at the time)
		//this means there will not be a lime session open at the time.  
		//So lets open one now, before we try and assign the new participant to surveys
		$lime_session = $this->start_lime_session();
		$this->qls->user_info['lime_session'] = $lime_session;
			
		//get all survey marked auto assign, and assign participant to those
		$survey_results = $this->qls->SQL->select_simple('id', 'surveys', array('auto_assign_new_participant' => 'Y'));			
		while ($survey_row = $this->qls->SQL->fetch_array($survey_results)) 
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
		if ($this->is_participant_assigned($participant_id, $survey_id)){ return; }		
	
		//get email for the participant with that id
		$participant_info = $this->qls->SQL->select_one_simple(array('email'), 'participants', array('id' => $participant_id));
		
		//create array of data for this participant to send to lime (could put first and last name in here too if we had them)
		$participant_data = array(
		'email' => $participant_info['email'],
		'emailstatus' => 'OK' //it will not send the email unless we set this too
		);	
		
		//get connection to lime
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	
				
		//add the participant, get the token that was created for him (Note the method actually takes an array of partcipants)
		$add_participant_results = $lime_rpc->add_participants($lime_session_key, $survey_id, array($participant_data) );		
		$token_id = $add_participant_results[0]['tid'];
		$token = $add_participant_results[0]['token'];
		
		
		//send a invite to the participant (the function sends invite to all that have not been reminded which should be just the new participant)		
		$invite_participant_results = $lime_rpc->invite_participants($lime_session_key, $survey_id);								
		//TODO: if we are going to send more than one then we need to check results because i think it will only send X at a time 
		
											
		//add to our participant_surveys table		
		$this->qls->SQL->insert_simple('participant_surveys',
			array(
				'participant_id' => $participant_id,
				'survey_id' => $survey_id,
				'token_id' => $token_id,
				'token' => $token,
			)
		);		
	}
		
		
    /* unassign the participant with the id pass to the survey passed */
	function unassign_to_survey($participant_id, $survey_id)
	{	
		//make sure participant is already assigned
		if ($this->is_participant_assigned($participant_id, $survey_id) == false){ return; }
		
		//make sure participant has not already completed survey
		if ($this->participant_completed_date($participant_id, $survey_id) != 'N'){ return; }
		
		//get token id for that participant in that survey
		$participant_survey_info = $this->qls->SQL->select_one_simple('token_id', 'participant_surveys', array('participant_id' => $participant_id, 'survey_id' => $survey_id));
		$token_id = $participant_survey_info['token_id'];
		
		//connect to lime
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	

		//remove the participant, (Note the method actually takes an array of token ids)
		$results = $lime_rpc->delete_participants($lime_session_key, $survey_id, array($token_id) );		
											
		//remove from our participant_surveys table		
		$this->qls->SQL->delete_simple('participant_surveys',
			array(
				'participant_id' => $participant_id,
				'survey_id' => $survey_id				
			)
		);	
	}
		
	
	/* get true or false if the participant passed is assigned to the survey passed */
	function is_participant_assigned($participant_id, $survey_id)
	{
		$resultrow = $this->qls->SQL->select_one_simple('*', 'participant_surveys',
			array(
				'participant_id' => $participant_id,
				'survey_id' => $survey_id				
			)
		);
				
		return ($resultrow != null);
	}	
	
		
	/* get 'N' or the date the participant completed the survey */
	function participant_completed_date($participant_id, $survey_id)
	{
		$participant_survey_row = $this->qls->SQL->select_one_simple('token_id', 'participant_surveys',
			array(
				'participant_id' => $participant_id,
				'survey_id' => $survey_id				
			)
		);
		
		//not even assigned, so of course they are not completed
		if ($participant_survey_row == null) { return false; }
		
		//participants token for the survey
		$token_id = $participant_survey_row['token_id'];
			
		//get lime connection
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	
		
		//get if the participant has completed the survey, and when
		$results = $lime_rpc->get_participant_properties($lime_session_key, $survey_id, $token_id, array('completed'));												
		$completed = $results['completed'];
						
		//if completed is not 'N' its complete
		return $completed;
	}
	
	
	/* search participant (currently just on email) */
	function search_participants($email)
	{
		$participant_ids = array();
		$participant_emails = array();
	
		$participants_table = $this->qls->config['sql_prefix'] . 'participants';
		$participants_results = $this->qls->SQL->query("SELECT `id`, `email` FROM `{$participants_table}` WHERE `email` LIKE '%{$email}%'");	
				
		while ($participant_row = $this->qls->SQL->fetch_array($participants_results)) 
		{
			$participant_ids[] = $participant_row['id'];
			$participant_emails[] = $participant_row['email'];			
		}
		return array($participant_ids, $participant_emails);
	}	
	
	
	/* search participant (currently just on email), also return if they are assigned or have completed the survey passed */
	function search_participants_in_survey($email, $survey_id)
	{
		//search participants
		list ($participant_ids, $participant_emails) = $this->search_participants($email);
		$participant_assigneds = array();
		$participant_completeds = array();
		
		//for each participant get if they have completed the survey, or if they are even assigned
		foreach ($participant_ids as $participant_id)
		{
			$participant_assigneds[] = $this->is_user_assigned($participant_ids, $survey_id);
			$participant_completeds[] = $this->user_completed_date($participant_ids, $survey_id);		
		}
		
		return array($participant_ids, $participant_emails, $participant_assigneds, $participant_completeds);
	}
	

}
	
?>