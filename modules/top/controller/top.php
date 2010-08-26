<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library('div');
		
		$CI->div->get(array('where' => 'div_alias = "'.$CI->setting->get_alias().'@top"'));
		$offset = ($CI->uri->segment(1)) ? $CI->uri->segment(1) : 0;
		#print_r($CI->data->out['div']);exit;
		#if (empty($CI->data->out['div'])) {
			
		$CI->load->library('post');
		$CI->post->get(array(
			'offset'		=> $offset,
			'uri_segment'	=> 1,
			#'type'			=> 0,
			'get_parent'	=> true,
			'pager'			=> true
		));
			
		/*} else {
			#print_r($this->data->out['div']);
			$CI->data->out['div'][0]['content'][0]['param']['uri_segment'] = 1;
			$param = array(
				'title_clear'		=> true
			);
		}*/
		
		#$param['segment']['offset'] = $offset;
		#print_r($param);exit;
		$CI->mod->top->view(array(
			'tpl'	=> 'top.php'
		));
	}
	
	function Mod_Top() {
		
	}
}

?>