<?php

class Core extends Model {
	
	function init() {
		if (defined('IS_CRON')) return false;
		$CI =& get_instance();
		
		$CI->setting->init();	//Initialize Env
		$CI->auth->init();		//Initialize Auth Data
		
		//$this->trigger = $CI->config->item('blox_trigger');//トリガー取得
		
		//Module : initialize modules
		$load_module = array();
		$m_arr = explode(',', $CI->setting->get('module'));
		if (!empty($m_arr)) {
			$CI->load->library('module');
			foreach ($m_arr as $l) {
				$lm = explode(':', $l);
				if (count($lm) < 2) $lm[1] = $lm[0];
				$load_module['name'][]	= $lm[0];
				$load_module['alias'][]	= $lm[1];
			}
		
			$module_path = array(
				SITE_FOLDER.'/modules/%s/',
				APP_FOLDER.'/modules/%s/'
			);
			
			foreach ($load_module['name'] as $k=>$v) {
				foreach ($module_path as $m) {
					$p = str_replace('%s', $v, $m);
					if (is_dir($p)) {
						$mod_loaded[$load_module['alias'][$k]] = array(
							'name'	=> $load_module['name'][$k],
							'alias'	=> $load_module['alias'][$k],
							'path'	=> $p
						);
						$CI->module->load_config($load_module['name'][$k], $p);
						$CI->module->load_lang($load_module['name'][$k], $p);
						continue 2;
					}
				}
			}
		}
		if (!empty($mod_loaded)) $CI->setting->set('mod_loaded', $mod_loaded);
		
		//Plugin : 関数の提供（CIのヘルパー的）
		$plugins = directory_map(PLUGIN_FOLDER);
		if (is_array($plugins)) {
			if (isset($plugins) && is_array($plugins)) {//Pluginの読み込み
				foreach($plugins as $p) {
					$p = PLUGIN_FOLDER.'/'.$p;
					if (is_file($p)) require_once($p);
				}
			}
		}
		
		/*
		if (defined('ADMIN_MODE') && ADMIN_MODE === true) {//管理画面
			define('SSL_MODE', true);//SSLモード
			$CI->setting->set('theme', '_admin');
			$CI->setting->set('site_name', 'Admin - '.$CI->setting->get('site_name'));
			$CI->data->out['admin_menu'] = $CI->setting->get_admin_menu();
			
			if (!$CI->session->userdata('login') || !$CI->auth->check_auth()) {
				if ($CI->uri->segment(2) != 'login') {
					$CI->data->out['redirect'] = self_url();
					print($CI->load->view('login.php', $CI->data->out, true));
					exit;
				}
			}
		} elseif (defined('API_MODE') && API_MODE === true) {//API
			if (!$CI->setting->get('open_api')) show_error('API access is not arrowed.', 403);
			define('SSL_MODE', true);//SSLモード
			$CI->setting->set('theme', '_api');
		} else {
			$CI->log->set_access();//アクセス解析
		}
		
		$CI->blox->action('c:'.substr($CI->uri->uri_string(), 1));
		
		if (defined("DEBUG_MODE") && DEBUG_MODE === true) $CI->output->enable_profiler(TRUE);
		
		if (defined('SSL_MODE') && SSL_MODE === true) {//SSLモード
			if(defined('USE_SSL') && USE_SSL === true) {
				$CI->config->config['base_url'] = base_url(true);
				if ($_SERVER['SERVER_PORT'] != 443) header('location:'.str_replace(base_url(), base_url(true), self_url()));
			}
		}
		*/
		
		
		
		
		
		/*
		//ユーザーエージェント取得
		if ($CI->setting->get('switch_useragent')) {
			if ($CI->agent->is_mobile()) {//mobile
				switch($CI->agent->mobile()) {
					case 'Apple iPhone':
						$ua = 'iphone';
					break;
					
					default:
						$ua = 'mobile';
					break;
				}
			} else {
				switch($CI->agent->mobile()) {
					case 'Nintendo Wii':
						$ua = 'wii';
					break;
					
					default:
						$ua = 'pc';
					break;
				}
			}
			
			$CI->setting->set('user_agent', $ua);
			if ($CI->setting->get('switch_useragent_'.$ua)) {
				if ($CI->uri->segment(1) != $CI->setting->get('switch_useragent_'.$ua)) {
					$redirect = base_url().$CI->setting->get('switch_useragent_'.$ua).'/';
					$redirect .= ($CI->uri->uri_string() != "") ? trim($CI->uri->uri_string(), '/').'/' : "";
					header('location:'.$redirect);
					exit;
				}
			}
		}*/
	}
}

?>