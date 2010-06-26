<?php

class Logout extends Controller {

	function index() {//ログアウト
		$this->auth->logout();
		$redirect = ($this->input->post('redirect')) ? $this->input->post('redirect') : base_url();
		header('location:'.$redirect);
	}
	
	function Logout() {
		parent::Controller();
	}
}