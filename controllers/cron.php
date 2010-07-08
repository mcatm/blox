<?php

class Cron extends Controller {

	function index() {//cronjob（15〜20分毎に）
		//指定日投稿
		#define('DEBUG_MODE', true);
		
		$this->load->library(array('post'));
		$this->load->helper('date');
		$post = $this->post->get(array(
			'where'	=> 'post_createdate < "'.now().'"',
			'status'	=> 1,
			'order'	=> 'asc',
			'auth'	=> 'cron',
			'pager'	=> false,
			'stack'	=> false
		));
		
		if (!empty($post)) {
			foreach($post as $v) $this->db->or_where('post_id', $v['id']);
			$this->db->update(DB_TBL_POST, array('post_status' => 0));
		}
	}
	
	function Cron() {
		parent::Controller();
		if (!defined('CRON')) header('location:'.base_url());
	}
}