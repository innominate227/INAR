<?php
/*** *** *** *** *** ***
* @package Quadodo Login Script
* @file    Update.class.php
* @start   October 29th, 2007
* @author  Douglas Rennehan
* @license http://www.opensource.org/licenses/gpl-license.php
* @version 1.1.6
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
if (!defined('IN_INSTALL')) {
exit;
}

class Update {

/**
 * @var string $new_version - The new version we are updating to
 */
var $new_version = '3.1.9';

/**
 * @var string $update_success - The success message when done updating
 */
var $update_success = 'You have successfully updated your version of the Quadodo Login Script! You may now remove the entire <b>/install/</b> directory from your site. Remember that support is available in the <a href="http://www.quadodo.com/forum/index.php" target="_blank">Support Area</a>.';

	/**
	 * Constructs the class
	 * @return void
	 */
	function Update($old_version) {
	$this->old_version = $old_version;

	// Get the directory locations
	$this->install_directory = dirname(__FILE__);
	$this->main_directory = str_replace('/install/updates', '', $this->install_directory);

	// We need the database information
	require_once($this->main_directory . '/includes/database_info.php');
	$this->database_prefix = $database_prefix;
	$this->database_type = $database_type;
	$this->database_server_name = $database_server_name;
	$this->database_username = $database_username;
	$this->database_password = $database_password;
	$this->database_name = $database_name;
	$this->database_port = $database_port;
	$this->database_persistent = $database_persistent;
	}

	/**
	 * Generates the password hash, works differently than the original function.
	 * This will actually read the user information to obtain the code.
	 * @param string $password - Their password
	 */
	function generate_password_hash($password) {
	$result = $this->test->query("SELECT * FROM `{$this->database_prefix}users` WHERE `id`=1");
	$row = $this->test->fetch_array($result);
	$user_code = $row['code'];
	// Password generation
	$hash[] = md5($password);
	$hash[] = md5($password . $user_code);
	$hash[] = md5($password) . sha1($user_code . $password) . md5(md5($password));
	$hash[] = sha1($password . $user_code . $password);
	$hash[] = md5($hash[3] . $hash[0] . $hash[1] . $hash[2] . sha1($hash[3] . $hash[2]));
	$hash[] = sha1($hash[0] . $hash[1] . $hash[2] . $hash[3]) . md5($hash[4] . $hash[4]) . sha1($user_code);
	$final_hash = sha1($hash[0] . $hash[1] . $hash[2] . $hash[3] . $hash[4] . $hash[5] . md5($user_code));
	return $final_hash;
	}

	/**
	 * Make the input safe, same as in Security.class.php
	 * @param string  $input - The input text
	 * @param boolean $html  - Whether to use htmlentities() or not
	 * @return clean string
	 */
	function make_safe($input, $html = true) {
		/**
		 * Loops through to a certain depth and uses the addslashes()
		 * or htmlentities() functions to make it safe.
		 */
		if (is_array($input)) {
			foreach ($input as $key => $value) {
				if (is_array($value)) {
					foreach ($value as $key2 => $value2) {
						if (is_array($value2)) {
							foreach ($value2 as $key3 => $value3) {
								if (is_array($value3)) {
									foreach ($value3 as $key4 => $value4) {
										// This is as far as we go
										if (is_array($value4)) {
										$input[$key][$key2][$key3][$key4] = $value4;
										}
										else {
											if ($html === false) {
											$input[$key][$key2][$key3][$key4] = addslashes($value4);
											}
											else {
											$input[$key][$key2][$key3][$key4] = htmlentities($value4, ENT_QUOTES);
											}
										}
									}
								}
								else {
									if ($html === false) {
									$input[$key][$key2][$key3] = addslashes($value3);
									}
									else {
									$input[$key][$key2][$key3] = htmlentities($value3, ENT_QUOTES);
									}
								}
							}
						}
						else {
							if ($html === false) {
							$input[$key][$key2] = addslashes($value2);
							}
							else {
							$input[$key][$key2] = htmlentities($value2, ENT_QUOTES);
							}
						}
					}
				}
				else {
					if ($html === false) {
					$input[$key] = addslashes($value);
					}
					else {
					$input[$key] = htmlentities($value, ENT_QUOTES);
					}
				}
			}

		return $input;
		}
		else {
			if ($html === false) {
			return addslashes($input);
			}
			else {
			return htmlentities($input, ENT_QUOTES);
			}
		}
	}

	/**
	 * This will update the database
	 * @return true on success, false on failure
	 */
	function update_system() {
	// Get the Test class
	require_once($this->main_directory . '/install/Test.class.php');
	$this->test = new Test($this->database_server_name,
		$this->database_username,
		$this->database_password,
		$this->database_name,
		$this->database_port,
		$this->database_type
	);
	$this->test->test_connection();
	$suffix = ($this->database_type == 'MySQL' || $this->database_type == 'MySQLi') ? 'mysql' : 'postgresql';

		switch ($this->old_version) {
			default:
			case '3.1.5':
			$online_users_format = (isset($_POST['online_users_format']) && strlen($_POST['online_users_format']) <= 255) ? $this->make_safe($_POST['online_users_format'], false) : '{username}';
			$online_users_separator = (isset($_POST['online_users_separator']) && strlen($_POST['online_users_separator']) <= 255) ? $this->make_safe($_POST['online_users_separator'], false) : ',';
			$password = (isset($_POST['password']) && strlen($_POST['password']) <= 15 && strlen($_POST['password']) >= 4) ? $this->make_safe($_POST['password']) : false;
				if ($password === false) {
				$this->update_error = 'Please use a valid password.<br /><br /><a href="#" onclick="javascript:history.go(1);">Go Back</a>';
				return false;
				}

			$password = $this->generate_password_hash($password);

			// Parse the SQL
			$this->test->current_layer->database_prefix = $this->database_prefix;
			$this->test->parse_sql_file('u3.1.5_' . $suffix . '.sql');

				if (!$this->test->query("INSERT INTO `{$this->database_prefix}config` (`name`,`value`) VALUES('online_users_format','{$online_users_format}')")) {
				$this->test->output_error();
				}

				if (!$this->test->query("INSERT INTO `{$this->database_prefix}config` (`name`,`value`) VALUES('online_users_separator','{$online_users_separator}')")) {
				$this->test->output_error();
				}

				if (!$this->test->query("UPDATE `{$this->database_prefix}users` SET `password`='{$password}' WHERE `id`=1")) {
				$this->test->output_error();
				}

				if (!$this->test->query("UPDATE `{$this->database_prefix}config` SET `value`='{$this->new_version}' WHERE `name`='current_version'")) {
				$this->test->output_error();
				}
			break;
			case '3.1.6':
			$password = (isset($_POST['password']) && strlen($_POST['password']) <= 15 && strlen($_POST['password']) >= 4) ? $this->make_safe($_POST['password']) : false;
				if ($password === false) {
				$this->update_error = 'Please use a valid password.<br /><br /><a href="#" onclick="javascript:history.go(1);">Go Back</a>';
				return false;
				}

			$password = $this->generate_password_hash($password);

				if (!$this->test->query("UPDATE `{$this->database_prefix}users` SET `password`='{$password}' WHERE `id`=1")) {
				$this->test->output_error();
				}

				if (!$this->test->query("UPDATE `{$this->database_prefix}config` SET `value`='{$this->new_version}' WHERE `name`='current_version'")) {
				$this->test->output_error();
				}
			break;
			case '3.1.7':
			$password = (isset($_POST['password']) && strlen($_POST['password']) <= 15 && strlen($_POST['password']) >= 4) ? $this->make_safe($_POST['password']) : false;
				if ($password === false) {
				$this->update_error = 'Please use a valid password.<br /><br /><a href="#" onclick="javascript:history.go(1);">Go Back</a>';
				return false;
				}

			$password = $this->generate_password_hash($password);

				if (!$this->test->query("UPDATE `{$this->database_prefix}users` SET `password`='{$password}' WHERE `id`=1")) {
				$this->test->output_error();
				}

				if (!$this->test->query("UPDATE `{$this->database_prefix}config` SET `value`='{$this->new_version}' WHERE `name`='current_version'")) {
				$this->test->output_error();
				}
			break;
			case '3.1.8':
			$password = (isset($_POST['password']) && strlen($_POST['password']) <= 15 && strlen($_POST['password']) >= 4) ? $this->make_safe($_POST['password']) : false;
				if ($password === false) {
				$this->update_error = 'Please use a valid password.<br /><br /><a href="#" onclick="javascript:history.go(1);">Go Back</a>';
				return false;
				}

			$password = $this->generate_password_hash($password);

				if (!$this->test->query("UPDATE `{$this->database_prefix}users` SET `password`='{$password}' WHERE `id`=1")) {
				$this->test->output_error();
				}

				if (!$this->test->query("UPDATE `{$this->database_prefix}config` SET `value`='{$this->new_version}' WHERE `name`='current_version'")) {
				$this->test->output_error();
				}
			break;
		}

	return true;
	}
}
?>