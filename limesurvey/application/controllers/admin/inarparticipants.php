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
class InarParticipants extends Survey_Common_Action
{

		
	/* search participant (currently just on email) */
	private function search_participants_date($email,$date1,$date2)
	{
		$participant_ids = array();
		$participant_emails = array();

		$command = Yii::app()->db->createCommand()
			->select("id, email")
			->from("inar_participants")
			->where("email LIKE :email AND date BETWEEN :date1 AND :date2");            
		$command->bindParam(":email", $email, PDO::PARAM_STR);
		$command->bindParam(":date1", $date1, PDO::PARAM_STR);
		$command->bindParam(":date2", $date2, PDO::PARAM_STR);
		$results = $command->queryAll();		

		foreach ($results as $participant_row) 
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
	
	
	private function search_participants_month($month)
	{			
		$participant_ids = array();
		$participant_emails = array();
		
		$date1 = '2012-' . $month . '-01';
		$date2 = '2012-' . $month . '-31';
		
		$command = Yii::app()->db->createCommand()
			->select("id, email")
			->from("inar_participants")
			->where("date BETWEEN :date1 AND :date2");            		
		$command->bindParam(":date1", $date1, PDO::PARAM_STR);
		$command->bindParam(":date2", $date2, PDO::PARAM_STR);
		$results = $command->queryAll();					
		
		foreach ($results as $participant_row) 
		{
			$participant_ids[] = $participant_row['id'];
			$participant_emails[] = $participant_row['email'];			
		}
		return array($participant_ids, $participant_emails);
	}


	/* search participant, also return what surveys they have been assigned to */
	private function search_participants($email,$date1,$date2)
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
						
			$command = Yii::app()->db->createCommand()
				->select("inar_surveys.name")
				->from("inar_participant_surveys, inar_surveys")
				->where("inar_participant_surveys.participant_id=:participant_id AND inar_participant_surveys.survey_id=inar_surveys.id");            		
			$command->bindParam(":participant_id", $participant_id, PDO::PARAM_INT);			
			$results = $command->queryAll();
			
			foreach ($results as $participants_surveys_row) 
			{
				$participant_surveys[] = $participants_surveys_row['name'];						
			}			
			$participant_surveyss[] = $participant_surveys;			
		}

		return array($participant_ids, $participant_emails, $participant_surveyss);
	}	
		


    public function run()
    {
        $clang = Yii::app()->lang;

		
		//process search made
		if (isset($_POST['process_search_users'])) 
		{		
			$search_term = $_POST['user_name_search'];
			$term2 = $_POST['from_date'];	
			$term3 = $_POST['to_date'];
			list($participant_ids, $participant_emails, $participant_surveyss) = $this->search_participants($search_term, $term2,$term3);	
			
			$aData['participant_ids'] = $participant_ids;
			$aData['participant_emails'] = $participant_emails;
			$aData['participant_surveyss'] = $participant_surveyss;
		}
		
		if (isset($_POST['process_reports'])) 
		{	
		  $s_term = $_POST['month'];
		  $mons = array(1 => "January", 2 => "February", 3 => "March", 4 => "April", 5 => "May", 6 => "June", 7 => "July", 8 => "August", 9 => "September", 10 => "October", 11 => "November", 12 => "December");
		  $month_name = $mons[$s_term];
		  list($dates_reg,$emails) = $this->search_participants_month($s_term);
				  
			//$aData['dates_reg'] = $dates_reg;
			//$aData['emails'] = $emails;		
			$aData['dates_reg'] = array();
			$aData['emails'] = array();		
			$aData['month_name'] = $month_name;
		}
			
			
		//set to only show INAR_MENU items
		Yii::app()->session['INAR_MENU_ONLY'] = 1;
				
		$this->_renderWrappedTemplate('inarparticipant', 'participants', $aData);			        				
    }
	
}
