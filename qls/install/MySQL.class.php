<?php
/*** *** *** *** *** ***
* @package Quadodo Login Script
* @file    MySQL.class.php
* @start   August 1st, 2007
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
if (!defined('IN_INSTALL')) {
exit;
}

/**
 * Contains all functions needed to test the MySQL information
 * the user entered in the installation process.
 */
class MySQL {
/**
 * @var object $sql_class - Contains error functions
 */
var $sql_class;

	/**
	 * Constructs the class
	 * @param string  $database_server_name
	 * @param string  $database_username
	 * @param string  $database_password
	 * @param integer $database_port
	 * @param string  $database_name
	 * @param object  $sql_class
	 */
	function MySQL($database_server_name, $database_username, $database_password, $database_port, $database_name, &$sql_class) {
	$this->database_server_name = $database_server_name;
	$this->database_username = $database_username;
	$this->database_password = $database_password;
	$this->database_name = $database_name;
	$this->database_port = $database_port;
	$this->sql_class = &$sql_class;
	}

	/**
	 * Attempts to create a table
	 *	@return void but will output error if found
	 */
	function test_create_table() {
	$sql = "CREATE TABLE `qls3_test`(
		`id` INT(11) NOT NULL AUTO_INCREMENT,
		`test1` VARCHAR(255) NOT NULL,
		`test2` INT(11) NOT NULL,
		`test3` TINYINT(1) NOT NULL,
		PRIMARY KEY(`id`),
		INDEX(`test1`)
	)";
		if (!mysql_query($sql, $this->connection)) {
		$this->sql_class->create_failed();
		}
	}

	/**
	 * Attempts to insert into the test table
	 * @return void but will output error if found
	 */
	function test_insert() {
	$sql = "INSERT INTO `qls3_test` (`test1`,`test2`,`test3`) VALUES('test',12345,9)";
		if (!mysql_query($sql, $this->connection)) {
		$this->sql_class->insert_failed();
		}
	}

	/**
	 * Tries to select from the newly created table
	 * @return void but will output error if found
	 */
	function test_select() {
	$sql = "SELECT `test2` FROM `qls3_test` WHERE `test3`=9";
		if (!$result = mysql_query($sql)) {
		$this->sql_class->select_failed();
		}
		else {
		$row = mysql_fetch_row($result);
			// Did it return 12345?
			if ($row[0] != '12345') {
			$this->sql_class->select_failed();
			}
			else {
			mysql_free_result($result);
			}
		}
	}

	/**
	 * Attempts to update the row in the test table
	 * @return void but will output error if found
	 */
	function test_update() {
	$sql = "UPDATE `qls3_test` SET `test1`='testagain',`test2`=123 WHERE `test3`=9";
		if (!mysql_query($sql, $this->connection)) {
		$this->sql_class->update_failed();
		}
	}

	/**
	 *	Attempts to alter the test table
	 * @return void but will output error if found
	 */
	function test_alter() {
	$sql = "ALTER TABLE `qls3_test` ADD `test4` VARCHAR(20)";
	$sql2 = "ALTER TABLE `qls3_test` DROP COLUMN `test4`";
		if (!mysql_query($sql, $this->connection)) {
		$this->sql_class->alter_failed();
		}
		else {
			if (!mysql_query($sql2, $this->connection)) {
			$this->sql_class->alter_failed();
			}
		}
	}

	/**
	 * Attempts to delete from the table
	 * @return void but will output error if found
	 */
	function test_delete() {
	$sql = "DELETE FROM `qls3_test` WHERE `test3`=9";
		if (!mysql_query($sql, $this->connection)) {
		$this->sql_class->delete_failed();
		}
	}

	/**
	 * Attempts to drop the test table
	 *	@return void but will output error if found
	 */
	function test_drop_table() {
	$sql = "DROP TABLE `qls3_test`";
		if (!mysql_query($sql, $this->connection)) {
		$this->sql_class->drop_failed();
		}
	}

	/**
	 * Tries to connect to the database
	 * @return void but will output error if found
	 */
	function test_connection() {
	$real_server = ($this->database_port !== false) ? $this->database_server_name . ':' . $this->database_port : $this->database_server_name;

	// Test the connection
	$this->connection = @mysql_connect($real_server,
		$this->database_username,
		$this->database_password
	);

		if ($this->connection) {
		// Test the database
		$this->database = @mysql_select_db($this->database_name);
			if ($this->database) {
			// Call the functions to test functionality
			$this->test_create_table();
			$this->test_insert();
			$this->test_select();
			$this->test_update();
			$this->test_alter();
			$this->test_delete();
			$this->test_drop_table();
			}
			else {
			$this->sql_class->database_selection_failed();
			}
		}
		else {
		$this->sql_class->connection_failed();
		}
	}

	/**
	 * Parses a SQL file
	 * @param string $file_name - The file name
	 * @return void
	 */
	function parse_sql_file($file_name) {
	$sql = $this->read_sql_file($file_name);
	$sql = explode(';', $sql);
	$query_count = count($sql);
		for ($x = 0; $x < $query_count; $x++) {
		$sql[$x] = str_replace('{database_prefix}', $this->database_prefix, $sql[$x]);
			if (!empty($sql[$x])) {
			$this->query($sql[$x]);
			}
		}
	}

	/**
	 * Reads a SQL file
	 * @param string $file_name - The file name
	 * @return string of file contents
	 */
	function read_sql_file($file_name) {
	$file_name = dirname(__FILE__) . '/schemas/' . $file_name;
		if (file_exists($file_name) && is_readable($file_name)) {
			if ($file_handle = fopen($file_name, 'r')) {
			$file_data = fread($file_handle, filesize($file_name));
			fclose($file_handle);
			return $file_data;
			}
			else {
			return false;
			}
		}
		else {
		return false;
		}
	}

	/**
	 * Creates the necessary tables for the system
	 * @param string $database_prefix - The prefix entered by the user
	 * @return void but will output error if found
	 */
	function create_system_tables($database_prefix) {
	// Fetch the SQL data
	$sql = $this->read_sql_file('mysql.sql');
		if ($sql === false) {
		$this->sql_class->open_sql_file_failed();
		}

	$sql = explode(';', $sql);
	$query_count = count($sql);
		// Loop through all the SQL and do it to it :)
		for ($x = 0; $x < $query_count; $x++) {
		$sql[$x] = ereg_replace('{database_prefix}', $database_prefix, $sql[$x]);
			if (!empty($sql[$x])) {
			mysql_query($sql[$x], $this->connection) or die(mysql_error());
			}
		}
	}

	/**
	 * Kills the script and outputs the error
	 * @return void and kills the script with the defined error message
	 */
	function output_error() {
	die($this->last_error_number . ': ' . $this->last_error);
	}

	/**
	 * Adds to the error information
	 * @return void
	 */
	function error() {
	$this->last_error = mysql_error();
	$this->last_error_number = mysql_errno();
	}

	/**
	 * Fetches an array from a result
	 * @param result $result - The result
	 * @return array on success, false on false
	 */
	function fetch_array($result) {
	return mysql_fetch_array($result);
	}

	/**
	 * Runs a query on the database (same as in MySQL.class.php)
	 * @param string $query - A SQL query
	 * @return result identifier on success, false on error
	 */
	function query($query) {
		if ($query != '') {
			// Run and check if true or false
			if ($result = mysql_query($query, $this->connection)) {
			return $result;
			}
			else {
			$this->error();
			return false;
			}
		}
		else {
		// Find the error for no query
		$result = @mysql_query('');
		$this->error();
		return false;
		}
	}
}
?>