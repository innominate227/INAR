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
class InarSurveys extends Survey_Common_Action
{

	
	/* make a survey be auto matically assigned to a new participant */
	private function set_survey_auto_assign($survey_id, $auto_assign)
	{	
		$auto_assign_char = 'N';
		if ($auto_assign) { $auto_assign_char = 'Y'; }
			
		$command = Yii::app()->db->createCommand()
			->select("auto_assign_new_participant")
			->from("inar_surveys")
			->where("id=:id");            
		$command->bindParam(":id", $survey_id, PDO::PARAM_INT);
		$results = $command->queryAll();	
		if (count($results) == 0)
		{
			$sql="INSERT INTO inar_surveys (id, auto_assign_new_participant) VALUES(:id, :auto_assign)";
		}
		else
		{
			$sql="UPDATE inar_surveys SET auto_assign_new_participant=:auto_assign WHERE id=:id";
		}
		//update the survey in database		
		$command=Yii::app()->db->createCommand($sql);	
		$command->bindParam(":auto_assign", $auto_assign_char, PDO::PARAM_STR);				
		$command->bindParam(":id", $survey_id, PDO::PARAM_INT);
		$command->execute();		
	}
	
		
	private function get_survey_auto_assign($survey_id)
	{			
		$command = Yii::app()->db->createCommand()
			->select("auto_assign_new_participant")
			->from("inar_surveys")
			->where("id=:id");            
		$command->bindParam(":id", $survey_id, PDO::PARAM_INT);
		$results = $command->queryAll();	
		if (count($results) == 0)
		{
			return 'N';
		}		
		return $results[0]['auto_assign_new_participant'];
	}
		
	private function get_survey_participants($survey_id)
	{		
		$command = Yii::app()->db->createCommand()
			->select("COUNT(*)")
			->from("inar_participant_surveys")
			->where("survey_id=:survey_id");            
		$command->bindParam(":survey_id", $survey_id, PDO::PARAM_INT);
		$results = $command->queryAll();		
		return $results[0]['COUNT(*)'];
	}

	private function get_survey_responses($survey_id)
	{		
		$table_name = 'lime_survey_' . $survey_id;
		if (Yii::app()->db->schema->getTable($table_name)) 
		{
			$command = Yii::app()->db->createCommand()
				->select("COUNT(*)")
				->from($table_name);            			
			$results = $command->queryAll();		
			return $results[0]['COUNT(*)'];
		}
		else
		{
			return 0;
		}
	}
	
	
	
    public function run()
    {
        $clang = Yii::app()->lang;
			
		$aData['auto_assign_updated'] = false;		
		//process changes made
		if (isset($_POST['process_update_auto_assign'])) 
		{
				
			//start atomic operation
			$transaction=Yii::app()->db->beginTransaction();
		
			//update each surveys auto assign setting
			$edits_count = $_POST['surveys_count'];
						
			for ($edit_num = 0; $edit_num < $edits_count; $edit_num++) 
			{
				$survey_id = $_POST['survey_id_' . $edit_num];
				$survey_auto_assign = ($_POST['survey_auto_' . $edit_num] == 'true');
				$this->set_survey_auto_assign($survey_id, $survey_auto_assign);
			}
			
			//commit atomic operation
			$transaction->commit();
			
			$aData['auto_assign_updated'] = false;
		}		
		

		
		
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
				$survey_info['name'] = 'No ' . $oSurvey->language . ' Title';
			}
			else
			{
				$survey_info['name'] = $oSurveyLanguageSettings->attributes['surveyls_title'];
			}					
					
			//get if the survey is auto assign
			$survey_info['auto_assign'] = ($this->get_survey_auto_assign($oSurvey->primaryKey) == 'Y');
							
			//get participant count
			$survey_info['participants'] = $this->get_survey_participants($oSurvey->primaryKey);			
			
			//get participant count
			$survey_info['responses'] = $this->get_survey_responses($oSurvey->primaryKey);
								
			//put survey info into data to pass to view
			$aData['surveys'][] = $survey_info;	
		}
		
		
		//set to only show INAR_MENU items
		Yii::app()->session['INAR_MENU_ONLY'] = 1;	
		
		$this->_renderWrappedTemplate('inarparticipant', 'admin_surveys', $aData);			        				
    }
	
}
