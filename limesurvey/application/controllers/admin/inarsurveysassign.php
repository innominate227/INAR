<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * LimeSurvey
 * Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v2 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 *	$Id$
 */
class InarSurveysAssign extends Survey_Common_Action
{
	
	/* get 'N' or the date the participant completed the survey */	
	
	function participant_completed_date($participant_id, $survey_id)
	{
		$command = Yii::app()->db->createCommand()
			->select("token_id")
			->from("inar_participant_surveys")
			->where("survey_id=:survey_id AND participant_id=:participant_id");            
		$command->bindParam(":survey_id", $survey_id, PDO::PARAM_INT);
		$command->bindParam(":participant_id", $participant_id, PDO::PARAM_INT);
		$results = $command->queryAll();
		
		//not even assigned, so of course they are not completed
		if (count($results) == 0)
		{
			return 'N';
		}
				
		//participants token for the survey
		$token_id = $results[0]['token_id'];
		
		
		$table_name = 'lime_tokens_' . $survey_id;
		if (Yii::app()->db->schema->getTable($table_name)) 
		{
			$command = Yii::app()->db->createCommand()
				->select("completed")
				->from($table_name)
				->where("tid=:token_id");            			
			$results = $command->queryAll();		
			return $results[0]['completed'];
		}
		else
		{
			return 'N';
		}		
	}
	

	/* get true or false if the participant passed is assigned to the survey passed */
	
	function is_participant_assigned($participant_id, $survey_id)
	{
		$command = Yii::app()->db->createCommand()
			->select("token_id")
			->from("inar_participant_surveys")
			->where("survey_id=:survey_id AND participant_id=:participant_id");            
		$command->bindParam(":survey_id", $survey_id, PDO::PARAM_INT);
		$command->bindParam(":participant_id", $participant_id, PDO::PARAM_INT);
		$results = $command->queryAll();
		return (count($results) != 0);		
	
	}

	
		
    /* Assign the participant with the id pass to the survey passed */
	
