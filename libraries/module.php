<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module {
	
	var $module_path = "";
	var $admin_menu = array();
	
	function controller($mod, $mode = '') {
		$CI =& get_instance();
		
		$uri_segment = $CI->uri->segment_array();
		
		$MD =& $CI->mod->$mod;
		$mod_loaded = $CI->setting->get('mod_loaded');
		define('MOD_CONTROLLER', $mod);
		
		if (empty($uri_segment)) $uri_segment[] = 'top';
		
		#print_r($uri_segment);
		
		//make instance of certain controller by using the uri segments.
		$ctl = $this->module_path.'controller/';
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
		if (!$ctlflg && is_file($ctl = $this->module_path.'controller/top.php')) {
			$ctlpath = $ctl;
			$classname = 'top';
			$ctlflg = true;
		}
		
		if ($ctlflg) {
			include($ctlpath);
			
			$method = (isset($uri_segment[$i+1])) ? $uri_segment[$i+1] : 'index';
			
			$classname = 'Mod_'.$classname;
			
			#print $CI->setting->get('theme');
			
			$BX = new $classname;//redefine the instance of controller
			
			foreach ($CI as $k => $m) {
				#print_r($k);
				$BX->$k = $CI->$k;
				#print'<br />';
			}
			
			$CI = $BX;
			
			if (method_exists($CI, '_remap')) $CI->_remap($method);
			
			call_user_func_array(array(&$CI, $method), array_slice($CI->uri->rsegments, 2));
						
			exit;
		}
		
		show_404();
		
		/*
		$uri_segment[] = ($mode == "") ? 'top' : '_'.$mode;
		for ($i=1;$CI->uri->segment($i);$i++) {
			if (is_numeric($CI->uri->segment($i))) break;
			if ($mode != "" && ($CI->uri->segment($i) == 'mod' || $CI->uri->segment($i) == $mod)) continue;
			if ($i != 1) $uri_segment[] = $CI->uri->segment($i);
		}
		*/
		
		
		
		
		
		
		
		/*
		// Is there a "remap" function?
		if (method_exists($CI, '_remap')) {
			$CI->_remap($method);
			exit('UUUU');
		} else {
			// is_callable() returns TRUE on some versions of PHP 5 for private and protected
			// methods, so we'll use this workaround for consistent behavior
			if ( ! in_array(strtolower($method), array_map('strtolower', get_class_methods($CI)))) {
				show_404("{$class}/{$method}");
			}

			// Call the requested method.
			// Any URI segments present (besides the class/function) will be passed to the method for convenience
			call_user_func_array(array(&$CI, $method), array_slice($URI->rsegments, 2));
		}
		*/
		
		
	}
	
	function view($user_param = array()) {
		$CI =& get_instance();
		
		/*
		
		$param
		type: content type
		tpl:	テンプレートファイル
		theme:	テーマ
		
		*/
		
		$param = array(
			'keyword'			=> array(),//for SEO
			'description'		=> "",//for SEO
			'title'				=> '{@sitename}',
			'rss'				=> '',
			'flg_title_clear'	=> true
		);
		
		$div = array();
		if (isset($CI->data->out['div'][0])) {
			foreach ($CI->data->out['div'][0] as $dk => $dv) {
				if (!empty($d)) $div[$dk] = $dv;
			}
		}
		
		$param = array_merge($param, $user_param, $div);
		
		//コンテンツ取得
		if (isset($div['content']) && is_array($div['content'])) {
			foreach ($div['content'] as $c) {
				$CI->load->library($c['type']);
				$c['param']['offset'] = (isset($param['segment']['offset'])) ? $param['segment']['offset'] : 0;
				$p = (isset($c['param']) && is_array($c['param']) && !empty($c['param'])) ? $c['param'] : array();
				$CI->$c['type']->get($p);
			}
		}
		
		//META
		$CI->setting->set_title($param['title'], $param['flg_title_clear']);
		$CI->setting->set_keyword($param['keyword'], true);
		$CI->setting->set_rss($param['rss']);
		$CI->setting->set_description(str_replace("\n", '', $param['description']));
		
		if (isset($param['theme'])) $CI->setting->set('theme', $param['theme']);
		$CI->load->view($param['tpl']);
	}
	
	function init($name, $module_path) {
		$CI =& get_instance();
		$this->module_path = $module_path;
		
		$this->load_config($name, $module_path);//load a config file.
		
		//load a language file
		
	}
	
	function load_config($name, $module_path) {
		$CI =& get_instance();
		
		$config_path = $module_path.'config.php';
		$cfg_prefix = 'mod_'.$name.'_';
		
		if (is_file($config_path)) {
			require_once($config_path);
			if (!empty($config)) {
				foreach ($config as $k => $v) {
					$CI->setting->set($cfg_prefix.$k, $v);
				}
				if (!empty($admin_menu) && defined('ADMIN_MODE')) $CI->data->out['admin_menu'][$name] = $admin_menu;
			}
		}
	}
	
	function load_lang($name, $module_path) {
		$CI =& get_instance();
		
		$lang_path = $module_path.'language/'.$CI->config->item('language').'.php';
		if (is_file($lang_path)) {
			$CI->config->set_item('mod_language_path', $lang_path);
			$CI->lang->load($name);//拡張言語ファイル読込
		}
	}
	
	function Module() {
		
	}
}

?>