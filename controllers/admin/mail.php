<?php

class Mail extends Controller {

	function index($offset = 0) {
		$this->load->library('mail');
		$this->load->helper('filter');
		
		$where = array(
			'offset'	=> $offset,
			'uri_segment' => 4,
			'base_url'	=> base_url().'admin/mail/offset/'
		);
		$where = get_filter('admin/mail', $where);
		
		$this->mail->get($where);
		
		if (isset($this->data->out['mail'])) {
			foreach($this->data->out['mail'] as $k => $v) {
				$arr = explode('/', $v['path']);
				$this->data->out['mail'][$k]['div'] = $arr[1];
				$this->data->out['mail'][$k]['target'] = $arr[2];
			}
		}
		
		$this->setting->set_title($this->lang->line('system_mail_list'));
		$this->load->view('mail.list.php');
	}
	
	function detail($id) {
		$this->load->library('mail');
		
		$this->mail->get(array(
			'id'	=> $id
		));
		
		if ($this->data->out['mail'][0]['status_a']) $this->setting->set_title($this->data->out['mail'][0]['status_a']);
		$this->load->view('mail.detail.php');
	}
	
	function filter($type = "", $param_1 = "", $param_2 = "") {
		$this->load->helper('filter');
		set_filter('admin/mail', $type, $param_1, $param_2);
		$this->index();
	}
	
	function delete() {
		foreach ($this->input->post('id') as $k => $v) {
			$id[] = $v;
		}
		$this->log->delete($id);
		header('location:'.base_url().'admin/mail/');
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
			
			case 'delete':
				$this->delete();
			break;
			
			case 'detail':
				$this->detail($this->uri->segment(4));
			break;
		}
	}
	
	function Mail() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}