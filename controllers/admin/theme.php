<?php

class Theme extends Controller {
	
	var $out = array();
	
	function index($dir = "") {
		$this->load->helper('directory');
		$tpl = directory_map(THEME_FOLDER);
		$map = array();
		
		$base_segment = array();
		for($i=3;$this->uri->segment($i);$i++) $base_segment[] = $this->uri->segment($i);
		
		$this->data->out['page']['base_segment'] = implode($base_segment, '/');
		if (!empty($this->data->out['page']['base_segment'])) $this->data->out['page']['base_segment'] .= "/";
		
		$out = $this->_get_tpl($tpl, array());
		
		foreach ($base_segment as $bs) $out = $out[$bs];
		$this->data->out['tpl'] = $out;
		
		$this->load->view('theme.list.php');
	}
	
	function edit() {
		$this->data->out['tpl'] = $this->_get_tpl_file();
		
		$this->load->view('theme.form.php');
	}
	
	private function _get_tpl_file() {
		$this->load->helper('file');
		
		$path = "";
		for ($i=1;$this->uri->segment($i);$i++) {
			if ($i > 3) $path .= '/'.$this->uri->segment($i);
		}
		
		return array(
			'name'	=> $this->uri->segment($i-1),
			'body'	=> htmlspecialchars(read_file(THEME_FOLDER.$path))
		);
	}
	
	private function _get_tpl($tpl = array(), $out = array(), $is_theme = true) {
		foreach($tpl as $k => $v) {
			if (!preg_match('(^_(.*))', $k) || $is_theme === false) {
				if (is_array($v)) {
					$out[$k] = $this->_get_tpl($v, $out, false);
				} else {
					$out[] = $v;
				}
			}
		}
		return $out;
	}
	
	function _remap($m) {
		switch ($m) {
			case 'edit':
				$this->edit();
			break;
			
			case 'index':
			case 'offset':
			default:
				$this->index(trim(str_replace('/admin/theme', '', $this->uri->uri_string()), '/'));
			break;
		}
	}
	
	function Theme() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}