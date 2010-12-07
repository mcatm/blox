<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Loader extends CI_Loader {
	
	function BLX_Loader() {	
		parent::CI_Loader();
	}
		
	function view($view, $vars = array(), $return = FALSE) {
		$CI =& get_instance();
		
		$theme_folder = THEME_FOLDER.'/';
		
		/*if (!defined('ALREADY_BLOX_ACTIONED')) {//一回だけ、プラグイン動作
			$CI->blox->action('e:'.substr($CI->uri->uri_string(), 1));
			define('ALREADY_BLOX_ACTIONED', true);
		}*/
		
		$CI->load->library('data');
		if (count($vars) == 0) $vars = $CI->data->out;//Output自動呼び出し
		
		if (!defined('EXTENSION_CONTROLLER') || is_file($theme_folder.$CI->setting->get('theme').'/html/'.$view)) {
			$this->_ci_view_path	= $theme_folder;
			$theme_path				= $CI->setting->get('theme').'/html/'.$view;
		} else {//拡張コアの場合
			$this->_ci_view_path	= LIB_FOLDER.'/extension/';
			$theme_path				= EXTENSION_CONTROLLER.'/view/'.$view;
		}
		
		return $this->_ci_load(array(
			'_ci_view'		=> $theme_path,
			'_ci_vars'		=> $this->_ci_object_to_array($vars),
			'_ci_return'	=> $return
		));
	}
	
	function feed($view, $vars = array(), $return = FALSE) {
		$CI =& get_instance();
		$this->_ci_view_path = THEME_FOLDER.'/';
		
		if (count($vars) == 0) $vars = $CI->data->out;//Output自動呼び出し
		
		$theme_path = '_rss/'.$view;
		
		if (!is_file($this->_ci_view_path.$theme_path)) show_404();
		
		return $this->_ci_load(array(
			'_ci_view'		=> $theme_path,
			'_ci_vars'		=> $this->_ci_object_to_array($vars),
			'_ci_return'	=> $return
		));
	}
	
	function api($view, $format = 'xml', $vars = array(), $return = FALSE) {
		$CI =& get_instance();
		$this->_ci_view_path = THEME_FOLDER.'/';
		
		if (count($vars) == 0) $vars = $CI->data->out;//Output自動呼び出し
		
		$theme_path = '_api/'.$format.'/'.$view;
		
		#if (!is_file($this->_ci_view_path.$theme_path)) show_404();
		
		return $this->_ci_load(array(
			'_ci_view'		=> $theme_path,
			'_ci_vars'		=> $this->_ci_object_to_array($vars),
			'_ci_return'	=> $return
		));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Loader
	 *
	 * This function is used to load views and files.
	 * Variables are prefixed with _ci_ to avoid symbol collision with
	 * variables made available to view files
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _ci_load($_ci_data)
	{
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val)
		{
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}

		// Set the path to the requested file
		if ($_ci_path == '')
		{
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;
			$_ci_path = $this->_ci_view_path.$_ci_file;
		}
		else
		{
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}
		
		if ( ! file_exists($_ci_path))
		{
			show_error('Unable to load the requested file: '.$_ci_file);
		}
	
		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5
		
		if ($this->_ci_is_instance())
		{
			$_ci_CI =& get_instance();
			foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var)
			{
				if ( ! isset($this->$_ci_key))
				{
					$this->$_ci_key =& $_ci_CI->$_ci_key;
				}
			}
		}

		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->load_vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */	
		if (is_array($_ci_vars))
		{
			$this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
		}
		extract($this->_ci_cached_vars);
				
		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be
		 * post-processed by the output class.  Why do we
		 * need post processing?  For one thing, in order to
		 * show the elapsed page load time.  Unless we
		 * can intercept the content right before it's sent to
		 * the browser and then stop the timer it won't be accurate.
		 */
		ob_start();
				
		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.
		
		if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE)
		{
			echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))));
		}
		else
		{
			include($_ci_path); // include() vs include_once() allows for multiple views with the same name
		}
		
		log_message('debug', 'File loaded: '.$_ci_path);
		
		// Return the file data if requested
		if ($_ci_return === TRUE)
		{		
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 *
		 */	
		
		# ----------------
		#
		# blox : we change the logic from below codes for module works.
		# we add a variable named $ob_limit switched at request from modules or default controllers.
		#
		# ----------------
		
		$ob_limit = (defined('MOD_CONTROLLER')) ? $this->_ci_ob_level : $this->_ci_ob_level + 1;
		if (ob_get_level() > $ob_limit)
		{
			ob_end_flush();
		}
		else
		{
			// PHP 4 requires that we use a global
			global $OUT;
			$OUT->append_output(ob_get_contents());
			@ob_end_clean();
		}
	}
}

?>