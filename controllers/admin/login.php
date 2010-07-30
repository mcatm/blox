<?php

class Login extends Controller {

	function index() {
		if ($this->auth->login($this->input->post('email'), $this->input->post('pwd'))) {
			$redirect = ($this->input->post('redirect')) ? $this->input->post('redirect') : base_url().'admin/';
			header('location:'.$redirect);
		} else {
			$this->load->view('login.php');
		}
	}
	
	function Login() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}