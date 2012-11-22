<?php

/**
 * Contains all Survey functions
 */
class Surveys {
	
	
	/**
	 * Construct class
	 * @param object $this->qls - Contains all other classes
	 * @return void
	 */
	function Surveys() 
	{	
	}
			
		
		
		
	/*************************
	 *        Surveys        *  
	 *************************/
		
	
	
	/* make a survey be auto matically assigned to a new participant */
	function set_survey_auto_assign($survey_id, $auto_assign)
	{
		$auto_assign_char = 'N';
		if ($auto_assign) { $auto_assign_char = 'Y'; }
	
		//update the survey in database
		$sql="UPDATE inar_surveys SET auto_assign_new_participant=:auto_assign WHERE id=:id";
		$command=Yii::app()->db->createCommand($sql);	
		$command->bindParam(":auto_assign", $auto_assign_char, PDO::PARAM_STR);				
		$command->bindParam(":id", $survey_id, PDO::PARAM_INT);
		$command->execute();		
	}
	
	
	/* get survey info for all surveys */
	function get_all_surveys_info()
	{
		
		$aUserSurveys = Survey::model()->findAll(); //list all surveys
		foreach ($aUserSurveys as $oSurvey)
		{
			$survey_info = array();
			
			//surveys id
			$survey_info['id'] = $oSurvey->primaryKey;
							
			//get the title of the survey			
			$oSurveyLanguageSettings = Surveys_languagesettings::model()->findByAttributes(array('surveyls_survey_id' => $oSurvey->primaryKey, 'surveyls_language' => $oSurvey->language));
			if (!isset($oSurveyLanguageSettings))
			{
				$survey_info['title'] = 'No ' . $oSurvey->language . ' Title';
			}
			else
			{
				$survey_info['title'] = $oSurveyLanguageSettings->attributes['surveyls_title'];
			}					
						
			
			//is the survey active
			$survey_info['active'] = $oSurvey->attributes['active'];
			
			//check survey for issues with the way it is setup
			$survey_info['issues'] = $this->check_survey($oSurvey);
			
			//put survey info into data to pass to view
			$aData['surveys'][] = $survey_info;	
		}
	
	
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
						
			//ask lime how many people have responded (this is throwing an exception if 0 people have completed)
			$response_count = 0;
			try 
			{
				$response_count = $lime_rpc->get_summary($lime_session_key, intval($survey_row['id']), 'full_responses');	
			}
			catch (Exception $e) 
			{
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
		
		//ask lime how many people have responded (this is throwing an exception if 0 people have completed)
		$response_count = 0;
		try 
		{
			$response_count = $lime_rpc->get_summary($lime_session_key, intval($survey_id), 'full_responses');	
		}
		catch (Exception $e) 
		{
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
	function create_new_participant($array1)
	{
		//make sure no one already registered that email
                $email = $array1['email'];
		$participant_row = $this->qls->SQL->select_one_simple('*', 'participants', array('email' => $email));
		if ($participant_row != null){ return false; }
	        

		//insert participant and get their id
                $date = date("Y-m-d");
                $array1['date']=$date;
		//$this->qls->SQL->insert_simple('participants', array('email' => $email,'date' => $date));
                
                $this->qls->SQL->insert_simple('participants', $array1);
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
                //not actually doing anything, so what if the user was deleted and is registering again?
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
                //was throwing exception, but was still sending email...	
                 try
                {	
		$invite_participant_results = $lime_rpc->invite_participants($lime_session_key, $survey_id);	
                }
                catch(Exception $e) 
		{
		}							
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
		
		//seems like it sometimes send back '' for not completed
		if ($completed == 'N'){ $completed ='N'; }
						
		//if completed is not 'N' its complete
		return $completed;
	}
	
	
	/* search participant (currently just on email) */
	private function search_participants_date($email,$date1,$date2)
	{
		$participant_ids = array();
		$participant_emails = array();
	
		$participants_table = $this->qls->config['sql_prefix'] . 'participants';
		$participants_results = $this->qls->SQL->query("SELECT `id`, `email` FROM `{$participants_table}` WHERE `email` LIKE '%{$email}%' AND `date` between '{$date1}' AND '$date2'");	
				
		while ($participant_row = $this->qls->SQL->fetch_array($participants_results)) 
		{
			$participant_ids[] = $participant_row['id'];
			$participant_emails[] = $participant_row['email'];			
		}
		return array($participant_ids, $participant_emails);
	}
        private function search_participants_inner($email)
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
       function search_participants_month($month)
	{
         $dates = array();
         $emails = array();
          $participants_table = $this->qls->config['sql_prefix'] . 'participants';
	  $sql3 = "SELECT date,email FROM `{$participants_table}` WHERE `date` BETWEEN '2012-$month-01' AND '2012-$month-31'";
         $result = $this->qls->SQL->query($sql3);
       if (!$result) 
       {
          die('Invalid query: ' . mysql_error());
       }
         
        while($row = $this->qls->SQL->fetch_array($result))
        {
         $dates[] = $row['date'];
         $emails[] = $row['email'];
         }
       return array($dates,$emails);
       }
	/* search participant, also return what surveys they have been assigned to */
	function search_participants($email,$date1,$date2)
	{
		//search participants
                if($date1==""&&$date2=="")
                list ($participant_ids, $participant_emails) = $this->search_participants_inner($email);
                else
		list ($participant_ids, $participant_emails) = $this->search_participants_date($email,$date1,$date2);
		$participant_surveyss = array();
				
		//for each participant get if they have completed the survey, or if they are even assigned
		foreach ($participant_ids as $participant_id)
		{
			$participant_surveys = array();
			$participant_surveys_table = $this->qls->config['sql_prefix'] . 'participant_surveys';
			$surveys_table = $this->qls->config['sql_prefix'] . 'surveys';
			$participants_surveys_result = $this->qls->SQL->query("SELECT `{$surveys_table}`.`name` FROM `{$surveys_table}`, `{$participant_surveys_table}` WHERE `{$participant_surveys_table}`.`participant_id` = {$participant_id} AND `{$participant_surveys_table}`.`survey_id` = `{$surveys_table}`.`id`");	
			while ($participants_surveys_row = $this->qls->SQL->fetch_array($participants_surveys_result)) 
			{
				$participant_surveys[] = $participants_surveys_row['name'];
			}
			$participant_surveyss[] = $participant_surveys;			
		}
	
		return array($participant_ids, $participant_emails, $participant_surveyss);
	}	
	
	
	/* search participant, also return if they are assigned or have completed the survey passed */
	function search_participants_in_survey($email, $survey_id)
	{
		//search participants
		list ($participant_ids, $participant_emails) = $this->search_participants_inner($email);
		$participant_assigneds = array();
		$participant_completeds = array();
		
		//for each participant get if they have completed the survey, or if they are even assigned
		foreach ($participant_ids as $participant_id)
		{
			$participant_assigneds[] = $this->is_participant_assigned($participant_id, $survey_id);
			$participant_completeds[] = $this->participant_completed_date($participant_id, $survey_id);		
		}
		
		return array($participant_ids, $participant_emails, $participant_assigneds, $participant_completeds);
	}
	

}
	
?>