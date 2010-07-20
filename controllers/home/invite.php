<?php

class Invite extends Controller {

	function index() {//招待
		$this->load->library(array('post'));
		
		$this->load->view('home/invite.php');
	}
	
	function Invite() {
		parent::Controller();
		define('HOME_MODE', true);//ホーム画面モード
	}
}