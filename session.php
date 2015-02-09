<?php

class session 
{
	
	function session()
	{
		session_start();
	}
	
	function r($var)
	{
		if (isset($_SESSION[$var])) {
			return $_SESSION[$var];
		}
	}
	
	function w($var, $str)
	{
		$_SESSION[$var] = $str;
		return true;
	}
	
	function destroy()
	{
		if ($_SESSION !== array()) {
			$_SESSION = array();
			return session_destroy();
		}
	}
	
	function id()
	{
		return session_id();
	}

	function generate_new_id()
	{
		return session_id(md5(uniqid(rand(),1)));
	}
	
	function valid($var = null)
	{
		if ($var === null) {
   			return $_SESSION !== array();
		}
		return isset($_SESSION[$var]) && $_SESSION[$var] !== null;
	}
	
}

?>
