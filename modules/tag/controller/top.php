<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library(array('post', 'tag'));
		
		if ($CI->uri->segment(2) && $CI->uri->segment(2) != 'offset') {//タグ詳細
			
			$tagstr = urldecode($CI->uri->segment(2));
			$tag = explode('+', $tagstr);
			$offset = ($CI->uri->segment(3)) ? $CI->uri->segment(3) : 0;
			
			$CI->tag->get_post($tag, array(
				'offset'	=> $offset
			));
			
			if (isset($CI->data->out['post'])) {
				$CI->mod->tag->view(array(
					'flg_title_clear'	=> false,
					'title'			=> $CI->lang->line('tag_result_title').$tagstr,
					'tpl'			=> $CI->setting->get('mod_tag_tpl')
				));
			} else {
				show_404();
			}
		} else {//タグ一覧
			header('location:'.base_url());
		}
	}
	
	function Mod_Top() {
		
	}
}

?>