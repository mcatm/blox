<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Loader extends CI_Loader {

	function BLX_Loader() {	
		parent::CI_Loader();
		#$this->_ci_view_path = THEME_FOLDER. '/';
	}
		
	function view($view, $vars = array(), $return = FALSE) {
		$CI =& get_instance();
		
		if (!defined('ALREADY_BLOX_ACTIONED')) {//一回だけ、プラグイン動作
			$CI->blox->action('e:'.substr($CI->uri->uri_string(), 1));
			define('ALREADY_BLOX_ACTIONED', true);
		}
		
		if (count($vars) == 0) $vars = $CI->data->out;//Output自動呼び出し
		
		$theme_folder = THEME_FOLDER.'/';
		if (!defined('EXTENSION_CONTROLLER') || is_file($theme_folder.$CI->setting->get('theme').'/html/'.$view)) {
			$this->_ci_view_path = $theme_folder;
			$theme_path = $CI->setting->get('theme').'/html/'.$view;
		} else {//拡張コアの場合
			$this->_ci_view_path = LIB_FOLDER.'/extension/';
			$theme_path = EXTENSION_CONTROLLER.'/view/'.$view;
		}
		return $this->_ci_load(array(
			'_ci_view'		=> $theme_path,
			'_ci_vars'		=> $this->_ci_object_to_array($vars),
			'_ci_return'	=> $return
		));
	}
	
	function feed($view, $vars = array(), $return = FALSE) {
		$CI =& get_instance();
		
		if (count($vars) == 0) $vars = $CI->data->out;//Output自動呼び出し
		
		$theme_path = '_rss/'.$view;
		
		return $this->_ci_load(array(
			'_ci_view'		=> $theme_path,
			'_ci_vars'		=> $this->_ci_object_to_array($vars),
			'_ci_return'	=> $return
		));
	}
}

?>