<?php

class Cron extends Controller {

	function index() {//cronjob（15〜20分毎に）
		$this->load->library('cron');
	}
	
	function Cron() {
		parent::Controller();
		#if (!defined('CRON')) header('location:'.base_url());
	}
}