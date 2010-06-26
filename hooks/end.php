<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

function this() {
	$CI =& get_instance();
	
	$CI->blox->action('e:'.substr($CI->uri->uri_string(), 1));
}