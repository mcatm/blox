<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library(array('post'));
		
		if (is_numeric($CI->uri->segment(2))) {//記事詳細
			$post_id = (int)$CI->uri->segment(2);
			
			$page = 0;//get page number
			if (isset($_GET['p']))		$page = $_GET['p'];
			if (isset($_GET['page']))	$page = $_GET['page'];
			
			$CI->post->get(array(
				'id'		=> $post_id,
				'related'	=> true,
				'comment'	=> true,
				'neighbor'	=> true,
				'pager'		=> false,
				'page'		=> $page,
				'qty'		=> 1
			));
			
			$CI->log->get_access('access/'.$CI->uri->segment(1).'/'.$CI->uri->segment(2));
			
			if (isset($CI->data->out['post'])) {
				$CI->mod->post->view(array(
					'type'				=> 'post',
					'title'				=> $CI->data->out['post'][0]['title'],
					'description'		=> format_description($CI->data->out['post'][0]['text'], 300),
					'keyword'			=> $CI->data->out['post'][0]['tag'],
					'flg_title_clear'	=> false,
					'tpl'				=> 'post.detail.php'
				));
			} else {
				show_404();
			}
		} else {//記事一覧
			$offset = ($CI->uri->segment(3)) ? (int)$CI->uri->segment(3) : 0;
			
			$CI->post->get(array(
				'base_url'	=> base_url().$CI->setting->get('url_alias_post').'/offset/',
				'type'		=> 0,
				'offset'	=> $offset
			));
								
			$CI->log->get_access();
			
			if ($CI->data->out['post']) {
				$CI->mod->post->view(array(
					'type'			=> 'post',
					'name'			=> $CI->setting->get('url_alias_post'),
					'title'			=> 'post',
					'flg_title_clear'	=> false,
					'description'	=> "",
					'keyword'		=> "",
					'tpl'			=> 'list'
				));
			} else {
				show_404();
			}
		}
	}
	
	function Mod_Top() {
		
	}
}

?>