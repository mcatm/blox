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
					'title'		=> $tagstr,
					'tpl'	=> 'top.php'
				));
			} else {
				show_404();
			}
		} else {//ユーザー一覧
			$CI->user->get(array(
				'offset'			=> $offset,
				'base_url'			=> base_url().'/'.$CI->uri->segment(1).'/',
				'sort'				=> 'actiondate',
				'order'				=> 'desc',
				'reject_tmp_user'	=> true,
				'pager'				=> true
			));
			print_r($CI->data->out['user']);
		}
	}
	
	function Mod_Top() {
		
	}
}

?>