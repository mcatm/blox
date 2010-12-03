<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blox {
	/*
	var $libs = array();
	var $trigger = array();
	
	function action($path = "") {
		$CI =& get_instance();
		$protocol = substr($path, 0, 1);
		$path = ($path !== "") ? trim(substr($path, 2), '/') : "";
		foreach($this->libs as $k=>$v) {
			$CI->$v['method']->_scan($protocol, $path);
		}
	}
	
	function out($arr = array()) {//アウトプットを整理
		$CI =& get_instance();
		$CI->data->add_array($arr);
	}
	
	function load($core) {
		$CI =& get_instance();
		require_once(LIB_FOLDER.'/core/'.$core.'.php');
		$CI->$core = new $core;
	}
	
	function _scan($protocol, $path = "") {//パスから、プラグインの動作を決定する
		$CI =& get_instance();
		if (isset($this->trigger[$protocol])) {
			foreach($this->trigger[$protocol] as $k=>$v) {
				$target_path = (isset($v['path'])) ? '(^'.$v['path'].')' : "(.*)";
				$param = (isset($v['param'])) ? $v['param'] : array();
				if (preg_match($target_path, $path) && isset($v['method']) && method_exists($this, $v['method'])) $this->$v['method']($param);
			}
		}
	}
	
	function init() {
		
	}*/
}

?>