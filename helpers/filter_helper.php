<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

if ( ! function_exists( 'set_filter' ) ) {
	function set_filter($path = "", $type = "", $param_1 = "", $param_2 = "") {
		if ($path != "") {
			$CI =& get_instance();
			$CI->load->library('session');
			
			$set = array(
				'filter' => array($path => array())
			);
			
			if (isset($CI->session->userdata['filter'][$path])) {
				foreach($CI->session->userdata['filter'][$path] as $k => $v) {
					$set['filter'][$path][$k] = $v;
				}
			}
			
			switch ($type) {
				case 'sort':
					$set['filter'][$path]['sort']	= $param_1;
					$set['filter'][$path]['order']	= $param_2;
				break;
				
				case 'where':
					$set['filter'][$path][$param_1]	= $param_2;
				break;
				
				case 'q':
					if ($CI->input->post('q')) {
						$set['filter'][$path]['query'] = $CI->input->post('q', true);
					} else {
						$set['filter'][$path]['query'] = "";
					}
				break;
				
				case 'clear':
					$set['filter'][$path] = array();
				break;
			}
			$CI->session->set_userdata($set);
		}
	}
}

if ( ! function_exists( 'get_filter' ) ) {
	function get_filter($path = "", $arr = array()) {
		if ($path != "") {
			$CI =& get_instance();
			$CI->load->library('session');
			
			if (isset($CI->session->userdata['filter'][$path])) {
				foreach($CI->session->userdata['filter'][$path] as $k => $v) {
					$arr[$k] = $v;
				}
			}
		}
		return $arr;
	}
}


?>
