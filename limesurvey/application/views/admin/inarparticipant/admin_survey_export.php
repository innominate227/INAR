<?php
/*** *** *** *** *** ***
* @package Quadodo Login Script
* @file    security_image.php
* @start   July 25th, 2007
* @author  Douglas Rennehan
* @license http://www.opensource.org/licenses/gpl-license.php
* @version 1.1.0
* @link    http://www.quadodo.com
*** *** *** *** *** ***
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*** *** *** *** *** ***
* Comments are always before the code they are commenting.
*** *** *** *** *** ***/
define('QUADODO_IN_SYSTEM', true);
require_once('includes/header.php');
$qls->Security->check_auth_page('admin_survey_export.php'); 

//get info for the survey with the passed id
$survey_id = $_GET['sid'];
list ($survey_id, $survey_name, $survey_auto_assign, $survey_participant_count, $survey_response_count) = $qls->Surveys->get_survey_info($survey_id);	

//get the exported survey data
$survey_data_base64 = $qls->Surveys->export_survey($survey_id);
$survey_data = base64_decode($survey_data_base64);

//the current date
$date = date('m/d/Y', time());

//set header info
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Cache-Control: pre-check=0, post-check=0', false);
header('Pragma: no-cache');
header('Expires: Thursday, 21 June 2007 05:00:00 GMT');
header('Content-Type: text/csv', true);
header('Content-Disposition: attachment; filename="' . $survey_name . ' ' . $date . '.csv"');

//echo the survey data
echo $survey_data

?>