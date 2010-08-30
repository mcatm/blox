<?php

class Logout extends Controller {

	function index() {
		$this->auth->logout();
		$redirect = ($this->input->post('redirect')) ? $this->input->post('redirect') : base_url().'admin/';
		header('location:'.$redirect);
	}
	
	function Logout() {
		parent::Controller();
		#define('ADMIN_MODE', true);//管理画面モード
	}
}