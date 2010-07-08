<?

function format_pi_special_tag($str, $param = array()) {
	
	$CI =& get_instance();
	
	preg_match('/{{img:(.*)}}?/', $str , $mt);
	#print_r($mt);exit;
	
	return $str;
}

?>