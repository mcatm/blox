<?php
class Download extends Controller {

	function index($id) {//トップページ
		$hash = $this->uri->segment(3);
		
		$this->load->library('file');
		
		if ($this->file->download($id, $hash) === false) {
			show_404();
		}
	}
	
	function _remap($method) {//URLマッピング
		$this->index($method);
	}
	
	function Download() {
		parent::Controller();
		$this->index($this->uri->segment(2));
	}
}

function lower(&$str) {
	$str = strtolower($str);
}