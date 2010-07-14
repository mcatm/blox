<?

class EX_Controller {
	function EX_Controller() {
		
	}
}

class EX_Admin_Controller {
	
	function index() {
		$CI =& get_instance();
		$CI->setting->set_title('Mail Magazine');
		$CI->load->view('mailmag.list.php');
	}
	
	function edit() {
		
	}
	
	function add() {
		
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