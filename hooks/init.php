<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function init() {
	$CI =& get_instance();
	$CI->setting->init();//設定読込
	$CI->auth->init();//ログインデータ読込
	if (defined('ADMIN_MODE') && ADMIN_MODE === true) {//管理画面
		define('SSL_MODE', true);//SSLモード
		if (!$CI->session->userdata('login') || !$CI->auth->check_auth()) {
			if ($CI->uri->segment(2) != 'login') header('location:'.base_url().'admin/login');//管理者権限のない場合、ログイン画面へ遷移
		}
		$CI->data->out['admin_menu'] = $CI->setting->get_admin_menu();//管理メニュー取得
		$CI->setting->set('theme', '_admin');
		$CI->setting->set('site_name', 'blox admin');
	} elseif (defined('HOME_MODE') && HOME_MODE === true) {
		define('SSL_MODE', true);//SSLモード
		if (!$CI->session->userdata('login') || !$CI->auth->check_auth('home')) {
			if ($CI->uri->segment(2) != 'login') header('location:'.base_url().'login');//管理者権限のない場合、ログイン画面へ遷移
		}
		$CI->setting->set('theme', 'home');
	} else {
		$CI->log->set_access();//アクセス解析
	}
	
	$CI->blox->action('c:'.substr($CI->uri->uri_string(), 1));
	
	if (defined('SSL_MODE') && SSL_MODE === true) {//SSLモード
		if(defined('USE_SSL') && USE_SSL === true) {
			$CI->config->config['base_url'] = base_url(true);
			if ($_SERVER['SERVER_PORT'] != 443) header('location:'.str_replace(base_url(), base_url(true), self_url()));
		}
	}
}