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

    public function run()
    {
        $clang = Yii::app()->lang;
			
		//set to only show INAR_MENU items
		Yii::app()->session['INAR_MENU_ONLY'] = 1;		
		       				
		
		$this->_renderWrappedTemplate('inarparticipant', 'admin_surveys', $aData);			        				
    }
	
}
