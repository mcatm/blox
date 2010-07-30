<?php

class Post extends Controller {

	function index($offset = 0) {
		$this->load->library('post');
		$this->load->helper('filter');
		$where = array(
			'qty'			=> 20,
			'offset'		=> $offset,
			'uri_segment'	=> 4,
			'base_url'		=> base_url().'admin/post/offset/',
			'type'			=> 0
		);
		if ($this->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 9;
		$where = get_filter('admin/post', $where);
		$this->post->get($where);
		$this->setting->set_title($this->lang->line('system_label_post_list'));
		$this->load->view('post.list.php');
	}
	
	function detail($id) {
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
			$this->log->get_access($access_path);
			//$this->data->set_array('access', $this->log->get_access($access_path));//アクセス解析
			$this->setting->set_title($this->data->out['post'][0]['title']);
		}
		$this->load->view('post.detail.php');
	}
	
	function delete() {
		$this->load->library('post');
		foreach ($this->input->post('id') as $k => $v) {
			$id[] = $v;
		}
		$this->post->delete($id);
		header('location:'.base_url().'admin/post/');
	}
	
	function filter($type = "", $param_1 = "", $param_2 = "") {
		$this->load->helper('filter');
		set_filter('admin/post', $type, $param_1, $param_2);
		$this->index();
	}
	
	function edit($id = 0) {
		$this->load->library(array('post', 'div', 'user'));
		$this->div->get(array('type' => 'section', 'label' => 'section'));
		$this->div->get(array('type' => 'category', 'label' => 'category'));
		$this->user->get(array('qty' => 0, 'auth' => array('contributor', 'admin')));
		
		$msg = $this->post->set($id);
		switch ($msg['result']) {
			case 'success':
				$where = array('id' => $msg['id'], 'qty' => 1, 'file' => true, 'schedule' => true);
				if ($this->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 10;
				$this->post->get($where);
			break;
			
			case 'error':
			default:
				$where = array('id' => $id, 'qty' => 1, 'file' => true, 'schedule' => true);
				if ($this->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 10;
				if ($id > 0) $this->post->get($where);
			break;
		}
		$this->setting->set_title($this->lang->line('system_post_edit'));
		$this->load->view('post.form.php');
	}
	
	function add() {
		$this->load->library(array('post', 'user', 'ext'));
		$this->div->get(array('type' => 'section', 'label' => 'section'));
		$this->div->get(array('type' => 'category', 'label' => 'category'));
		$this->user->get(array('qty' => 0, 'auth' => array('contributor', 'admin')));
		$this->ext->get(array('stack' => true, 'div' => 'post'));
		$this->post->clear();
		$this->setting->set_title($this->lang->line('system_post_add'));
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
			
			case 'new':
			case 'add':
				$this->add();
			break;
			
			case 'delete':
				$this->delete();
			break;
			
			default:
				$this->detail($m);
			break;
		}
	}
	
	function Post() {
		parent::Controller();
		$this->data->out['post_type'] = 'post';//記事タイプ
		define('ADMIN_MODE', true);//管理画面モード
	}
}