	function assign_to_survey($participant_id, $survey_id)
	{	
		//make sure participant is not already assigned
        //not actually doing anything, so what if the user was deleted and is registering again?
		if ($this->is_participant_assigned($participant_id, $survey_id)){ return; }		
	
		//get email for the participant with that id		
		$command = Yii::app()->db->createCommand()
			->select("email")
			->from("inar_participants")
			->where("id=:participant_id");            		
		$command->bindParam(":participant_id", $participant_id, PDO::PARAM_INT);
		$results = $command->queryAll();
		$participant_info = $results[0];
		
		
		//create array of data for this participant to send to lime (could put first and last name in here too if we had them)
		$participant_data = array(
		'email' => $participant_info['email'],
		'emailstatus' => 'OK' //it will not send the email unless we set this too
		);	
		
		//no token table we cant do anything
		if (!Yii::app()->db->schema->getTable('{{tokens_' . $survey_id . '}}'))
		{
			return;
		}
				
		Yii::app()->loadHelper('admin/token');	
        
		Tokens_dynamic::sid($survey_id);
		$token = new Tokens_dynamic;
		$token_id = $token->insertParticipant($participant_data);
		$token = Tokens_dynamic::model()->createToken($token_id);		
						
		
		//send a invite to the participant (the function sends invite to all that have not been reminded which should be just the new participant)
        //was throwing exception, but was still sending email...	
        try
        {			
			$oSurvey = Survey::model()->findByPk($survey_id);		   				
			if(!tableExists("{{tokens_$survey_id}}"))
			{
				return;
			}
							
			$oTokens = Tokens_dynamic::model($survey_id);				
			$aAllTokens = $oTokens->findUninvited(false, 0, true, $SQLemailstatuscondition);
			emailTokens($survey_id, $aAllTokens, 'invite');
        }
        catch(Exception $e) 
		{
		}							
				
		//add to table
		$command=Yii::app()->db->createCommand("INSERT INTO inar_participant_surveys (participant_id, survey_id, token_id, token) VALUES(:participant_id, :survey_id, :token_id, :token)");	
		$command->bindParam(":participant_id", $auto_assign_char, PDO::PARAM_INT);				
		$command->bindParam(":survey_id", $survey_id, PDO::PARAM_INT);
		$command->bindParam(":token_id", $survey_id, PDO::PARAM_INT);
		$command->bindParam(":token", $survey_id, PDO::PARAM_STR);
		$command->execute();			
	}
		
		
    /* unassign the participant with the id pass to the survey passed */	
	function unassign_to_survey($participant_id, $survey_id)
	{	
		//make sure participant is already assigned
		if ($this->is_participant_assigned($participant_id, $survey_id) == false){ return; }
		
		//make sure participant has not already completed survey
		if ($this->participant_completed_date($participant_id, $survey_id) != 'N'){ return; }
		
		//get token id for that participant in that survey
		$command = Yii::app()->db->createCommand()
			->select("token_id")
			->from("inar_participant_surveys")
			->where("participant_id=:participant_id AND survey_id=:survey_id");            		
		$command->bindParam(":participant_id", $participant_id, PDO::PARAM_INT);
		$command->bindParam(":survey_id", $survey_id, PDO::PARAM_INT);
		$results = $command->queryAll();
		$participant_survey_info = $results[0];		
		$token_id = $participant_survey_info['token_id'];
		
		//delete token		
		if(!tableExists("{{tokens_$survey_id}}"))
		{
			return;
		}				
		$tokenidExists = Tokens_dynamic::model($survey_id)->findByPk($token_id);
		if (isset($tokenidExists))
		{
			Survey_links::deleteTokenLink(array($token_id), $survey_id);
			Tokens_dynamic::model($iSurveyID)->deleteRecords(array($token_id));			
		}			
											
		//remove from our participant_surveys table		
		$command=Yii::app()->db->createCommand("DELETE FROM inar_participant_surveys WHERE participant_id=:participant_id, survey_id=:survey_id)");	
		$command->bindParam(":participant_id", $participant_id, PDO::PARAM_INT);				
		$command->bindParam(":survey_id", $survey_id, PDO::PARAM_INT);
		$command->execute();		
	}
	

	
	private function search_participants_inner($email)
	{
		$participant_ids = array();
		$participant_emails = array();

		$command = Yii::app()->db->createCommand()
			->select("id, email")
			->from("inar_participants")
			->where("email LIKE :email");            
		$command->bindParam(":email", $email, PDO::PARAM_STR);
		$results = $command->queryAll();	
		
		foreach ($results as $participant_row) 
		{
			$participant_ids[] = $participant_row['id'];
			$participant_emails[] = $participant_row['email'];			
		}
		return array($participant_ids, $participant_emails);
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
	

    public function run()
    {
        $clang = Yii::app()->lang;
		
		
		$survey_id = $_GET['sid'];
		$aData['survey_id'] = $survey_id;	
		
		
		$oSurveyLanguageSettings = Surveys_languagesettings::model()->findByAttributes(array('surveyls_survey_id' => $survey_id, 'surveyls_language' => 'en'));
		if (!isset($oSurveyLanguageSettings))
		{
			$survey_name = 'No ' . $oSurvey->language . ' Title';			
		}
		else
		{
			$survey_name = $oSurveyLanguageSettings->attributes['surveyls_title'];			
		}	
		$aData['survey_name'] = $survey_name;	
			
			
		

		$paticipants_updated = false;	
		//process user assigned changes
		if (isset($_POST['process_assign_participants'])) 
		{	
			//start operation
			$transaction=Yii::app()->db->beginTransaction();
			
			//look at each participant assigned/unassigned
			$edits_count = $_POST['assign_participant_count'];
			for ($edit_num = 0; $edit_num < $edits_count; $edit_num++) 
			{
				$participant_id = $_POST['participant_id_' . $edit_num];
				$participant_assigned = $_POST['participant_assigned_' . $edit_num];
				
				if ($participant_assigned == 'true')
				{
					$this->assign_to_survey($participant_id, $survey_id);
				}
				else
				{
					$this->unassign_to_survey($participant_id, $survey_id);
				}
			}
			
			//commit operation
			$transaction->commit();		
			$paticipants_updated = true;
		}
		$aData['paticipants_updated'] = $paticipants_updated;
		
		
		
		
		//process search made
		if (isset($_POST['process_search_users'])) 
		{		
			$search_term = $_POST['user_name_search'];	
			list($participant_ids, $participant_emails, $participant_assigneds, $participant_completeds) = $this->search_participants_in_survey($search_term, $survey_id);	
			
			$aData['participant_ids'] = $participant_ids;
			$aData['participant_emails'] = $participant_emails;
			$aData['participant_assigneds'] = $participant_assigneds;
			$aData['participant_completeds'] = $participant_completeds;
		}
		
		
			
		//set to only show INAR_MENU items
		Yii::app()->session['INAR_MENU_ONLY'] = 1;		
		       				
		
		$this->_renderWrappedTemplate('inarparticipant', 'admin_survey_assign', $aData);			        				
    }
	
}
