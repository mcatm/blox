<?

function format_pi_special_tag($str, $param = array()) {
	
	$CI =& get_instance();
	
	$regex_open = '/'.$CI->setting->get('format_tag_open');
	$regex_close = $CI->setting->get('format_tag_close').'?/';
	
	preg_match($regex_open.'img:(.*)'.$regex_close, $str , $mt);
	#print_r($mt);exit;
	
	return $str;
}

?>