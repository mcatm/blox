<?

class Mod_Top {
	
	function index() {
		$CI =& get_instance();
		$CI->load->library(array('post', 'user'));
		
		$offset = ($CI->uri->segment(3)) ? $CI->uri->segment(3) : 0;
		
		if ($CI->uri->segment(2) && $CI->uri->segment(2) != 'offset') {//ユーザー詳細
			$CI->user->get(array(
				'account'	=> $CI->uri->segment(2)
			));
			
			if (isset($CI->data->out['user'])) {
				$CI->post->get(array(
					'user'		=> $CI->data->out['user'][0]['id'],
					'pager'		=> true,
					'base_url'	=> base_url().'/'.$CI->uri->segment(1).'/'.$CI->uri->segment(2).'/',
					'offset'	=> $offset
				));
				print_r($CI->data->out['post']);
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