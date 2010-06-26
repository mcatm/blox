<?php

class Delete extends Controller {
	
	var $msg;
	
	function post($user_param = array('redirect_success' => '', 'redirect_error' => '')) {
		$this->load->library('post');
		
		$param = array();
		$param = array_merge($param, $user_param);
		
		$this->post->delete();
	}
	
	function linx($type, $a, $b) {
		$this->load->library('linx');
		
		$where = array(
			'a'			=> $a,
			'b'			=> $b
		);
		
		$this->linx->delete($type, $where);
	}
	
	function div($user_param = array('redirect_success' => '', 'redirect_error' => '')) {
		$this->load->library('div');
		
		$param = array();
		$param = array_merge($param, $user_param);
		
		$this->div->delete();
	}
	
	function mail($user_param = array('redirect_success' => '', 'redirect_error' => '')) {
		$param = array();
		$param = array_merge($param, $user_param);
		
		$this->log->delete();
	}
	
	function index() {
		header('location:'.base_url());
	}
	
	function Delete() {
		parent::Controller();
	}
}