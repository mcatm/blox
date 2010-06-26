<?php

class Feed extends Controller {

	function index() {//RSS出力
		$this->load->library(array('post'));
		
		$this->post->get(array(
			'qty'			=> 15,
			'type'			=> 0
		));
		
		$this->load->feed('post');
	}
	
	function Feed() {
		parent::Controller();
	}
}