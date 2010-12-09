<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Module {
	
	var $module_path = "";
	var $admin_menu = array();
	
	function controller($mod, $mode = '') {
		$CI =& get_instance();
		
		$uri_segment = $CI->uri->rsegment_array();
		$ctlpath = $this->module_path.'controller/index.php';
		
		$mod_loaded = $CI->setting->get('mod_loaded');
		
		$class	= ($uri_segment[2] != 'index') ? $uri_segment[2]:'top';
		$method	= (isset($uri_segment[3]) && !is_numeric($uri_segment[3])) ? $uri_segment[3]:'index';
		define('MOD_CONTROLLER', $class);
		
		$ctl = $this->module_path.'controller/';
		
		foreach($uri_segment as $i => $u) {
			if ($i > 2) {
				if (is_file($ctl.$class.'.php')) {
					$ctlpath = $ctl.$class.'.php';
					continue 2;
				}
				if (is_dir($ctl.$class)) $ctl .= $class.'/';
			}
		}
		
		if (is_file($ctlpath)) {
			include($ctlpath);
			
			$method = (isset($uri_segment[$i+1])) ? $uri_segment[$i+1] : 'index';
			
			$class = 'Mod_'.$class;
			
			$BX = new $class;//redefine the instance of controller
			foreach ($CI as $k => $m) $BX->$k = $CI->$k;
			$CI = $BX;
			
			if (method_exists($CI, '_remap')) $CI->_remap($method);
			call_user_func_array(array(&$CI, $method), array_slice($CI->uri->rsegments, 2));
		}
	}
	
	function _page($uri = "") {
		$CI =& get_instance();
		$u = get_uri_string();
		if (empty($uri)) $uri = $u['uri'];
		if (!empty($uri)) {
			$CI->blox->get(array(
				'qty'	=> 'all',
				'where'	=> array(
					'blox_alias'	=> $uri,
					'blox_type'		=> 'page'
				)
			));
			
			if (isset($CI->output->dat['blox'])) $this->view();
		}
	}
	
	function _archive($alias = "") {
		$CI =& get_instance();
		$u = get_uri_string($CI->uri->uri_string());
		if (empty($alias)) $alias = $u['uri'];
		if (!empty($alias)) {
			$CI->blox->get(array(
				'qty'	=> 1,
				'where'	=> array(
					'blox_alias'	=> $alias,
					'blox_type'		=> 'archive'
				)
			));
			
			if (isset($CI->output->dat['blox'])) $this->view();
		}
	}
	
	function view() {
		$CI =& get_instance();
		$FM =& $CI->output->dat['blox'][0];
		define('MOD_CONTROLLER', true);
		
		// ------ set template file.
		if (!empty($FM['tpl'])) {
			$tpl = $FM['tpl'];
		} else {
			$tpl = (!empty($FM['category'])) ? $FM['category'].'.' : "";
			$tpl .= $FM['type'];
		}
		$tpl .= '.php';
		
		// ------ set theme
		if (!empty($FM['theme'])) $CI->setting->set('theme', $FM['theme']);
		
		// ------ set title
		$CI->setting->set_title($FM['name']);
		#$CI->setting->set_keyword($param['keyword'], true);
		#$CI->setting->set_rss($param['rss']);
		$CI->setting->set_description(format_description($FM['body']));
		
		$CI->load->view($tpl);
		
		
		
		/*
		
		$param
		type: content type
		tpl:	テンプレートファイル
		theme:	テーマ
		
		*/
		
		/*$param = array(
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
		
		
		
		if (isset($param['theme'])) $CI->setting->set('theme', $param['theme']);
		$CI->load->view($param['tpl']);*/
		
		exit();
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
	
	function __construct() {
		
	}
}

?>