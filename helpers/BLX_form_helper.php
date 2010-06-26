<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * Form Value
 *
 * Grabs a value from the POST array for the specified field so you can
 * re-populate an input field or textarea.  If Form Validation
 * is active it retrieves the info from the validation class
 *
 * @access	public
 * @param	string
 * @return	mixed
 */
if ( ! function_exists('set_content_value')) {
	function set_content_value($field = '', $default = '') {//content[***]を$fieldで指定
		return (isset($_POST['content'][$field])) ? $_POST['content'][$field] : "";
		//return form_prep($OBJ->set_value($field, $default), $field);
	}
}

if ( ! function_exists('set_require_value')) {
	function set_require_value($field = '', $default = '') {//content[***]を$fieldで指定
		return (isset($_POST['require'][$field])) ? $_POST['require'][$field] : "";
		//return form_prep($OBJ->set_value($field, $default), $field);
	}
}

/* End of file form_helper.php */
/* Location: ./system/helpers/form_helper.php */