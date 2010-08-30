<?

class Mod_User {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library(array('post', 'user'));
		
		$offset = ($CI->uri->segment(4)) ? $CI->uri->segment(4) : 0;
		
		if ($CI->uri->segment(3) && $CI->uri->segment(3) != 'offset') {//ユーザー詳細
			$account = $CI->uri->segment(3);
			
			$CI->user->get(array(
				'account'	=> $account
			));
			
			if (isset($CI->data->out['user'])) {
				
				$title = (!empty($CI->data->out['user'][0]['title'])) ? $CI->data->out['user'][0]['title'] : $CI->data->out['user'][0]['name'];
				
				$CI->post->get(array(
					'offset'		=> $offset,
					'uri_segment'	=> 1,
					#'type'			=> 0,
					'get_parent'	=> true,
					'base_url'		=> base_url().$CI->uri->segment(1).'/'.$account.'/',
					'user'			=> $CI->data->out['user'][0]['id'],
					'qty'			=> 15,
					'uri_segment'	=> 3,
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
				/*$CI->mod->rss->view(array(
					'flg_title_clear'	=> false,
					'title'		=> $title,
					'base_url'		=> base_url().$CI->uri->segment(1).'/'.$account.'/',
					'tpl'	=> 'top.php'
				));*/
				$CI->setting->set('title', $title);
				$CI->setting->set('description', format_description($CI->data->out['user'][0]['description'], 200));
				$CI->load->feed('post.php');
				exit;
			}
		}
		show_404();
	}
	
	function Mod_User() {
		
	}
}

?>