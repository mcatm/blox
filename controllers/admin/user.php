<?php

class User extends Controller {

	function index($offset = 0) {
		$this->load->library('user');
		$this->load->helper('filter');
		$where = array(
			'qty'			=> 20,
			'offset'		=> $offset,
			'uri_segment'	=> 4,
			'base_url'		=> base_url().'admin/user/offset/'
		);
		$where = get_filter('admin/user', $where);
		$this->user->get($where);
		$this->setting->set_title($this->lang->line('system_label_user_list'));
		$this->load->view('user.list.php');
	}
	
	function detail($id) {
		$this->load->library(array('post', 'user'));
		
		$this->user->get(array('id' => $id));
		
		$where = array();
		$where['user'] = $id;
		$where['file_main']	= true;
		if (isset($this->data->out['me']['auth']['view_draft'])) $where['auth'] = 10;//下書き閲覧権限
		$this->post->get($where);
		$this->load->view('user.detail.php');
	}
	
	function filter($type = "", $param_1 = "", $param_2 = "") {
		$this->load->helper('filter');
		set_filter('admin/user', $type, $param_1, $param_2);
		$this->index();
	}
	
	function edit($id = 0) {
		$this->load->library('user');
		
		$msg = $this->user->set($id);
		
		switch ($msg['result']) {
			case 'success':
				$this->user->get(array('id' => $msg['id'], 'qty' => 1, 'file' => true));
			break;
			
			case 'error':
			default:
				if ($id > 0) $this->user->get(array('id' => $id, 'qty' => 1, 'file' => true));
			break;
		}
		$this->setting->set_title($this->lang->line('system_user_edit'));
		$this->load->view('user.form.php');
	}
	
	function invite() {
		$this->load->library('user');
		
		$msg = $this->user->invite();
		
		switch ($msg['result']) {
			case 'success':
				#$this->user->get(array('id' => $msg['id'], 'qty' => 1, 'file' => true));
			break;
			
			case 'error':
			default:
				#if ($id > 0) $this->user->get(array('id' => $id, 'qty' => 1, 'file' => true));
			break;
		}
		$this->setting->set_title($this->lang->line('system_user_invite'));
		$this->load->view('user.invite.php');
	}
	
	function change_password($id = 0) {
		$this->load->library('user');
		$msg = $this->user->set_password($id);
		
		switch ($msg['result']) {
			case 'success':
				$this->user->get(array('id' => $msg['id'], 'qty' => 1, 'file' => true));
			break;
			
			case 'error':
			default:
				if ($id > 0) $this->user->get(array('id' => $id, 'qty' => 1, 'file' => true));
			break;
		}
		$this->setting->set_title($this->lang->line('system_user_edit').' '.$this->lang->line('system_label_user'));
		$this->load->view('user.form.php');
	}
	
	function add() {
		$this->load->library('user');
		$this->user->clear();
		$this->setting->set_title($this->lang->line('system_user_add'));
		$this->load->view('user.form.php');
	}
	
	//コールバック関数は、コントローラー内に記述する
	function check_account($str) {
		return $this->user->_check_unique('user_account', $str);
	}
	
	function check_email($str) {
		return $this->user->_check_unique('user_email', $str);
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
			
			case 'invite':
				$this->invite();
			break;
			
			case 'password':
				$this->change_password($this->uri->segment(4));
			break;
			
			case 'new':
			case 'add':
				$this->add();
			break;
			
			case 'detail';
				$this->detail($this->uri->segment(4));
			break;
			
			default:
				show_404();
			break;
		}
	}
	
	function User() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}