<?php
/**
* MY_Input
*
* Allows CodeIgniter to be passed Query Strings
* NOTE! You must add the question mark '?' to the allowed URI chars,
* and set the URI protocol to PATH_INFO
*
* @package Flame
* @subpackage Hacks
* @copyright 2009, Jamie Rumbelow
* @author Jamie Rumbelow <http://www.jamierumbelow.net>
* @license GPLv3
* @version 1.0.0
*/

class BLX_Input extends CI_Input {
	
	function _sanitize_globals()
	{
		$this->allow_get_array = TRUE;
		
		// Would kind of be "wrong" to unset any of these GLOBALS
		$protected = array('_SERVER', '_GET', '_POST', '_FILES', '_REQUEST', '_SESSION', '_ENV', 'GLOBALS', 'HTTP_RAW_POST_DATA',
							'system_folder', 'application_folder', 'BM', 'EXT', 'CFG', 'URI', 'RTR', 'OUT', 'IN');

		// Unset globals for security. 
		// This is effectively the same as register_globals = off
		foreach (array($_GET, $_POST, $_COOKIE, $_SERVER, $_FILES, $_ENV, (isset($_SESSION) && is_array($_SESSION)) ? $_SESSION : array()) as $global)
		{
			if ( ! is_array($global))
			{
				if ( ! in_array($global, $protected))
				{
					unset($GLOBALS[$global]);
				}
			}
			else
			{
				foreach ($global as $key => $val)
				{
					if ( ! in_array($key, $protected))
					{
						unset($GLOBALS[$key]);
					}

					if (is_array($val))
					{
						foreach($val as $k => $v)
						{
							if ( ! in_array($k, $protected))
							{
								unset($GLOBALS[$k]);
							}
						}
					}
				}
			}
		}

		// Is $_GET data allowed? If not we'll set the $_GET to an empty array
		
		# need to refactor below lines. IT MUST BE CLEANED $_GET.
		
		if ($this->allow_get_array == FALSE)
		{
			$_GET = array();
		}
		else
		{
			$_GET = $this->_clean_input_data($_GET);
		}

		// Clean $_POST Data
		$_POST = $this->_clean_input_data($_POST);

		// Clean $_COOKIE Data
		// Also get rid of specially treated cookies that might be set by a server
		// or silly application, that are of no use to a CI application anyway
		// but that when present will trip our 'Disallowed Key Characters' alarm
		// http://www.ietf.org/rfc/rfc2109.txt
		// note that the key names below are single quoted strings, and are not PHP variables
		unset($_COOKIE['$Version']);
		unset($_COOKIE['$Path']);
		unset($_COOKIE['$Domain']);
		$_COOKIE = $this->_clean_input_data($_COOKIE);

		log_message('debug', "Global POST and COOKIE data sanitized");
	}
	
	/*function _clean_input_data($str)//さくら／Multiviews対策
	{
		
		if (is_array($str))
		{
			$new_array = array();
			foreach ($str as $key => $val)
			{
				$new_array[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
			}
			return $new_array;
		}
		
		// We strip slashes if magic quotes is on to keep things consistent
		if (get_magic_quotes_gpc())
		{
			$str = stripslashes($str);
		}

		// Should we filter the input data?
		if ($this->use_xss_clean === TRUE)
		{
			$str = $this->xss_clean($str);
		}

		// Standardize newlines
		if (strpos($str, "\r") !== FALSE)
		{
			$str = str_replace(array("\r\n", "\r"), "\n", $str);
		}
		
		return $str;
	}*/
	
	function _clean_input_keys($str)//さくら／Multiviews対策
	{
		#$str = str_replace('_php', '', $str);
		#print $str.'<hr />';
		/*if ( ! preg_match("/^[a-z0-9:_\/-]+$/i", $str))
		{
			exit('Disallowed Key Characters!');
		}*/

		return $str;
	}
	
	/*function _sanitize_globals() {
		$this->allow_get_array = TRUE;
		parent::_sanitize_globals();
	}*/
}