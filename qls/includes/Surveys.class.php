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
			'allowsave' => 'N');
		$response = $lime_rpc->set_survey_properties($lime_session_key, $new_survey_id, $survey_properties);
				
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
	
	
	/* make a survey be auto matically assigned to a new user */
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
	
	
	/* auto assign a new user to all surveys marked auto assign*/
	function auto_assign_new_user($user_id)
	{
		//get all survey marked auto assign
		$survey_results = $this->qls->SQL->select_simple('id', 'surveys', array('auto_assign_new_participant' => 'Y'));			
		while ($survey_row = $this->qls->SQL->fetch_array($survey_results)) 
		{
			$this->assign_to_survey($user_id, $survey_row['id']);
		}
	}	
	
	
    /*
     * Assign the user with the id pass to the survey passed 
     */
	function assign_to_survey($user_id, $survey_id)
	{	
		//make sure user is not already assigned
		if ($this->is_user_assigned($user_id, $survey_id)){ return; }		
	
		//get email and user name for the user with that id
		$user_info = $this->qls->SQL->select_one_simple(array('username', 'email'), 'users', array('id' => $user_id));
		
		//create array of data for this participant to send to lime
		$participant_data = array(			
			'firstname' => $user_info['username'],
			'email' => $user_info['email']);	
		
		//connect to lime
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	
				
		//add the participant, get the token that was created for him (Note the method actually takes an array of partcipants)
		$results = $lime_rpc->add_participants($lime_session_key, $survey_id, array($participant_data) );		
		$token_id = $results[0]['tid'];
		$token = $results[0]['token'];
									
		//add to our user_surveys table		
		$this->qls->SQL->insert_simple('user_surveys',
			array(
				'user_id' => $user_id,
				'survey_id' => $survey_id,
				'token_id' => $token_id,
				'token' => $token,
			)
		);
	}
	
	
    /*
     * unassign the user with the id pass to the survey passed 
     */
	function unassign_to_survey($user_id, $survey_id)
	{	
		//make sure user is already assigned
		if ($this->is_user_assigned($user_id, $survey_id) == false){ return; }
		
		//make sure user has not already completed survey
		if ($this->user_completed_date($user_id, $survey_id) != 'N'){ return; }
		
		//get token id for that user in that survey
		$user_survey_info = $this->qls->SQL->select_one_simple('token_id', 'user_surveys', array('user_id' => $user_id, 'survey_id' => $survey_id));
		$token_id = $user_survey_info['token_id'];
		
		//connect to lime
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	

		//remove the participant, (Note the method actually takes an array of token ids)
		$results = $lime_rpc->delete_participants($lime_session_key, $survey_id, array($token_id) );		
											
		//remove from our user_surveys table		
		$this->qls->SQL->delete_simple('user_surveys',
			array(
				'user_id' => $user_id,
				'survey_id' => $survey_id				
			)
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
			
			$user_surveys_table = $this->qls->config['sql_prefix'] . 'user_surveys';
			$survey_participant_count_results = $this->qls->SQL->query("SELECT COUNT(*) FROM `{$user_surveys_table}` WHERE `survey_id`={$survey_row['id']}");
			$survey_participant_count_row = $this->qls->SQL->fetch_array($survey_participant_count_results);
			$participants[] = $survey_participant_count_row['COUNT(*)'];
						
			//ask lime how many people have responded
			$response_count = $lime_rpc->get_summary($lime_session_key, $survey_row['id'], 'full_responses');	
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
	
		$user_surveys_table = $this->qls->config['sql_prefix'] . 'user_surveys';
		$survey_participant_count_results = $this->qls->SQL->query("SELECT COUNT(*) FROM `{$user_surveys_table}` WHERE `survey_id`={$survey_id}");
		$survey_participant_count_row = $this->qls->SQL->fetch_array($survey_participant_count_results);
		$participants = $survey_participant_count_row['COUNT(*)'];
		
		//ask lime how many people have responded
		$response_count = $lime_rpc->get_summary($lime_session_key, $survey_id, 'full_responses');	
		$responses = $response_count;
		
	
		$survey_row = $this->qls->SQL->select_one_simple('*', 'surveys', array('id' => $survey_id));			
		if ($survey_row != null) 
		{
			return array($survey_row['id'], $survey_row['name'], ($survey_row['auto_assign_new_participant'] == 'Y'), $participants, $responses);			
		}
		return null;		
	}	
	
	
	
	
	/* get true or false if the user passed is assigned to the survey passed */
	function is_user_assigned($user_id, $survey_id)
	{
		$resultrow = $this->qls->SQL->select_one_simple('*', 'user_surveys',
			array(
				'user_id' => $user_id,
				'survey_id' => $survey_id				
			)
		);
				
		return ($resultrow != null);
	}	
	
		
	/* get 'N' or the date the user completed the survey */
	function user_completed_date($user_id, $survey_id)
	{
		$user_survey_row = $this->qls->SQL->select_one_simple('token_id', 'user_surveys',
			array(
				'user_id' => $user_id,
				'survey_id' => $survey_id				
			)
		);
		
		//user if not even assigned, so of course they are not completed
		if ($user_survey_row == null) { return false; }
		
		//users token for the survey
		$token_id = $user_survey_row['token_id'];
			
		//get lime connection
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	
		
		//get if the participant has completed the survey, and when
		$results = $lime_rpc->get_participant_properties($lime_session_key, $survey_id, $token_id, array('completed'));												
		$completed = $results['completed'];
						
		//if completed is not 'N' its complete
		return $completed;
	}
	
	/* get survey info for surveys a user can take*/
	function get_survey_info_for_user($user_id)
	{		
		//get lime connection
		$lime_rpc = $this->get_lime_connection();
		$lime_session_key = $this->qls->user_info['lime_session'];	
	
		$ids = array();
		$names = array();
		$tokens = array();
		$completes = array();		
	
		//get all surveys for the user
		$surveys_table = $this->qls->config['sql_prefix'] . 'surveys';
		$user_surveys_table = $this->qls->config['sql_prefix'] . 'user_surveys';		
		$survey_results = $this->qls->SQL->query("SELECT `{$surveys_table}`.`id`, `{$surveys_table}`.`name`, `{$user_surveys_table}`.`token`, `{$user_surveys_table}`.`token_id` FROM `{$surveys_table}`, `{$user_surveys_table}` WHERE `{$user_surveys_table}`.`user_id`={$user_id} AND `{$user_surveys_table}`.`survey_id`=`{$surveys_table}`.`id`");
		while ($survey_row = $this->qls->SQL->fetch_array($survey_results)) 
		{
			$ids[] = $survey_row['id'];
			$names[] =  $survey_row['name'];
			$tokens[] =  $survey_row['token'];
			
			//get if the participant has completed the survey, and when
			$results = $lime_rpc->get_participant_properties($lime_session_key, $survey_row['id'], $survey_row['token_id'], array('completed'));												
			$completes[] = $results['completed'];
		}		
				
		//return all the info
		return array($ids, $names, $tokens, $completes);
	}	
	
	
	

}
	
?>