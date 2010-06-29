<?php

class Post extends Controller {

	function index($offset = 0) {
		$this->load->library('post');
		$this->load->helper('filter');
		$where = array(
			'qty'			=> 20,
			'offset'		=> $offset,
			'user' => $this->data->out['me']['id'],
			'base_url'	=> base_url().$this->setting->get('url_alias_home').'/post/offset/',
			'uri_segment'	=> 4,
			'type'			=> 0
		);
		if ($this->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 9;
		$where = get_filter('home/post', $where);
		$this->post->get($where);
		$this->setting->set_title($this->setting->get('url_alias_home').' - my '.$this->setting->get('url_alias_post'));
		$this->load->view('post.list.php');
	}
	
	function filter($type = "", $param_1 = "", $param_2 = "") {
		$this->load->helper('filter');
		set_filter('home/post', $type, $param_1, $param_2);
		$this->index();
	}
	
	function edit($id = 0) {
		$this->load->library(array('post', 'div', 'user'));
		$this->div->get(array('type' => 'section', 'label' => 'section'));
		$this->div->get(array('type' => 'category', 'label' => 'category'));
		$this->user->get(array('qty' => 0, 'auth' => 'usertype_auth_post'));
		
		$msg = $this->post->set($id);
		switch ($msg['result']) {
			case 'success':
				$this->post->get(array('id' => $msg['id'], 'qty' => 1, 'file' => true, 'schedule' => true,));
			break;
			
			case 'error':
			default:
				$where = array('id' => $id, 'qty' => 1, 'file' => true, 'schedule' => true);
				if ($this->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 10;
				if ($id > 0) $this->post->get($where);
			break;
		}
		$this->setting->set_title($this->lang->line('system_post_edit').' '.$this->lang->line('system_label_post'));
		$this->load->view('post.form.php');
	}
	
	function add() {
		$this->load->library(array('post', 'user', 'ext'));
		$this->div->get(array('type' => 'section', 'label' => 'section'));
		$this->div->get(array('type' => 'category', 'label' => 'category'));
		$this->user->get(array('qty' => 0, 'auth' => 'usertype_auth_post'));
		$this->ext->get(array('stack' => true, 'div' => 'post'));
		$this->post->clear();
		$this->setting->set_title($this->lang->line('system_post_add').' '.$this->lang->line('system_label_post'));
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
		define('HOME_MODE', true);//ホーム画面モード
	}
}