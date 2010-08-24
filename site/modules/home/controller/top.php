<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library('div');
		
		#$CI->div->get(array('where' => 'div_alias = "'.$CI->setting->get_alias().'@top"'));
		$offset = ($CI->uri->segment(1)) ? $CI->uri->segment(1) : 0;
		
		$CI->load->library('post');
		$CI->post->get(array(
			'offset'		=> $offset,
			'uri_segment'	=> 1,
			#'type'			=> 0,
			'user'			=> $CI->data->out['me']['id'],
			'pager'			=> true
		));
		
		$CI->mod->home->view(array(
			'tpl'	=> 'home/top.php'
		));
	}
	
	function post() {
		exit('UUUU');
	}
	
	function Mod_Top() {
		
	}
}

?>