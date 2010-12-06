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
				
				$MD =& $this->mod->$mod_loaded[$method]['name'];
				
				$MD = new $mod_loaded[$method]['name'];
				$MD->init($mod_loaded[$method]['name'], $mod_loaded[$method]['path']);
				#$this->mod->$mod_loaded[$method]['name']->controller($mod_loaded[$method]['name']);
				
				//Controller
				$uri_segment = $this->uri->segment_array();
				$module_path = $mod_loaded[$method]['path'];
		
				define('MOD_CONTROLLER', $mod_loaded[$method]['name']);
				
				if (empty($uri_segment)) $uri_segment[] = 'top';
				
				print_r($uri_segment);
				
				//make instance of certain controller by using the uri segments.
				$ctl = $module_path.'controller/';
				foreach ($uri_segment as $i => $u) {
					$classname = $u;
					$ctlflg = false;
					
					if (is_file($ctl.$classname.'.php') && $classname != 'top') {
						$ctl .= $classname.'.php';
						$ctlflg = true;
					}
					
					if (!$ctlflg) {
						$ctl .= ($u != 'top') ? $classname.'/' : '';
						if (is_file($ctl.'top.php')) {
							if ((count($uri_segment) == 1 && $i == 0) || $i > 0) {
								$classname = 'top';
								$ctl .= 'top.php';
								$ctlflg = true;
							}
						}
					}
					$ctlpath = $ctl;
					#print $ctlpath.'<br />';
					if ($ctlflg) break;
				}
				
				//コントローラーが無くてtop.phpだけ存在する場合、最終的にtop.phpにアクセス
				if (!$ctlflg && is_file($ctl = $module_path.'controller/top.php')) {
					$ctlpath = $ctl;
					$classname = 'top';
					$ctlflg = true;
				}
				
				if ($ctlflg) {
					require_once($ctlpath);
					
					$method = (isset($uri_segment[$i+1])) ? $uri_segment[$i+1] : 'index';
					
					$classname = 'Mod_'.$classname;
					$this->controller = new $classname;
					#exit($classname);
					#if (!method_exists($MD->controller, '_remap')) exit('remap!');//$MD->controller->_remap($method);
					if (!method_exists($this->controller, $method)) $method = 'index';//show_404();//メソッドが存在しない場合、404
					
					$this->controller->$method();
					exit;
				}
				
				show_404();
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