<?php

class Feed extends Controller {

	function index() {//RSS出力
		$this->load->library(array('post'));
		
		$this->post->get(array(
			'qty'			=> 15,
			'type'			=> 0
		));
		
		$this->load->feed('post.php');
	}
	
	function user($account = "") {
		$this->load->library(array('post'));
		
		if (!is_integer($account)) $account = $this->_get_user_id($account);
		if (empty($account)) show_404();
		
		$this->post->get(array(
			'qty'			=> 15,
			'user'			=> $account,
			'type'			=> 0
		));
		
		$this->load->feed('post.php');
	}
	
	function comment($account = "") {
		$this->load->library(array('post'));
		
		$param = array(
			'qty'			=> 15,
			'type'			=> 1
		);
		
		if (!is_integer($account)) $account = $this->_get_user_id($account);
		if ($account != "") $param['user'] = $account;
		$this->post->get($param);
		
		$this->load->feed('post.php');
	}
	
	function _get_user_id($account = "") {
		$this->load->library('user');
		$u = $this->user->get(array(
			'account' => $account,
			'stack'	=> false
		));
		if (empty($u)) return false;
		return $u[0]['id'];
	}
	
	function Feed() {
		parent::Controller();
	}
}