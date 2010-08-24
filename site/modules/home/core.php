<?

class Home extends Module {
	
	function Home() {
		parent::Module();
		
		$CI =& get_instance();
		define('SSL_MODE', true);//SSLモード
		if (!$CI->session->userdata('login') || !$CI->auth->check_auth('home')) {
			if ($CI->uri->segment(1) != 'login') {
				$CI->data->out['redirect'] = self_url();
				print($CI->load->view('login.php', $CI->data->out, true));
				exit;
			}
		}
		if(defined('USE_SSL') && USE_SSL === true) {
			$CI->config->config['base_url'] = base_url(true);
			if ($_SERVER['SERVER_PORT'] != 443) header('location:'.str_replace(base_url(), base_url(true), self_url()));
		}
	}
}

?>