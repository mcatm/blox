<?

class Mod_top extends Controller {
	
	function index() {
		#print_r($this->uri->rsegments);
		$this->load->library('post');
		$this->post->get(array('qty' => 1));
		$this->load->view('top.php');
		
		exit('HELLO');
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
	}
	
	function pipe() {
		exit('HAHAHAHAHA');
	}
	
	function p() {
		exit('HKDALJL');
	}
	
	function _remap($method) {
		#exit($method);
	}
	
	function __construct() {
		parent::Controller();
	}
}

?>