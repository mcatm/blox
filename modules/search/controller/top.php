<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library(array('post'));
		
		$query = $CI->input->post('query');
		
		if (!$CI->uri->segment(2) && $query) {
			header('location:'.base_url().'search/'.urlencode($query).'/');
		} else {
			$query = urldecode($CI->uri->segment(2));
		}
		
		$offset = ($CI->uri->segment(3)) ? $CI->uri->segment(3) : 0;
		
		$CI->post->get(array(
			'query'			=> $query,
			'base_url'		=> base_url().'search/'.$CI->uri->segment(2).'/',
			'offset'		=> $offset,
			'uri_segment'	=> 3,
			'pager'			=> true
		));
		
		$CI->mod->search->view(array(
			'title'				=> $CI->lang->line('search_result_title').$query,
			'flg_title_clear'	=> false,
			'tpl'				=> $CI->setting->get('mod_search_tpl')
		));
	}
	
	function Mod_Top() {
		
	}
}

?>