<?php

if (!defined('QUADODO_IN_SYSTEM')) {
exit;	
}

/**
 * Contains all Survey functions
 */
class Surveys {
	
	require_once 'jsonrpcphp\includes\jsonRPCClient.php';

	/**
	 * @var object $this->qls - Will contain everything else
	 */
	var $qls;

	/**
	 * Construct class
	 * @param object $this->qls - Contains all other classes
	 * @return void
	 */
	function Surveys(&$qls) {
	$this->qls = &$qls;
	}

	/* export a survey in the format passed */
	function export_survey($sid, $format)
	{
		//connect via json
		$myJSONRPCClient = new jsonRPCClient('http://indiaautismregistry.com/TEST2/limesurvey/admin/remotecontrol');
		$sessionKey = $myJSONRPCClient->get_session_key('inar2012', 'Inar!2012');

		//get responses
		$data = $myJSONRPCClient->export_responses($sessionKey, $sid, 'xls');

		//dissconnect
		$myJSONRPCClient->release_session_key( $sessionKey );
		
		//return data?
		return $data;
	}
	
	
    /*
     * Assign the user with the id pass to the survey passed (if they are eligable to be assigned to it)
     */
	function assign_to_survey($lime_participant_id, $sid)
	{	
		//get all info for the user with that participant id
		$user_info_result = $this->qls->SQL->query("SELECT `username`, `email` FROM `{$this->qls->config['sql_prefix']}users` WHERE `lime_participant_id`='{$lime_participant_id}'");					
		$user_info_row = $this->qls->SQL->fetch_array($user_info_result);		
		if ($user_info_row == null)
		{		
			return;
		}

		//create array of data for this participant to send to lime
		$participant_data = array(
			'participant_id' => $lime_participant_id,
			'firstname' => $user_info_row['username'],
			'email' => $user_info_row['email']);
			
		
		//connect via json
		$myJSONRPCClient = new jsonRPCClient('http://indiaautismregistry.com/TEST2/limesurvey/admin/remotecontrol');
		$sessionKey = $myJSONRPCClient->get_session_key('inar2012', 'Inar!2012');

		//add the participant (Note the method actually takes an array of partcipants)
		$result = $myJSONRPCClient->add_participants($sessionKey, $sid, array($participant_data) );

		var_dump($result);
		
		//dissconnect
		$myJSONRPCClient->release_session_key( $sessionKey );
					
	
		/*
	
		//get row for survey, give up if you cant find it
		$survey_results = $this->qls->SQL->query("SELECT `active` FROM `{$this->qls->config['lime_sql_prefix']}surveys` WHERE `sid`='{$sid}'");	
		$survey_row = $this->qls->SQL->fetch_array($survey_results);
		if ($survey_row == null)
		{
			return;
		}
		
		//check for issues that prevent adding user to the survey (no token table, not active)
		//if any of these exsits we give up and return
		$survey_issue = '';
		$token_exsists_result = $this->qls->SQL->query("SHOW TABLES LIKE '{$this->qls->config['lime_sql_prefix']}tokens_{$sid}'");					
		if ($this->qls->SQL->num_rows($token_exsists_result) == 0)
		{		
			return;
		}
		if ($survey_row['active'] != 'Y')
		{
			return;
		}
		
		//make sure the user is not already assigned to this survey
		$surveys_links_result = $this->qls->SQL->query("SELECT COUNT(*) AS `count` FROM `{$this->qls->config['lime_sql_prefix']}survey_links` WHERE `survey_id`='{$sid}' AND `participant_id`='{$lime_participant_id}'");					
		$surveys_links_row = $this->qls->SQL->fetch_array($surveys_links_result);		
		if ($surveys_links_row['count'] > 0)
		{		
			return;
		}
	
		//get all info for the user with that participant id
		$user_info_result = $this->qls->SQL->query("SELECT `username`, `email` FROM `{$this->qls->config['sql_prefix']}users` WHERE `lime_participant_id`='{$lime_participant_id}'");					
		$user_info_row = $this->qls->SQL->fetch_array($user_info_result);		
		if ($user_info_row == null)
		{		
			return;
		}
		
		//lime survey has more complicated token generation code (see Tokens_dynamic.php)
		//it actually checks new tokens against all other tokens, but it seems to me like would get very slow to me so im just going to give it a uuid.				
		$token = $this->gen_uuid();
		$token = str_replace('-', '', $token);
	
		// Insert participant into that surveys token table
		$this->qls->SQL->insert_lime('tokens_' . $sid,
			array(
				'participant_id',
				'firstname',
				'lastname',
				'email',
				'emailstatus',
				'token',						
				'language'
			),
			array(
				$lime_participant_id,
				$user_info_row['username'],
				'',
				$user_info_row['email'],
				'OK',
				$token,
				'en'						
			)
		);
		
		//id for the token we just inserted
		$token_id = $this->qls->SQL->insert_id();
		
		// Also insert record into survey links table
		$this->qls->SQL->insert_lime('survey_links',
			array(
				'participant_id',
				'token_id',
				'survey_id',
				'date_created'
			),
			array(
				$lime_participant_id,
				$token_id,
				$sid,
				'NOW()'			
			)
		);
		
		*/
	}
	
	
    /*
     * unassign the user with the id pass to the survey passed (if they have not already completed it )
     */
	function unassign_to_survey($lime_participant_id, $sid)
	{			
		//check for issues that prevent remove the participant from the survey (no token table)
		$survey_issue = '';
		$token_exsists_result = $this->qls->SQL->query("SHOW TABLES LIKE '{$this->qls->config['lime_sql_prefix']}tokens_{$sid}'");					
		if ($this->qls->SQL->num_rows($token_exsists_result) == 0)
		{		
			return;
		}
		
		//make sure the user is already assigned to this survey
		$surveys_links_result = $this->qls->SQL->query("SELECT `date_completed` FROM `{$this->qls->config['lime_sql_prefix']}survey_links` WHERE `survey_id`='{$sid}' AND `participant_id`='{$lime_participant_id}'");					
		$surveys_links_row = $this->qls->SQL->fetch_array($surveys_links_result);		
		if ($surveys_links_row == null)
		{		
			return;
		}
		if ($surveys_links_row['date_completed'] != null)
		{	
			return;
		}
			
		//delete from tokens table
		$this->qls->SQL->query("DELETE FROM `{$this->qls->config['lime_sql_prefix']}tokens_{$sid}` WHERE `participant_id`='{$lime_participant_id}'");	
		
		//delete from links table
		$this->qls->SQL->query("DELETE FROM `{$this->qls->config['lime_sql_prefix']}survey_links` WHERE `participant_id`='{$lime_participant_id}'");	
	}
	
	

	
    /*
     * Generation of unique id (this function was copied from lime survey, use the same method to generate uuid that they do)
     */
    function gen_uuid()
    {
        return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0x0fff) | 0x4000,
                mt_rand(0, 0x3fff) | 0x8000,
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff),
                mt_rand(0, 0xffff)
        );
    }
	
		

}
	
?>