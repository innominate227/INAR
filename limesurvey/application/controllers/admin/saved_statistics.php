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

/**
 * Statistics Controller
 *
 * This controller performs statistics actions
 *
 * @package		LimeSurvey
 * @subpackage	Backend
 */
class savedstatistics extends statistics {

    function __construct($controller, $id)
    {
        parent::__construct($controller, $id);
    }

	/**
	 * Constructor
	 */
	public function run($surveyid = 0, $subaction = null)
	{
	/*
		//get some of the POST data we care about
		$report_name = $_POST['report_name'];
		$datestampG = $_POST['datestampG'];
		$datestampL = $_POST['datestampL'];
		
		echo $report_name;
		
		//set post to what was posted in order to generate the report		
		$command = Yii::app()->db->createCommand()
            ->select("post_data")
            ->from("inar_reports")
            ->where("name='{$report_name}'");            
        $results = $command->queryAll();
		$post_data = ($results[0][''post_data]
		$_POST = json_decode($post_data);
		
		//set back the datestamps to what was selected in the dashboard
		$_POST['datestampG'] = $datestampG;
		$_POST['datestampL'] = $datestampL;
		$_POST['doSaveReport'] = ''; //make sure statistics doesnt try and resave the report
		
		$surveyid = 753971;
		*/
				
		//let the statistic class do its thing
		parent::run($surveyid, $subaction);
	}

}
