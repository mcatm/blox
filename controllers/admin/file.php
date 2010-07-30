<?php

class File extends Controller {

	function index($offset = 0) {
		$this->load->library('file');
		$this->load->helper('filter');
		$where = array(
			'uri_segment'	=> 4,
			'offset'	=> $offset,
			'base_url'		=> base_url().'admin/file/offset/',
			'qty'	=> 20
		);
		$where = get_filter('admin/file', $where);
		$this->file->get($where);
		$this->setting->set_title($this->lang->line('system_file_list'));
		$this->load->view('file.list.php');
	}
	
	function image($offset = 0) {
		$this->load->library('file');
		$this->load->helper('filter');
		$where = array(
			'uri_segment'	=> 4,
			'base_url'		=> base_url().'admin/file/offset/',
			'type'	=> 'image',
			'qty'	=> 20
		);
		$where = get_filter('admin/file', $where);
		$this->file->get($where);
		$this->load->view('file.list.php');
	}
	
	function sound($offset = 0) {
		$this->load->library('file');
		$this->load->helper('filter');
		$where = array(
			'uri_segment'	=> 4,
			'base_url'		=> base_url().'admin/file/offset/',
			'type'	=> 'sound',
			'qty'	=> 20
		);
		$where = get_filter('admin/file', $where);
		$this->file->get($where);
		$this->load->view('file.list.php');
	}
	
	function delete() {
		$this->load->library('file');
		foreach ($this->input->post('id') as $k => $v) {
			$id[] = $v;
		}
		$this->file->delete($id);
		header('location:'.base_url().'admin/file/');
	}
	
	function filter($type = "", $param_1 = "", $param_2 = "") {
		$this->load->helper('filter');
		set_filter('admin/file', $type, $param_1, $param_2);
		$this->index();
	}
	
	function edit($id = 0) {
		$this->load->library(array('file', 'form_validation'));
		
		$msg = $this->file->set_information($id);
		switch ($msg['result']) {
			case 'success':
				$this->file->get(array('id' => $msg['id']));
			break;
			
			case 'error':
			default:
				$where = array('id' => $id);
				if ($this->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 10;
				if ($id > 0) $this->file->get($where);
			break;
		}
		$this->setting->set_title($this->lang->line('system_file_edit'));
		
		$this->load->view('file.detail.php');
	}
	
	function add() {
		#exit(ini_get('upload_max_filesize'));
		#print_r($_FILES);exit;
		$this->load->library('file');
		$this->file->set();
		$this->file->get(array(
			'qty'	=> 20
		));
		$this->load->view('file.form.php');
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
			
			case 'img':
			case 'image':
				$this->image($this->uri->segment(4));
			break;
			
			case 'snd':
			case 'sound':
				$this->sound($this->uri->segment(4));
			break;
			
			case 'edit':
				$this->edit($this->uri->segment(4));
			break;
			
			case 'delete':
				$this->delete();
			break;
			
			case 'new':
			case 'add':
			case 'upload':
				$this->add();
			break;
			
			default:
				$this->detail($m);
			break;
		}
	}
	
	function File() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}