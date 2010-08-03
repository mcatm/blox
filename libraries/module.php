<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module {
	
	var $module_path = "";
	
	/*var $extension_name = "";
	var $admin_menu = array();
	
	function controller($ext, $mode = '') {
		$CI =& get_instance();
		$EX =& $CI->extension->$ext;
		define('EXTENSION_CONTROLLER', $ext);
		$ctlpath = LIB_FOLDER.'/extension/'.$ext.'/controller.php';
		
		if (is_file($ctlpath)) {
			require_once($ctlpath);
			switch ($mode) {
				case 'admin';
					$EX->controller = new Ex_Admin_Controller;
					$method = ($CI->uri->segment(4)) ? $CI->uri->segment(4) : "index";
					if (!empty($this->admin_menu)) $CI->data->out['admin_menu'] = $this->admin_menu;
				break;
				
				default:
					$EX->controller = new Ex_Controller;
					$method = ($CI->uri->segment(2)) ? $CI->uri->segment(2) : "index";
				break;
			}
			if (!method_exists($EX->controller, $method)) show_404();//メソッドが存在しない場合、404
			$EX->controller->$method();
			exit;
		}
		
		show_404();
	}*/
	
	function init($name, $module_path) {
		$CI =& get_instance();
		#exit($module_path);
		$config_path = $module_path.'config.php';
		$cfg_prefix = 'mod_'.$name.'_';
		if (is_file($config_path)) {
			require_once($config_path);
			
			if (!empty($config)) {
				foreach ($config as $k => $v) {
					$CI->setting->set($cfg_prefix.$k, $v);
				}
				if (!empty($admin_menu)) $this->admin_menu = $admin_menu;
			}
		}
		
		if (is_file(EX_FOLDER.'/language/'.$CI->config->item('language').'/'.$name.'_lang.php')) $CI->lang->load($name);//拡張言語ファイル読込
	}
	
	function Module() {
		
	}
}

?>