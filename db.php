<?php

class db 
{
	var $_link_identifier;
	var $_queries;
	var $sql;
	var $dbname;
	
	function catch_error($handle, $msg = null)
	{
		if ($handle === false) {
			trigger_error($msg . '<br />' . mysql_error(), E_USER_WARNING);
		}
	}
	
	function connect($host, $user, $pass, $dbname = null)
	{
		$this->_queries = 0;
		$this->_link_identifier = mysql_pconnect($host, $user, $pass);
		$this->catch_error($this->_link_identifier, 'connect');
		if ($dbname !== null) {
			$this->select_db($dbname);
		}
	}
	
	function select_db($dbname)
	{
		$this->catch_error(mysql_select_db($dbname, $this->_link_identifier), 'select_db');
		$this->dbname = $dbname;
	}
	
	function insert_id()
	{
		return mysql_insert_id($this->_link_identifier);
	}
	
	function free_result($res)
	{
		$this->catch_error(mysql_free_result($res), 'free_result');
	}
	
	function query($str)
	{
		$res = mysql_query($str, $this->_link_identifier);
		$this->_queries++;
		$this->catch_error($res, 'query: ' . $str);
		$this->sql .= $str . "\n";
		return $res;
	}

	function queries()
	{
		return $this->_queries;
	}

	function affected_rows()
	{
		return mysql_affected_rows($this->_link_identifier);
	}

	function num_rows($res)
	{
		return mysql_num_rows($res);
	}

	function fetch_array($res)
	{
		return mysql_fetch_array($res);
	}

	function fetch_row($res)
	{
		return mysql_fetch_row($res);
	}

	function fetch_assoc($res)
	{
		return mysql_fetch_assoc($res);
	}

	function fetch_object($res)
	{
		return mysql_fetch_object($res);
	}

	function escape_string($str)
	{
		return mysql_escape_string($str);
	}
}

?>
