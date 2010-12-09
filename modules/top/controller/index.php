<?

class Mod_top extends Controller {
	
	function index() {
		#$this->load->library('post');
		$this->blox->get(array('qty' => 5, 'offset' => $this->uri->segment(1)));
		$this->setting->set_title('', true);
		$this->load->view('top.php');
		/*$CI =& get_instance();
		$CI->load->library(array('post', 'div'));
		
		$offset = ($CI->uri->segment(1)) ? $CI->uri->segment(1) : 0;
		
		$CI->post->get(array(
			'offset'		=> $offset,
			'uri_segment'	=> 1,
			#'type'			=> 0,
			'get_parent'	=> true,
			'pager'			=> true
		));
		
		$CI->mod->top->view(array(
			'tpl'	=> 'top.php'
		));*/
		exit;
	}
	
	function __construct() {
		parent::Controller();
	}
}

?>