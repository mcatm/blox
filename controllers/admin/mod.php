<?php

class Mod extends Controller {

	function _remap($method) {
		if ($mod_loaded = $this->setting->get('module_loaded')) {
			
			$this->load->library('module');
			if (array_key_exists($method, $mod_loaded)) {
				
				require_once($mod_loaded[$method]['path'].'core.php');
				
				$this->mod->$mod_loaded[$method]['name'] = new $mod_loaded[$method]['name'];
				$this->mod->$mod_loaded[$method]['name']->init($mod_loaded[$method]['name'], $mod_loaded[$method]['path']);
				$this->mod->$mod_loaded[$method]['name']->controller($mod_loaded[$method]['name'], 'admin');
				exit($method);
				exit;
			}
		}
		
		
		
		
		/*
		if (in_array($method, $this->setting->get('module_loaded'))) {
			exit($method);
			require_once($mod_loaded[$method]['path'].'core.php');
			$this->mod->$mod_loaded[$method]['name'] = new $mod_loaded[$method]['name'];
			$this->mod->$mod_loaded[$method]['name']->init($mod_loaded[$method]['name'], $mod_loaded[$method]['path']);
			$this->mod->$mod_loaded[$method]['name']->controller($mod_loaded[$method]['name']);
			
			
			
			$this->module->$method->controller($method, 'admin');
			exit;
		}*/
	}
	
	function Mod() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}