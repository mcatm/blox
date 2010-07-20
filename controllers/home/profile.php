<?php

class Top extends Controller {

	function index() {//ダッシュボード
		$this->load->library(array('post'));
		
		$this->post->get(array(
			'user'	=> $this->data->out['me']['id'],
			'qty'	=> 4
		));
		
		$this->load->view('home/top.php');
	}
	
	function Top() {
		parent::Controller();
		define('HOME_MODE', true);//ホーム画面モード
	}
}