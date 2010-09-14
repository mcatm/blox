<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function this() {
	$CI =& get_instance();
	if (defined('DEBUG_MODE') && DEBUG_MODE == true) {
		$CI->benchmark->mark('code_end');
		echo $CI->benchmark->elapsed_time('code_start', 'code_end');
	}
}