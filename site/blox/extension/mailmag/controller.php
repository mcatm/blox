<?

class EX_Controller {
	function EX_Controller() {
		
	}
}

class EX_Admin_Controller {
	
	function index() {
		$CI =& get_instance();
		#$CI->load->library(array('encrypt'));
		#$CI->load->helper('array');
		
		#$CI->load->library('mail');
		$CI->load->helper('filter');
		
		$offset = ($CI->uri->segment(4)) ? $CI->uri->segment(4) : 0;
		
		$where = array(
			'offset'	=> $offset,
			'uri_segment' => 4,
			'base_url'	=> base_url().'admin/ex/mailmag/offset/'
		);
		#$where = get_filter('admin/mailmag', $where);
		
		$CI->extension->mailmag->get($where);
		
		$CI->setting->set_title('Mail Magazine');
		$CI->load->view('mailmag.list.php');
		
		#
		#exit();//, $CI->encrypt->encode(compress_array($pc)));
		
		#$CI->setting->set_title('Mail Magazine');
		#$CI->load->view('mailmag.list.php');
	}
	
	function edit() {
		$CI =& get_instance();
		#exit($CI->uri->segment(5));
		$where = array(
			'id'	=> $CI->uri->segment(5)
		);
		#$where = get_filter('admin/mailmag', $where);
		
		$CI->extension->mailmag->get($where);
		#print_r($CI->data->out['mailmag']);
		
		$CI->extension->mailmag->set();
		
		$CI->load->view('mailmag.form.php');
	}
	
	function add() {
		$CI =& get_instance();
		
		$CI->extension->mailmag->set();
		
		#$mobile = decompress_array($CI->encrypt->decode($CI->setting->get('extension_mailmag_maillist_mobile')));
		#print_r($mobile);
		
		$CI->load->view('mailmag.form.php');
	}
	
	function send() {
		$CI =& get_instance();
		
		$id = $CI->uri->segment(5);
		$type = $CI->uri->segment(6);
		
		$CI->extension->mailmag->send($id, $type);
		
		header('location:'.base_url().'admin/ex/mailmag/');
	}
	
	function user() {
		$CI =& get_instance();
		$type = ($CI->uri->segment(5)) ? $CI->uri->segment(5) : 'all';
		
		$CI->data->out['maillist'] = $CI->extension->mailmag->get_user($type);
		
		$CI->load->view('mailmag.user.php');
		var_dump($CI->data->out['maillist']);
		exit();
	}
	
	function sync() {
		$CI =& get_instance();
		$CI->load->library(array('mail', 'encrypt'));
		$CI->load->helper('array');
		
		$csv = @file_get_contents($CI->setting->get('extension_mailmag_path_csv'));
		$maillist = explode(',', str_replace(array("\n","\r"), '', $csv));
		
		$pc = array();
		$mobile = array();
		
		#print count($maillist).'<hr />';
		foreach ($maillist as $k => $v) {
			$address = trim($v);
			if ($CI->mail->check_mobile($address)) {
				$mobile[] = $address;
			} else {
				$pc[] = $address;
			}
		}
		
		$CI->setting->store('extension_mailmag_maillist_pc', $CI->encrypt->encode(compress_array($pc)));
		$CI->setting->store('extension_mailmag_maillist_mobile', $CI->encrypt->encode(compress_array($mobile)));
		
		#print 'PC<br />';
		#print_r($pc);
		#print '<hr />MOBILE<br />';
		#print_r($mobile);
		#print_r($maillist);exit;
		#exit($CI->setting->get('extension_mailmag_path_csv'));
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