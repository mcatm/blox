<?php

class Get extends Controller {
	
	var $msg;
	
	function post($id = 0, $user_param = array('redirect_success' => '', 'redirect_error' => '')) {
		$this->load->library('post');
		
		$param = array(
			'qty'		=> 10,
			'stack'		=> true,
			'theme'		=> $this->input->post('theme'),
			'tpl'		=> $this->input->post('tpl'),
			'type'		=> $this->input->post('type')
		);
		
		$param = array_merge($param, $user_param);
		
		if ($this->input->post('q') != "") {
			$param['query'] = $this->input->post('q');
		}
		
		$this->post->get($param);
		
		if (!isset($this->data->out['post'])) $this->data->out['post'] = array();
		$this->output->ajax($this->data->out['post'], $param);
	}
	
	function file() {
		$this->load->library('file');
		
		$this->data->set_array('file_segment', explode('|', $this->setting->get('file_segment')));
		
		$param = array(
			'stack'		=> true,
			'theme'		=> $this->input->post('theme'),
			'tpl'		=> $this->input->post('tpl'),
			'datatype'		=> $this->input->post('type')
		);
		
		if ($this->input->post('qty')) $param['qty'] = $this->input->post('qty');
		if ($this->input->post('offset')) $param['offset'] = $this->input->post('offset');
		if ($this->input->post('post_id')) $param['post_id'] = $this->input->post('post_id');
		if ($this->input->post('user_id')) $param['user_id'] = $this->input->post('user_id');
		
		$this->file->get($param);
		
		if (!isset($this->data->out['file'])) $this->data->out['file'] = array();
		$this->output->ajax($this->data->out['file'], $param);
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
	
	function linx($type, $a, $b, $status = '', $unique = '') {
		
	}
	
	function tpl() {
		if ($this->input->post('theme')) $this->setting->set('theme', $this->input->post('theme'));
		$this->load->view($this->input->post('tpl'));
	}
	
	function zip() {
		$this->load->library('file');
		$this->file->zip();
	}
	
	function index() {
		header('location:'.base_url());
	}
	
	function Get() {
		parent::Controller();
		
		$url = parse_url(base_url());
		if (!preg_match('(^(http|https):\/\/'.$url['host'].')', $this->agent->referrer())) exit;//外部からの参照はNG
	}
}