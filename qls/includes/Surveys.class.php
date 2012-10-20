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

	
	
	
	/* create a new survey */
	function create_survey($name, $data)
	{
		//connect to lime
		$myJSONRPCClient = new jsonRPCClient($qls->config['lime_location']);
		$sessionKey = $myJSONRPCClient->get_session_key($qls->config['lime_user'], $qls->config['lime_password']);

		//create the survey
		$new_survey_id = $myJSONRPCClient->import_survey($sessionKey, $data, 'lss', $name);
		
		//insert the new survey on our side
		$this->qls->SQL->insert_simple('surveys',
			array(
				'id' => $new_survey_id,
				'name' => $name
			)
		);
		
		//set properties for the survey
		$survey_properties = array(
			'usetokens' => 'Y',
			'tokenanswerspersistence' => 'Y'
			'allowsave' => 'N');
		$myJSONRPCClient->set_survey_properties($sessionKey, $new_survey_id, $survey_properties);
		
		
		//activate the survey
		$myJSONRPCClient->activate_survey($sessionKey, $new_survey_id);
				
		//dissconnect
		$myJSONRPCClient->release_session_key( $sessionKey );		
	}
	
	
	
	/* export a survey in the format passed */
	function export_survey($survey_id, $format)
	{
		//connect to lime
		$myJSONRPCClient = new jsonRPCClient($qls->config['lime_location']);
		$sessionKey = $myJSONRPCClient->get_session_key($qls->config['lime_user'], $qls->config['lime_password']);

		//get responses
		$data = $myJSONRPCClient->export_responses($sessionKey, $survey_id, 'xls');

		//dissconnect
		$myJSONRPCClient->release_session_key( $sessionKey );
		
		//return data?
		return $data;
	}
	
	
	/* make a survey be auto matically assigned to a new user */
	function set_survey_auto_assign($survey_id, $auto_assign)
	{
		//update the survey in database
		$this->qls->SQL->update_simple('surveys',
			array('auto_assign_new_participant' => $auto_assign),
			array('survey_id' => $survey_id),
		);	
	}
	
	
	/* auto assign a new user to all surveys marked auto assign*/
	function auto_assign_new_user($user_id)
	{
		//get all survey marked auto assign
		$survey_results = $this->qls->SQL->select_simple('id', 'surveys', array('auto_assign_new_participant' => true));			
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
		$myJSONRPCClient = new jsonRPCClient($qls->config['lime_location']);
		$sessionKey = $myJSONRPCClient->get_session_key($qls->config['lime_user'], $qls->config['lime_password']);

		//add the participant, get the token that was created for him (Note the method actually takes an array of partcipants)
		$results = $myJSONRPCClient->add_participants($sessionKey, $survey_id, array($participant_data) );		
		$token = $results['token'];
		
		//dissconnect
		$myJSONRPCClient->release_session_key( $sessionKey );
					
		//add to our user_surveys table		
		$this->qls->SQL->insert_simple('user_surveys',
			array(
				'user_id' => $user_id,
				'survey_id' => $survey_id,
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
		
		//get email and user name for the user with that id
		$user_survey_info = $this->qls->SQL->select_one_simple(array('username', 'email'), 'user_surveys', array('user_id' => $user_id, 'survey_id' => $survey_id));
		$token = $user_survey_info['token'];
		
		//connect to lime
		$myJSONRPCClient = new jsonRPCClient($qls->config['lime_location']);
		$sessionKey = $myJSONRPCClient->get_session_key($qls->config['lime_user'], $qls->config['lime_password']);

		//remove the participant, (Note the method actually takes an array of tokens)
		$results = $myJSONRPCClient->add_participants($sessionKey, $survey_id, array($token) );		
				
		//dissconnect
		$myJSONRPCClient->release_session_key( $sessionKey );
					
		//remove from our user_surveys table		
		$this->qls->SQL->delete_simple('user_surveys',
			array(
				'user_id' => $user_id,
				'survey_id' => $survey_id				
			)
		);	
	}
	
	
	
	
	
	
	
	
	
	/* get survey info for all surveys */
	function get_survey_info()
	{
		$ids = array();
		$names = array();
		$auto_assigns = array();
		$participants = array();
		$responses = array();
	
		//get all surveys
		$surveys_results = $this->qls->SQL->select('*', 'surveys'));			
		while ($survey_row = $this->qls->SQL->fetch_array($survey_results)) 
		{
			$ids[] = $survey_row['id'];
			$names[] =  $survey_row['name'];
			$auto_assign[] =  $survey_row['auto_assign_new_participant'];			
			$participants[] = 0;
			$responses[] = 0;
		}
		
		//return all the info
		return array($ids, $names, $auto_assigns, $participants, $responses);
	}

	
	/* get survey info for one surveys */
	function get_survey_info($survey_id)
	{	
		$survey_row = $this->qls->SQL->select_one_simple('*', 'surveys'));			
		if ($survey_row != null) 
		{
			return array($survey_row['id'], $survey_row['name'], $survey_row['auto_assign_new_participant'], 0, 0);			
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
	
	
	
	/* get survey info for surveys a user can take*/
	function get_survey_info_for_user($user_id)
	{
		$ids = array();
		$names = array();
		$tokens = array();
		$complete = array();
	
		//get all surveys for the user
		$survey_results = $qls->SQL->query("SELECT s.`id`, s.`name`, us.`token` FROM `{$qls->config['sql_prefix']}surveys` s, `{$qls->config['sql_prefix']}user_surveys` us WHERE us.`user_id`='{$user_id}' AND us.`survey_id`=s.`id`'");					
		while ($survey_row = $this->qls->SQL->fetch_array($survey_results)) 
		{
			$ids[] = $survey_row['id'];
			$names[] =  $survey_row['name'];
			$tokens[] =  $survey_row['token'];
			$complete[] = false; //TODO 			
		}				
		
		//return all the info
		return array($ids, $names, $tokens, $complete);
	}	
	
	
	

}
	
?>