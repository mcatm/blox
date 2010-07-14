<?

class EX_Controller {
	function EX_Controller() {
		
	}
}

class EX_Admin_Controller {
	
	function index() {
		$CI =& get_instance();
		$CI->setting->set_title('Store');
		$CI->load->view('_admin/top.php');
	}
	
	function EX_Admin_Controller() {
		
	}
}

class EX_Home_Controller {
	
	function index() {
		$CI =& get_instance();
	}
	
	function EX_Home_Controller() {
		
	}
}

?>