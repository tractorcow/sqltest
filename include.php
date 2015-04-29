<?php

require '../_ss_environment.php';

global $dbConn;
$dbConn = new MySQLi(
	SS_DATABASE_SERVER,
	SS_DATABASE_USERNAME,
	SS_DATABASE_PASSWORD,
	'SS_ss32test'
);
$dbConn->set_charset('utf8');
$dbConn->query("SET sql_mode = 'ANSI'");

function parse_prepared_params($parameters, &$blobs) {
	$types = '';
	$values = array();
	$blobs = array();
	for($index = 0; $index < count($parameters); $index++) {
		$value = $parameters[$index];
		$phpType = gettype($value);

		// Allow overriding of parameter type using an associative array
		if($phpType === 'array') {
			$phpType = $value['type'];
			$value = $value['value'];
		}

		// Convert php variable type to one that makes mysqli_stmt_bind_param happy
		// @see http://www.php.net/manual/en/mysqli-stmt.bind-param.php
		switch($phpType) {
			case 'boolean':
			case 'integer':
				$types .= 'i';
				break;
			case 'float': // Not actually returnable from gettype
			case 'double':
				$types .= 'd';
				break;
			case 'object': // Allowed if the object or resource has a __toString method
			case 'resource':
			case 'string':
			case 'NULL': // Take care that a where clause should use "where XX is null" not "where XX = null"
				$types .= 's';
				break;
			case 'blob':
				$types .= 'b';
				// Blobs must be sent via send_long_data and set to null here
				$blobs[] = array(
					'index' => $index,
					'value' => $value
				);
				$value = null;
				break;
			case 'array':
			case 'unknown type':
			default:
				user_error("Cannot bind parameter \"$value\" as it is an unsupported type ($phpType)",
					E_USER_ERROR);
				break;
		}
		$values[] = $value;
	}
	return array_merge(array($types), $values);
}

function prepare_statement($sql, $params) {
	global $dbConn;
	$statement = $dbConn->stmt_init();
	if($statement->error) die($statement->error);
	if($dbConn->error) die($dbConn->error);
	$statement->prepare($sql);
	if($statement->error) die($statement->error);

	// paramas
	$params = parse_prepared_params($params, $blobs);
	for ($i = 0; $i < count($params); $i++)
	{
		$boundName = "param$i";
		$$boundName = $params[$i];
		$boundNames[] = &$$boundName;
	}
	call_user_func_array( array($statement, 'bind_param'), $boundNames);

	return $statement;
}