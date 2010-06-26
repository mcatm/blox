<?php

class Site extends Controller {

	
	
	function _remap($m) {
		switch ($m) {
			case 'index':
			case 'offset':
				$this->index($this->uri->segment(4));
			break;
			
			case 'filter':
				$this->filter($this->uri->segment(4), $this->uri->segment(5), $this->uri->segment(6), $this->uri->segment(7));
			break;
			
			case 'img':
			case 'image':
				$this->image($this->uri->segment(4));
			break;
			
			case 'snd':
			case 'sound':
				$this->sound($this->uri->segment(4));
			break;
			
			case 'edit':
				//$this->edit($this->uri->segment(4));
			break;
			
			case 'delete':
				$this->delete();
			break;
			
			case 'new':
			case 'add':
			case 'upload':
				$this->add();
			break;
			
			default:
				$this->detail($m);
			break;
		}
	}
	
	function Site() {
		parent::Controller();
		define('ADMIN_MODE', true);//管理画面モード
	}
}