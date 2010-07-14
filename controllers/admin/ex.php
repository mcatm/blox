<?php

class Ex extends Controller {

	function _remap($method) {
		#exit($method);
		if (in_array($method, $this->setting->get('extension_loaded'))) {
			$this->extension->$method->controller($method, 'admin');
			exit;
		}
	}
	
	function Ex() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}