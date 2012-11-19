<?php
/*** *** *** *** *** ***
* @package Quadodo Login Script
* @file    update_3.1.5.php
* @start   December 10th, 2007
* @author  Douglas Rennehan
* @license http://www.opensource.org/licenses/gpl-license.php
* @version 1.0.4
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
define('IN_INSTALL', true);
define('QUADODO_IN_SYSTEM', true);

// Report all errors except E_NOTICE, because it screws things up...
error_reporting(E_ALL ^ E_NOTICE);

// Get the update class
require_once('Update.class.php');
$update = new Update('3.1.5');

if (isset($_POST['process'])) {
	// Update the system
	if (!$update->update_system()) {
	echo $update->update_error;
	}
	else {
	echo $update->update_success;
	}
}
else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

	<head>
		<title>Update from 3.1.5</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="author" content="Douglas Rennehan" />
		<meta name="robots" content="none" />
	</head>

	<body>
		<div align="left">
			Fill in the following fields. For <i>Online Users Format</i> {username} contains their username and {id} contains their user ID. Also put your current main admin account's password in the box:
		</div>
		<br />
		<form action="update_3.1.5.php" method="post">
			<input type="hidden" name="process" value="yes" />
			<table border="0">
				<tr>
					<td>
						Online Users Format:
					</td>
					<td>
						<input type="text" name="online_users_format" maxlength="255" value="{username}" />
					</td>
				</tr>
				<tr>
					<td>
						Online Users Separator:
					</td>
					<td>
						<input type="text" name="online_users_separator" maxlength="255" value="," />
					</td>
				</tr>
				<tr>
					<td>
						Password:
					</td>
					<td>
						<input type="text" name="password" maxlength="15" />
					</td>
				</tr>
			</table>
			<br />
			<input type="submit" value="Update" />
		</form>
	</body>

</html>
<?php
}
?>