<?

class Mod_post extends Controller {
	
	function index() {
		#$CI =& get_instance();
		$this->load->library(array('post', 'log'));
		
		if (is_numeric($this->uri->segment(2))) {//記事詳細
			$post_id = (int)$this->uri->segment(2);
			
			$page = 0;//get page number
			if (isset($_GET['p']))		$page = $_GET['p'];
			if (isset($_GET['page']))	$page = $_GET['page'];
			
			$this->post->get(array(
				'id'		=> $post_id,
				'related'	=> 6,
				'comment'	=> true,
				'neighbor'	=> true,
				'pager'		=> false,
				'page'		=> $page,
				'qty'		=> 1
			));
			
			$this->log->get_access('access/'.$this->uri->segment(1).'/'.$this->uri->segment(2));
			
			if (isset($this->data->out['post'])) {
				/*$this->module->view(array(
					'type'				=> 'post',
					'title'				=> $this->data->out['post'][0]['title'],
					'description'		=> format_description($this->data->out['post'][0]['text'], 300),
					'keyword'			=> $this->data->out['post'][0]['tag'],
					'flg_title_clear'	=> false,
					'tpl'				=> 'post.detail.php'
				));*/
				$this->load->view('post.detail.php');
			} else {
				show_404();
			}
		} else {//記事一覧
			$offset = ($this->uri->segment(3)) ? (int)$this->uri->segment(3) : 0;
			
			$this->post->get(array(
				'base_url'	=> base_url().$this->setting->get('url_alias_post').'/offset/',
				'type'		=> 0,
				'offset'	=> $offset
			));
								
			$this->log->get_access();
			
			if ($this->data->out['post']) {
				/*$this->module->view(array(
					'type'			=> 'post',
					'name'			=> $this->setting->get('url_alias_post'),
					'title'			=> 'post',
					'flg_title_clear'	=> false,
					'description'	=> "",
					'keyword'		=> "",
					'tpl'			=> 'list'
				));*/
				$this->load->view('list.php');
			} else {
				show_404();
			}
			print_r($this->data->out['post']);
		}
	}
	
	function __construct() {
		parent::Controller();
	}
}

?>