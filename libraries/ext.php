<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Ext {
	
	function get($user_param = array()) {
		$CI =& get_instance();
		
		$param = array(
			'label' => 'ext',
			'stack' => false
		);
		
		$param = array_merge($param, $user_param);
		
		if (isset($param['div'])) $CI->db->where('ext_div', $param['div']);
		$CI->db->order_by('ext_order', 'asc');
		return $CI->data->set($CI->db->get(DB_TBL_EXT), array('stack' => $param['stack'], 'label' => $param['label']));
	}
	
	function BLX_Ext() {
		
	}
}

?>