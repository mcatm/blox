<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function init() {
	if (defined('CRON')) return false;
	$CI =& get_instance();
	$CI->setting->init();//設定読込
	$CI->auth->init();//ログインデータ読込
	$CI->blox->init();
	
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
	}
	
	if (defined('ADMIN_MODE') && ADMIN_MODE === true) {//管理画面
		define('SSL_MODE', true);//SSLモード
		$CI->setting->set('theme', '_admin');
		$CI->setting->set('site_name', 'blox admin');
		if (!$CI->session->userdata('login') || !$CI->auth->check_auth()) {
			if ($CI->uri->segment(2) != 'login') {
				$CI->data->out['redirect'] = self_url();
				print($CI->load->view('login.php', $CI->data->out, true));
				exit;
			}
		}
		if (!isset($CI->data->out['admin_menu'])) $CI->data->out['admin_menu'] = $CI->setting->get_admin_menu();//管理メニュー取得
	} elseif (defined('HOME_MODE') && HOME_MODE === true) {//HOME画面
		define('SSL_MODE', true);//SSLモード
		if (!$CI->session->userdata('login') || !$CI->auth->check_auth('home')) {
			if ($CI->uri->segment(1) != 'login') {
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
}