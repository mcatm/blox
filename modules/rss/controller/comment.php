<?

class Mod_Comment {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library(array('post', 'div'));
		
		$offset = ($CI->uri->segment(1)) ? $CI->uri->segment(1) : 0;
		
		$CI->post->get(array(
			'offset'		=> $offset,
			'uri_segment'	=> 1,
			'type'			=> 1,
			'get_parent'	=> true,
			'qty'			=> 15,
			'pager'			=> true
		));
		
		$CI->load->feed('post.php');
	}
	
	
	
	function Mod_Comment() {
		
	}
}

?>