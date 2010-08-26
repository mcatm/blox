<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library(array('post', 'user'));
		
		$offset = ($CI->uri->segment(3)) ? $CI->uri->segment(3) : 0;
		
		if ($CI->uri->segment(2) && $CI->uri->segment(2) != 'offset') {//ユーザー詳細
			$CI =& get_instance();
			
			$account = $CI->uri->segment(2);
			
			$CI->user->get(array(
				'account'	=> $account
			));
			
			if (isset($CI->data->out['user'])) {
				
				$offset = ($CI->uri->segment(3)) ? $CI->uri->segment(3) : 0;
				$title = (!empty($CI->data->out['user'][0]['title'])) ? $CI->data->out['user'][0]['title'] : $CI->data->out['user'][0]['name'];
				
				$CI->post->get(array(
					'offset'		=> $offset,
					'uri_segment'	=> 1,
					#'type'			=> 0,
					'get_parent'	=> true,
					'user'			=> $CI->data->out['user'][0]['id'],
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
				$CI->mod->user->view(array(
					'flg_title_clear'	=> false,
					'title'		=> $title,
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