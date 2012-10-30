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
class Dashboard extends Survey_Common_Action
{

    public function run()
    {
        $clang = Yii::app()->lang;
						
						
		//get list of reports to show
		$command = Yii::app()->db->createCommand()
			->select("name")
			->from("inar_reports");  
		$results = $command->queryAll();		
		foreach($results as $result)
		{
			$aData['report_names'][] = $result['name'];
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
		
		//set to only show INAR_MENU items
		Yii::app()->session['INAR_MENU_ONLY'] = 1;		
		        				
		$this->getController()->_js_admin_includes(Yii::app()->getConfig('adminscripts') . 'dashboard.js');
		$this->_renderWrappedTemplate('dashboard', 'dashboard', $aData);
    }

	
	private function check_survey($oSurvey)
	{
		$survey_issues = '';
		if ($oSurvey->attributes['showwelcome'] != 'N')
		{
			$survey_issues .= 'Welcome screen is on.<br>';
		}			
		if ($oSurvey->attributes['alloweditaftercompletion'] != 'N')
		{
			$survey_issues .= 'Editing allowed after complettion.<br>';
		}			
		if ($oSurvey->attributes['publicstatistics'] != 'N')
		{
			$survey_issues .= 'Survey statistics are set to public!!<br>';
		}
		if ($oSurvey->attributes['publicgraphs'] != 'N')
		{
			$survey_issues .= 'Survey graphs are set to public!!<br>';
		}
		if ($oSurvey->attributes['listpublic'] != 'N')
		{
			$survey_issues .= 'Survey is publicly listed.<br>';
		}
		if ($oSurvey->attributes['datestamp'] != 'Y')
		{
			$survey_issues .= 'Survey results will not be datestamped.<br>';
		}
		if ($oSurvey->attributes['allowregister'] != 'N')
		{
			$survey_issues .= 'Allow register is set (Partiticpants register through the INAR website, not through lime survey).<br>';
		}			
		if ($oSurvey->attributes['tokenanswerspersistence'] != 'Y')
		{
			$survey_issues .= 'Answer persistance is off.<br>';
		}		
		if (!Yii::app()->db->schema->getTable('{{tokens_' . $oSurvey->primaryKey . '}}'))
		{
			$survey_issues .= 'No token table created.<br>';
		}
		
		$survey_issues = preg_replace('/\s*(?:<br\s*\/?>\s*)*$/i', '', $survey_issues);
		if ($survey_issues == '')
		{
			$survey_issues = 'None';
		}
		return $survey_issues;
	}
	
}
