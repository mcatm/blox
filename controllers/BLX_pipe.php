<?php

class BLX_pipe extends Controller {
	
	function _remap($method) {//switch as url segment
		
		$method = ($method == 'index') ? 'top' : $method;
		
		// Module
		if ($mod_loaded = $this->setting->get('mod_loaded')) {
			if (array_key_exists($method, $mod_loaded)) {
				$this->module->init($mod_loaded[$method]['name'], $mod_loaded[$method]['path']);
				$this->module->controller($mod_loaded[$method]['name']);
			}
		}
		
		$this->module->_page();// Page
		$this->module->_archive();// Archives

		show_404();
	}
	
	function __construct() {
		parent::Controller();
	}
}