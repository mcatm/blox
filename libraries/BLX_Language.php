<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2009, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Language Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Language
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 */
class BLX_Language extends CI_Language {

	function load($langfile = '', $idiom = '', $return = FALSE) {
		$langfile = str_replace(EXT, '', str_replace('_lang.', '', $langfile)).'_lang'.EXT;

		if (in_array($langfile, $this->is_loaded, TRUE)) {
			return;
		}

		if ($idiom == '') {
			$CI =& get_instance();
			$deft_lang = $CI->config->item('language');
			$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
		}
		
		// Determine where the language file is and load it
		if (file_exists($CI->config->item('mod_language_path'))) {
			include($CI->config->item('mod_language_path'));
		} else if (file_exists($CI->config->item('language_path').'/'.$idiom.'/'.$langfile)) {
			include($CI->config->item('language_path').'/'.$idiom.'/'.$langfile);
		} else if (file_exists(APPPATH.'language/'.$idiom.'/'.$langfile)) {
			include(APPPATH.'language/'.$idiom.'/'.$langfile);
		} else {
			if (file_exists(BASEPATH.'language/'.$idiom.'/'.$langfile)) {
				include(BASEPATH.'language/'.$idiom.'/'.$langfile);
			} else {
				show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
			}
		}

		if (!isset($lang)) {
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			return;
		}

		if ($return == TRUE)
		{
			return $lang;
		}

		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);

		log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}
}
// END Language Class

/* End of file Language.php */
/* Location: ./system/libraries/Language.php */