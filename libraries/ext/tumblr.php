<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_tumblr {
	
	var $user;
	var $api_path;
	var $param = array(
		
	);
	
	function read($param = array()) {
		return $this->get('read', $param);
	}
	
	function get($method = 'read', $param) {
		$CI =& get_instance();
		$CI->load->library('xml');
		
		$api_url = $this->_get_path($method, $param);
		
		$xml = $CI->output->get_cache($api_url);
		if (!$xml) {
			$xml = file_get_contents($api_url);
			$CI->output->set_cache($api_url, $xml, 600);
		}
		
		//XMLパース
		$CI->xml->parse($xml);
		return $CI->xml->dat;
	}
	
	function _get_path($method, $param = array(), $json = false) {
		$api_path = $this->api_path.$method;
		if ($json === true) $api_path.'/json';
		
		if (!empty($param) && is_array($param)) {
			$q = '?';
			foreach($param as $k => $v) {
				$q .= $k.'='.$v.'&';
			}
			$api_path .= substr($q, 0, -1);
		}
		
		return $api_path;
	}
	
	function init($user) {
		$this->user = $user;
		$this->api_path = 'http://'.$this->user.'.tumblr.com/api/';
	}
	
	function __construct() {
		$CI =& get_instance();
	}
}

?>