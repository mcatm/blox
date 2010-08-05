<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library('file');
		
		$id = $CI->uri->segment(2);
		$hash = $CI->uri->segment(3);
		
		if ($CI->file->download($id, $hash) === false || (!isset($id) || !isset($hash))) show_404();
	}
}

?>