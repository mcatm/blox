<?php

class Redirect extends Controller {

	function out($uri = "") {
		if ($uri == "") {
			$url = base_url();
		} else {
			$uri = str_replace(array('http://', 'https://'), '', $uri);
			$url = str_replace('/redirect', 'http:/', $uri);
		}
		
		header('Content-type:text/html; charset=utf8');
		print '<html>';
		print '<head>';
		print '<title>Redirect to '.$url.'</title>';
		print '<meta http-equiv="refresh" CONTENT="0;URL='.$url.'">';
		print '</head>';
		print '<body>';
		print '<a href="'.$url.'">'.$url.'</a>';
		print '</body>';
		print $this->setting->get('code_google_analytics');
		print '</html>';
	}
	
	function _remap($m) {
		switch($m) {
			case 'index':
			header('location:'.base_url());
			break;
			
			default:
			$this->out($this->uri->uri_string());
			break;
		}
	}
	
	function Redirect() {
		parent::Controller();
	}
}