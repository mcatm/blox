<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Extension {
	
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
	}
	
	function Extension() {
		
	}
}

?>