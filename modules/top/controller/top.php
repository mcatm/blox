<?

class Mod_Top {
	
	function index() {
		exit('HELLO');
		/*$CI =& get_instance();
		$CI->load->library(array('post', 'div'));
		
		$offset = ($CI->uri->segment(1)) ? $CI->uri->segment(1) : 0;
		
		$CI->post->get(array(
			'offset'		=> $offset,
			'uri_segment'	=> 1,
			#'type'			=> 0,
			'get_parent'	=> true,
			'pager'			=> true
		));
		
		$CI->mod->top->view(array(
			'tpl'	=> 'top.php'
		));*/
	}
	
	function Mod_Top() {
		
	}
}

?>