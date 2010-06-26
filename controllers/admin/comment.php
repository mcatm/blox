<?php

class Comment extends Controller {

	function index($offset = 0) {
		$this->load->library('post');
		$this->load->helper('filter');
		$where = array(
			'qty'			=> 20,
			'offset'		=> $offset,
			'uri_segment'	=> 4,
			'base_url'		=> base_url().'admin/comment/offset/',
			'type'			=> 1
		);
		$where = get_filter('admin/comment', $where);
		$this->post->get($where);
		$this->setting->set_title($this->lang->line('system_label_comment'));
		$this->load->view('post.list.php');
	}
	
	function delete() {
		$this->load->library('post');
		foreach ($this->input->post('id') as $k => $v) {
			$id[] = $v;
		}
		$this->post->delete($id);
		header('location:'.base_url().'admin/comment/');
	}
	
	function filter($type = "", $param_1 = "", $param_2 = "") {
		$this->load->helper('filter');
		set_filter('admin/comment', $type, $param_1, $param_2);
		$this->index();
	}
	
	function edit($id = 0) {
		$this->load->library('post');
		
		$msg = $this->post->set($id);
		
		switch ($msg['result']) {
			case 'success':
				#print_r($msg);
				#if ($param['redirect_success'] != "") header('location:'.$param['redirect_success']);
			break;
			
			case 'error':
			default:
				#print_r($msg);
				#if ($param['redirect_error'] != "") header('location:'.$param['redirect_error']);
			break;
		}
		$this->load->view('post.form.php');
	}
	
	function add() {
		$this->load->library('post');
		$this->post->clear();
		$this->load->view('post.form.php');
	}
	
	function _remap($m) {
		switch ($m) {
			case 'index':
			case 'offset':
				$this->index($this->uri->segment(4));
			break;
			
			case 'filter':
				$this->filter($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
			break;
			
			case 'edit':
				$this->edit($this->uri->segment(4));
			break;
			
			case 'delete':
				$this->delete();
			break;
			
			case 'new':
			case 'add':
				$this->add();
			break;
			
			default:
				$this->detail($m);
			break;
		}
	}
	
	function Comment() {
		parent::Controller();
		$this->data->out['post_type'] = 'comment';
		define('ADMIN_MODE', true);//管理画面モード
	}
}