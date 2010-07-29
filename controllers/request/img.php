<?php

class Img extends Controller {
	
	var $msg;
	
	function get_img() {
		$param['id'] = (int)$this->uri->segment(3);
		
		
		
		print_r($param);exit;
		/*$this->load->library('file');
		
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
		$this->output->ajax($this->data->out['file'], $param);*/
	}
	
	function _remap() {
		$this->get_img();
	}
	
	function Img() {
		parent::Controller();
	}
}