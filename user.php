<?php

class user 
{

	var $_expire_time = 0;
	var $_current;
	var $_userlist;

	function user()
	{

		global $session;

		if ($session->valid('user_current')) {
			$this->_current = $session->r('user_current');
		} else {
			$this->_current = 0;
		}

		if ($session->valid('userlist')) {
			$this->_userlist = $session->r('userlist');
		} else {
			$this->_userlist = array();
		}

		if ($this->_expire_time !== 0) {
			if ($session->valid('expire')) {
				if ($session->r('expire') >= time()) {
					$this->_touch_expire();
				} else {
					$this->logout();
					return false;
				}
			}
		}

		$this->getdata($this->_current, true);
		$this->update();

	}

	

	function valid($ulevel = false, $strict = false)
	{

		global $userlevels;

		if ($ulevel !== false) {
			return $userlevels->valid($this->getuserlevel(), $ulevel, $strict);
		}

		return (bool) $this->_current;

	}

	

	function getuserlevel()
	{

		if (isset($this->_userlist[$this->_current])) {
			return $this->_userlist[$this->_current]['ulevel'];
		}

	}

	

	function _touch_expire()
	{

		global $session;
		return $session->w('expire', time() + $this->_expire_time);

	}

	

	function update()
	{

		global $session;

		$session->w('userlist', $this->_userlist);
		$session->w('user_current', $this->_current);

	}

	

	function getdata($uid = false, $nocache = false)
	{

		global $db, $dbprefix;

		if ($uid !== false) {
			$uid = $db->escape_string($uid);
			if (empty($this->_userlist[$uid]) || $nocache === true) {
				if ($row = $db->fetch_assoc($db->query('select * from ' . $dbprefix . 'users where id="' . $uid . '"'))) {
					$this->_userlist[$uid] = $row;
				}
			}

			if (isset($this->_userlist[$uid])) {
				return (array) $this->_userlist[$uid];
			}
		}

		if (isset($this->_userlist[$this->_current])) {
			return (array) $this->_userlist[$this->_current];
		}

	}



	function exists($user)
	{

		global $db, $dbprefix;

		return (bool) $db->num_rows($db->query('select null from ' . $dbprefix . 'users where uname="' . $db->escape_string($user) . '"'));

	}



	function login($user, $passwd)
	{

		global $db, $dbprefix, $session;

		if ((string) $user !== '' && (string) $passwd !== '') {
			if ($row = $db->fetch_assoc($db->query('select id from ' . $dbprefix . 'users where uname="' . $db->escape_string($user) . '" and passwd="' . $db->escape_string($passwd) . '"'))) {
				$session->destroy();
				$session->generate_new_id();
				session_start();
				$this->_current = $row['id'];
				$this->_touch_expire();
				$this->update();
				$this->getdata($this->_current, true);
				return true;
			}
			$this->_current = 0;
		}

	}

	

	function logout()
	{

		global $session;

		unset($this->_current);
		unset($this->_userlist);
		return $session->destroy();

	}

}

?>
