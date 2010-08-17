<?php

class Theme extends Controller {
	
	var $out = array();
	
	function index($dir = "") {
		$this->load->helper('directory');
		$tpl = directory_map(THEME_FOLDER);
		$map = array();
		
		$base_segment = array();
		for($i=3;$this->uri->segment($i);$i++) $base_segment[] = $this->uri->segment($i);
		#print_r($base_segment);exit;
		
		$this->data->out['page']['base_segment'] = implode($base_segment, '/');
		if (!empty($this->data->out['page']['base_segment'])) $this->data->out['page']['base_segment'] .= "/";
		#exit($this->data->out['page']['base_segment']);
		
		$out = $this->_get_tpl($tpl, array());
		
		#print_r($out);
		
		foreach ($base_segment as $bs) $out = $out[$bs];
		$this->data->out['tpl'] = $out;
		#print_r($out);exit;
		/*if (empty($dir)) {
			$this->data->out['tpl'] = $out;
		} else {
			if (empty($subdir)) {
				$this->data->out['tpl'] = $out[$dir];
			} else {
				$this->data->out['tpl']	= $out[$dir][$subdir];
			}
		}*/
		
		/*if (empty($dir)) {
			
		} else {
			$path = THEME_FOLDER.'/'.$dir;
			if (!is_file($path)) {
				$p = explode('/', $dir);
				foreach ($p as $tm) $tpl = $tpl[$tm];
				foreach ($tpl as $tmp_k => $tmp) {
					$u = (is_array($tmp)) ? $tmp_k : $tmp;
					
					#print '<a href="'.base_url().'admin/theme/'.$dir.'/'.$u.'">'.$u.'<br />';
				}
			} else {
				print '<textarea style="width:900px;height:500px">'.file_get_contents($path).'</textarea>';
			}
		}*/
		$this->load->view('theme.list.php');
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