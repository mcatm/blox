<?php

class Tag extends Controller {

	function index($offset = 0) {
		$this->load->library('tag');
		$this->load->helper('filter');
		$where = array(
			'qty'			=> 100,
			'offset'		=> $offset,
			'uri_segment'	=> 4,
			'base_url'		=> base_url().'admin/tag/offset/',
			'type'			=> 0
		);
		$where = get_filter('admin/tag', $where);
		$this->tag->get($where);
		$this->setting->set_title($this->lang->line('system_label_tag'));
		$this->load->view('tag.list.php');
	}
	
	/*function detail($id) {
		$this->load->library('post');
		$where = array();
		$where['id'] = $id;
		$where['file_main']	= true;
		$where['file'] = true;
		if ($this->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 10;//下書き閲覧権限
		$where['history'] = true;
		$this->post->get($where);
		if (isset($this->data->out['post'])) {
			$access_path = 'access/'.$this->setting->get('url_alias_post').'/'.$id.'/';
			$this->data->set_array('access', $this->log->get_access($access_path));//アクセス解析
			$this->setting->set_title($this->data->out['post'][0]['title']);
		}
		$this->load->view('post.detail.php');
	}*/
	
	function filter($type = "", $param_1 = "", $param_2 = "") {
		$this->load->helper('filter');
		set_filter('admin/tag', $type, $param_1, $param_2);
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
		$this->setting->set_title($this->lang->line('system_post_edit').' '.$this->lang->line('system_label_post'));
		$this->load->view('post.form.php');
	}
	
	function add() {
		$this->load->library('post');
		$this->post->clear();
		$this->setting->set_title($this->lang->line('system_post_add').' '.$this->lang->line('system_label_post'));
		$this->load->view('post.form.php');
	}
	
	function delete() {
		$this->load->library('tag');
		foreach ($this->input->post('id') as $k => $v) {
			$id[] = $v;
		}
		$this->tag->delete($id);
		header('location:'.base_url().'admin/tag/');
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
	
	function Tag() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}