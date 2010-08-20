<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$uri = $CI->uri->uri_string();
		
		if ($uri == "") {
			$url = base_url();
		} else {
			$uri = str_replace(array('http://', 'https://'), '', $uri);
			$url = str_replace('/redirect', 'http:/', $uri);
		}
		
		$this->out($url);
	}
	
	function out($url = "") {
		$CI =& get_instance();
		
		if (empty($url)) header('location:'.base_url());
		
		// Redirect
		header('Content-type:text/html; charset=utf8');
		print '<html>';
		print '<head>';
		print '<title>Redirect to '.$url.'</title>';
		print '<meta http-equiv="refresh" CONTENT="0;URL='.$url.'">';
		print '</head>';
		print '<body>';
		#print '<a href="'.$url.'">'.$url.'</a>';
		print '</body>';
		print $CI->setting->get('code_google_analytics');
		print '</html>';
	}
	
	function Mod_Top() {
		
	}
}

?>