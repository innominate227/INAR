<?php
/*** *** *** *** *** ***
* @package Quadodo Login Script
* @file    update_3.1.8.php
* @start   March 9th, 2008
* @author  Douglas Rennehan
* @license http://www.opensource.org/licenses/gpl-license.php
* @version 1.0.0
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
$update = new Update('3.1.8');

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
		<title>Update from 3.1.8</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<meta name="author" content="Douglas Rennehan" />
		<meta name="robots" content="none" />
	</head>

	<body>
		<div align="left">
			Put your current main admin account's password in the box, and thne click Update and it will be run :)
		</div>
		<br />
		<form action="update_3.1.8.php" method="post">
			<input type="hidden" name="process" value="yes" />
			<table border="0">
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