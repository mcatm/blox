<?php

class Img extends Controller {
	
	var $msg;
	
	function get_img() {
		
		//paramator
		$param['id']		= (int)$this->uri->segment(3);
		$param['width']		= $this->uri->segment(4);
		$param['trim']		= $this->uri->segment(5);
		$param['height']	= $this->uri->segment(6);
		
		if ($param['id'] > 0) {
			$this->load->library('file');
		}
		
		
		print_r($param);exit;
		
		/*$this->data->set_array('file_segment', explode('|', $this->setting->get('file_segment')));
		
		$param = array(
			'stack'			=> true,
			'theme'			=> $this->input->post('theme'),
			'tpl'			=> $this->input->post('tpl'),
			'datatype'		=> $this->input->post('type')
		);
		
		if ($this->input->post('qty'))		$param['qty']		= $this->input->post('qty');
		if ($this->input->post('offset'))	$param['offset']	= $this->input->post('offset');
		if ($this->input->post('post_id'))	$param['post_id']	= $this->input->post('post_id');
		if ($this->input->post('user_id'))	$param['user_id']	= $this->input->post('user_id');
		
		$this->file->get($param);
		
		if (!isset($this->data->out['file'])) $this->data->out['file'] = array();
		$this->output->ajax($this->data->out['file'], $param);*/
		
		
		
		
		/*img_url($img_id, $w = 0, $trim = "", $h = 0) {
		if ($img_id > 0) {
			$CI =& get_instance();
			$CI->load->library('file');
			
			$CI->db->flush_cache();
			$CI->db->where('file_id', $img_id);
			$q = $CI->db->get(DB_TBL_FILE);
			$r = $q->result();
			
			$filename = $img_id;
			if ($w > 0) $filename .= "_".(int)$w;
			if ($h > 0) $filename .= "x".$h;
			if (!empty($trim)) {
				if ($trim == 't') $trim = 'trim';
				$filename .= "_".$trim;
			}
			
			if (count($r) > 0) {
				$filename .= $r[0]->file_ext;
				$filename_org = $img_id.$r[0]->file_ext;
				
				$org_path_arr	= $CI->file->_make_path($img_id, FILE_FOLDER);
				$org_path = $org_path_arr['full'].$filename_org;
				
				$path	= $org_path_arr['full'].$filename;
				
				$url_arr	= $CI->file->_make_path($img_id, substr(FILE_URL, 0, -1));
				$url = $url_arr['full'].$filename;
			} else {
				return nopic($w, $trim);
			}
			
			if (!is_file($path)) {//サムネイルが無い場合は、作成する
				$CI->load->library('image_lib');
				$CI->image_lib->make_thumb($org_path, $path, $w, $trim, $h);
			}
			
			return (defined('SSL_MODE') && SSL_MODE === true) ? str_replace(BASE_URL, base_url(true), $url) : $url;
		}*/
		
		
	}
	
	function _remap() {
		$this->get_img();
	}
	
	function Img() {
		parent::Controller();
	}
}