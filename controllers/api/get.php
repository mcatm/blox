<?php

class Get extends Controller {
	
	var $msg;
	
	function posts() {
		$this->load->library('post');
		
		$user_param = $this->_get_param();
		
		$param = array(
			'qty'		=> 10,
			'type'		=> 0,
			'auth'		=> 0,
			'stack'		=> true
		);
		
		$this->post->get(array_merge($param, $user_param));
		
		$format = (!isset($_GET['format'])) ? 'xml' : $_GET['format'];
		$this->load->api('post.list.php', $format);
	}
	
	function comments() {
		$this->load->library('post');
		
		$user_param = $this->_get_param();
		
		$param = array(
			'qty'		=> 10,
			'type'		=> 1,
			'auth'		=> 0,
			'stack'		=> true
		);
		
		$this->post->get(array_merge($param, $user_param));
		
		$format = (!isset($_GET['format'])) ? 'xml' : $_GET['format'];
		$this->load->api('comment.list.php', $format);
	}
	
	function tag() {
		$this->load->library('tag');
		
		$dat = $this->tag->get(array(
			'qty' => 1000,
			'sort'	=> 'count',
			'stack'	=> false,
			'order'	=> 'desc'
		));
		
		$tag = array();
		if (isset($dat)) {
			foreach($dat as $k => $v) {
				$tag[] = $v['name'];
			}
		}
		$this->output->ajax($tag, array('type' => 'json'));
	}
	
	function _get_param() {
		$param = array();
		
		foreach($_GET as $k => $v) {
			switch ($k) {
				case 'auth';
				break;
				
				default:
				$param[$k] = $v;
				break;
			}
		}
		
		return $param;
	}
	
	function _remap($method) {
		$m = explode('?', $method);
		$this->$m[0]();
	}
	
	function index() {
		header('location:'.base_url());
	}
	
	function Get() {
		parent::Controller();
		define('API_MODE', true);
	}
}