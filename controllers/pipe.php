<?php

class Pipe extends Controller {
	
	function _view($method, $type = NULL) {
		$param = array('where' => 'div_alias = "'.$this->setting->get_alias($method).'"');
		if ($type !== NULL) $param['type'] = $type;
		
		/*$this->div->get($param);
		
		if (isset($this->data->out['div'])) {
			#print_r($this->data->out['div']);exit;
			
			$type = (!empty($this->data->out['div'][0]['type'])) ? $this->data->out['div'][0]['type'] : 'page';
			$tpl = (!empty($this->data->out['div'][0]['tpl'])) ? $this->data->out['div'][0]['tpl'] : $type.'.php';
			
			$this->load->library('module');
			$this->module->view(array(
				'description'	=> $this->data->out['div'][0]['description'],
				'tpl'	=> $tpl
			));
			exit;
		}*/
		
		return false;
	}
	
	function _remap($method) {//switch as url segment
		
		if ($method == 'index') $method = 'top';
		
		// Module
		if ($mod_loaded = $this->setting->get('mod_loaded')) {
			$this->load->library('module');
			if (array_key_exists($method, $mod_loaded)) {
				require_once($mod_loaded[$method]['path'].'core.php');
				$this->mod->$mod_loaded[$method]['name'] = new $mod_loaded[$method]['name'];
				$this->mod->$mod_loaded[$method]['name']->init($mod_loaded[$method]['name'], $mod_loaded[$method]['path']);
				$this->mod->$mod_loaded[$method]['name']->controller($mod_loaded[$method]['name']);
				exit;
			}
		}

		$this->_view(trim($this->uri->uri_string(), '/'));	// Page
		$this->_view($method, 'section');					// Section

		show_404();
	}
	
	function Pipe() {
		parent::Controller();
	}
}