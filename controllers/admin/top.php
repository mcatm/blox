<?php

class Top extends Controller {

	function index() {
		$this->load->library(array('post', 'mail'));
		$this->post->get(array(
			'qty'			=> 10,
			'type'			=> 0
		));
		
		$this->post->get(array(
			'qty'			=> 10,
			'type'			=> 1,
			'label'			=> 'comment'
		));
		
		$this->user->get(array(
			'qty'			=> 2,
			'label'			=> 'user'
		));
		
		$this->post->ondate(array(
			'qty'			=> 10,
			'label'			=> 'weekly',
			'schedule'		=> true,
			'startdate'		=> 0,
			'enddate'		=> 7
		));
		
		$this->mail->get(array(
			'label'			=> 'mail',
			'qty'			=> 3
		));
		
		$this->setting->set_title('');
		$this->load->view('dashboard.php');
	}
	
	function Top() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}