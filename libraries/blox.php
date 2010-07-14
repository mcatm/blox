<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Blox {
	
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
				if (preg_match($target_path, $path)) $this->$v['method']($param);
			}
		}
	}
	
	function Blox() {
		$CI =& get_instance();
		$CI->load->helper('directory');
		$this->trigger = $CI->config->item('blox_trigger');//トリガー取得
		
		//extension : コアの拡張
		$extension_arr = directory_map(LIB_FOLDER.'/extension');
		$loaded_extension = array();
		if (!empty($extension_arr)) {
			$CI->load->library('extension');
			foreach ($extension_arr as $k=>$v) {
				if (is_file(LIB_FOLDER.'/extension/'.$k.'/core.php')) {
					require_once(LIB_FOLDER.'/extension/'.$k.'/core.php');
					$CI->extension->$k = new $k;
					$loaded_extension[] = $k;
				}
			}
		}
		$CI->setting->set('extension_loaded', $loaded_extension);
		#print '1: set extensions<br />';
		#print_r($CI->setting->get('extension_loaded'));
		
		if (isset($this->trigger) && is_array($this->trigger)) {//Lib : 追加機能
			foreach($this->trigger as $k=>$v) {
				$p = LIB_FOLDER.'/lib/'.$k.'.php';
				if (is_file($p)) {
					require_once($p);
					$path = explode('.', $k);
					$method = 'blx_'.$path[0];
					$this->libs[]['method'] = $method;
					$CI->$method = new $path[0];
					$CI->$method->trigger = $this->trigger[$path[0]];
				}
			}
		}
		
		$blx_arr = directory_map(LIB_FOLDER.'/plugin');
		
		if (is_array($blx_arr)) {
			//Plugin : 関数の提供（CIのヘルパー的）
			if (isset($blx_arr) && is_array($blx_arr)) {//Pluginの読み込み
				foreach($blx_arr as $p) {
					$p = LIB_FOLDER.'/plugin/'.$p;
					if (is_file($p)) require_once($p);
				}
			}
		}
	}
}

?>