<?php

class Install extends Controller {

	function index($offset = 0) {
		$this->setting->set_title($this->lang->line('system_label_install'));
		$this->load->view('install.list.php');
	}
	
	function twitter() {
		$this->load->library('ext/twitter');
		if ($auth = $this->auth->oauth(NULL, NULL, base_url().'admin/install/twitter/')) {
			foreach($auth as $k => $v) {
				$this->setting->store('twitter_'.$k, $v);
			}
			exit('Success');
		} else {
			print 'NG';
		}
	}
	
	function Install() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}