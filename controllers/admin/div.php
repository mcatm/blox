<?php

class Div extends Controller {

	function index($offset = 0) {
		$this->load->library('div');
		$this->load->helper('filter');
		$where = array(
			'qty'			=> 40,
			'offset'		=> $offset,
			'uri_segment'	=> 4,
			'sort'			=> 'theme',
			'base_url'		=> base_url().'admin/div/offset/'
		);
		$where = get_filter('admin/div', $where);
		$this->div->get($where);
		$this->setting->set_title($this->lang->line('system_div_list'));
		$this->load->view('div.list.php');
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
			$this->data->set_array('access', $this->log->get_access($access_path));//アクセス解析
			$this->setting->set_title($this->data->out['post'][0]['title']);
		}
		$this->load->view('post.detail.php');
	}
	
	function filter($type = "", $param_1 = "", $param_2 = "") {
		$this->load->helper('filter');
		set_filter('admin/div', $type, $param_1, $param_2);
		$this->index();
	}
	
	function edit($id = 0) {
		$this->load->library(array('div'));
		if ($id > 0) $this->div->get(array('id' => $id));
		
		$msg = $this->div->set($id);
		
		$this->data->set_array('theme', $this->setting->get_theme());
		
		switch ($msg['result']) {
			case 'success':
				$this->div->get(array('id' => $msg['id'], 'qty' => 1));
			break;
			
			case 'error':
			default:
				if ($id > 0) $this->div->get(array('id' => $id, 'qty' => 1));
			break;
		}
		$this->setting->set_title($this->lang->line('system_div_edit'));
		$this->load->view('div.form.php');
	}
	
	function delete() {
		$this->load->library('div');
		foreach ($this->input->post('id') as $k => $v) {
			$id[] = $v;
		}
		$this->div->delete($id);
		header('location:'.base_url().'admin/div/');
	}
	
	function add() {
		$this->load->library(array('div', 'ext'));
		$this->data->set_array('theme', $this->setting->get_theme());
		$this->ext->get(array('stack' => true, 'div' => 'div'));
		$this->div->clear();
		$this->setting->set_title($this->lang->line('system_div_add'));
		$this->load->view('div.form.php');
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
	
	function Div() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}