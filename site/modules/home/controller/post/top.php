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
	
	function edit() {
		$CI =& get_instance();
		$id = $CI->uri->segment(4);
		
		$CI->load->library(array('post', 'div', 'user'));
		$CI->div->get(array('type' => 'section', 'label' => 'section'));
		$CI->div->get(array('type' => 'category', 'label' => 'category'));
		$CI->user->get(array('qty' => 0, 'auth' => array('contributor', 'admin')));
		
		$msg = $CI->post->set($id);
		
		switch ($msg['result']) {
			case 'success':
				$where = array('id' => $msg['id'], 'qty' => 1, 'file' => true, 'schedule' => true);
				if ($CI->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 10;
				$CI->post->get($where);
			break;
			
			case 'error':
			default:
				$where = array('id' => $id, 'qty' => 1, 'file' => true, 'schedule' => true);
				if ($CI->data->out['me']['auth']['type'] == 'admin') $where['auth'] = 10;
				if ($id > 0) $CI->post->get($where);
			break;
		}
		$CI->setting->set_title($CI->lang->line('system_post_edit'));
		
		$CI->mod->home->view(array(
			'tpl'	=> 'home/post.form.php'
		));
	}
	
	function add() {
		$CI =& get_instance();
		
		//bookmarklet
		if($_GET) {
			$set = array();
			foreach ($_GET as $gk => $gv) {
				$set[$gk] = $gv;
			}
			$CI->data->set_array('post', array($set));
		}
		
		$CI->load->library(array('post', 'user', 'ext'));
		$CI->div->get(array('type' => 'section', 'label' => 'section'));
		$CI->div->get(array('type' => 'category', 'label' => 'category'));
		$CI->user->get(array('qty' => 0, 'auth' => array('contributor', 'admin')));
		$CI->ext->get(array('stack' => true, 'div' => 'post'));
		$CI->post->clear();
		$CI->setting->set_title($CI->lang->line('system_post_add'));
		
		$CI->mod->home->view(array(
			'tpl'	=> 'home/post.form.php'
		));
	}
	
	function Mod_Top() {
		
	}
}

?>