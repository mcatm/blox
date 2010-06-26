<?php

class Theme extends Controller {

	function index($dir = "") {
		$this->load->helper('directory');
		$tpl = directory_map(THEME_FOLDER);
		$map = array();
		
		if (empty($dir)) {
			foreach($tpl as $k => $v) {
				if (is_array($v) && !preg_match('(^_(.*))', $k)) {
					print '<h1><a href="'.base_url().'admin/theme/'.$k.'/">'.$k.'</a></h1>';
					print_r($v);
					print '<hr />';
				}
			}
		} else {
			$path = THEME_FOLDER.'/'.$dir;
			if (!is_file($path)) {
				$p = explode('/', $dir);
				foreach ($p as $tm) $tpl = $tpl[$tm];
				foreach ($tpl as $tmp_k => $tmp) {
					$u = (is_array($tmp)) ? $tmp_k : $tmp;
					print '<a href="'.base_url().'admin/theme/'.$dir.'/'.$u.'">'.$u.'<br />';
				}
			} else {
				print '<textarea style="width:900px;height:500px">'.file_get_contents($path).'</textarea>';
			}
		}
	}
	
	function _remap($m) {
		switch ($m) {
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