<?php
/*** *** *** *** *** ***
* @package Quadodo Login Script
* @file    PostgreSQL.class.php
* @start   September 23rd, 2007
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
class PostgreSQL {
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
	function PostgreSQL($database_server_name, $database_username, $database_password, $database_port, $database_name, &$sql_class) {
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
	$sql0 = "CREATE SEQUENCE `qls3_test_id_seq` start 1 increment 1 maxvalue 2147483647 minvalue 1 cache 1";

	$sql1 = "CREATE TABLE `qls3_test`(
		`id` int4 DEFAULT nextval('qls3_test_id_seq'::text) NOT NULL,
		`test1` varchar(255) NOT NULL,
		`test2` int4 NOT NULL,
		`test3` int2 NOT NULL,
		CONSTRAINT `qls3_test_pkey` PRIMARY KEY(`id`)
	)";

	$sql2 = "CREATE INDEX `qls3_test_idx` ON `qls3_test` (`test2`)";
		if (!pg_query($this->connection, $sql0)) {
		$this->sql_class->create_failed();
		}

		if (!pg_query($this->connection, $sql1)) {
		$this->sql_class->create_failed();
		}

		if (!pg_query($this->connection, $sql2)) {
		$this->sql_class->create_failed();
		}
	}

	/**
	 * Attempts to insert into the test table
	 * @return void but will output error if found
	 */
	function test_insert() {
	$sql = "INSERT INTO `qls3_test` (`test1`,`test2`,`test3`) VALUES('test',12345,9)";
		if (!pg_query($this->connection, $sql)) {
		$this->sql_class->insert_failed();
		}
	}

	/**
	 * Tries to select from the newly created table
	 * @return void but will output error if found
	 */
	function test_select() {
	$sql = "SELECT `test2` FROM `qls3_test` WHERE `test3`=9";
		if (!$result = pg_query($this->connection, $sql)) {
		$this->sql_class->select_failed();
		}
		else {
		$row = pg_fetch_row($result);
			// Did it return 12345?
			if ($row[0] != '12345') {
			$this->sql_class->select_failed();
			}
			else {
			pg_free_result($result);
			}
		}
	}

	/**
	 * Attempts to update the row in the test table
	 * @return void but will output error if found
	 */
	function test_update() {
	$sql = "UPDATE `qls3_test` SET `test1`='testagain',`test2`=123 WHERE `test3`=9";
		if (!pg_query($this->connection, $sql)) {
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
		if (!pg_query($this->connection, $sql)) {
		$this->sql_class->alter_failed();
		}
		else {
			if (!pg_query($this->connection, $sql2)) {
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
		if (!pg_query($this->connection, $sql)) {
		$this->sql_class->delete_failed();
		}
	}

	/**
	 * Attempts to drop the test table
	 *	@return void but will output error if found
	 */
	function test_drop_table() {
	$sql = "DROP TABLE `qls3_test`";
		if (!pg_query($this->connection, $sql)) {
		$this->sql_class->drop_failed();
		}
	}

	/**
	 * Tries to connect to the database
	 * @return void but will output error if found
	 */
	function test_connection() {
	$connection_string = ($this->database_port !== false) ? "host={$this->database_server_name} port={$this->database_port} dbname={$this->database_name} user={$this->database_username} password={$this->database_password}" : "host={$this->database_server_name} dbname={$this->database_name} user={$this->database_username} password={$this->database_password}";

	// Test the connection
	$this->connection = @pg_connect($connection_string);
		if ($this->connection) {
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
	// Fetch the SQL file
	$sql = $this->read_sql_file('postgresql.sql');
		if ($sql === false) {
		$this->sql_class->open_sql_file_failed();
		}

	$sql = explode(';', $sql);
	$query_count = count($sql);
		// Loop through all the SQL and do it to it :)
		for ($x = 0; $x < $query_count; $x++) {
		$sql[$x] = ereg_replace('{database_prefix}', $database_prefix, $sql[$x]);
			if (!empty($sql[$x])) {
			pg_query($this->connection, $sql[$x]) or die(pg_last_error());
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
	$this->last_error = pg_last_error();
	$this->last_error_number = 'PostgreSQL Error';
	}

	/**
	 * Fetches an array from a result resource
	 * @param result $result - The result
	 * @return array on success, false on failure
	 */
	function fetch_array($result) {
	return pg_fetch_array($result);
	}

	/**
	 * Runs a query on the database (same as in PostgreSQL.class.php)
	 * @param string $query - A SQL query
	 * @return result identifier on success, false on error
	 */
	function query($query) {
		if ($query != '') {
		$query = preg_replace("/LIMIT ([0-9]+),([ 0-9]+)/", "LIMIT \\2 OFFSET \\1", $query);
			// Run and check if true or false
			if ($result = @pg_query($this->connection, $query)) {
			return $result;
			}
			else {
			$this->error();
			return false;
			}
		}
		else {
		// Find the error for no query
		$result = @pg_query($this->connection, '');
		$this->error();
		return false;
		}
	}
}
?>