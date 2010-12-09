<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

if ( ! function_exists( 'base_url' ) ) {
	function base_url($ssl = false) {
		$CI =& get_instance();
		return ($ssl === true) ? $CI->config->slash_item('base_url_ssl') : $CI->config->slash_item('base_url');
	}
}

if ( ! function_exists( 'ex_url' ) ) {
	function ex_url($ssl = false) {
		$CI =& get_instance();
		return ($ssl === true || (defined('SSL_MODE') && SSL_MODE === true)) ? str_replace(BASE_URL, base_url(true), EX_URL) : EX_URL;
	}
}

if ( ! function_exists( 'theme_url' ) ) {
	function theme_url($ssl = false) {
		$CI =& get_instance();
		$url = THEME_URL.$CI->setting->get('theme').'/';
		return ($ssl === true || (defined('SSL_MODE') && SSL_MODE === true)) ? str_replace(BASE_URL, base_url(true), $url) : $url;
	}
}

if ( ! function_exists( 'self_url' ) ) {
	function self_url($url = "") {
		$CI =& get_instance();
		$delimiter = $CI->config->config['index_page'].'/';
		if ($url == "") $url = $_SERVER['PHP_SELF'];
		if ($_SERVER['QUERY_STRING'] != ""){
			$url .= (!defined('URI_PROTOCOL') || URI_PROTOCOL != 'QUERY_STRING') ? '?' : '';
			$url .= $_SERVER['QUERY_STRING'];
		}
		$url = base_url() . substr($url, strpos($url, $delimiter) + strlen($delimiter));
		return $url;
	}
}

if ( ! function_exists( 'extimg_url' ) ) {
	function extimg_url($url, $w = 0, $trim = "") {
		$CI =& get_instance();
		$expire_cache = 7 * 24 * 60 * 60;
		$time = time();
		
		$cache_dir = FILE_FOLDER.'/_cache/';
		$cache_url = FILE_URL.'_cache/';
		
		$pt = pathinfo($url);
		
		//cacheファイルネーム確定
		$filename = $pt['dirname'].'/'.$pt['filename'];
		$filename_org = md5($filename).'.'.$pt['extension'];
		if ($w > 0) $filename .= '_'.$w;
		if ($trim == 'trim' || $trim == 't') $filename .= '_trim';
		$filename = md5($filename);
		$filename .= '.'.$pt['extension'];
		
		if (!is_file($cache_dir.$filename)) {//キャッシュが存在する場合
			$CI->load->library('file');
			$CI->load->helper('file');
			
			$dirinfo = get_dir_file_info($cache_dir);
			
			if (!empty($dirinfo)) {//一週間以上前のファイルは削除
				foreach($dirinfo as $i) {
					if (($time - $i['date']) > $expire_cache) unlink($i['server_path']);
				}
			}
			
			$file = @file_get_contents($url);
			
			if (!empty($file)) {
				write_file($cache_dir.$filename_org, $file);//オリジナルファイル書き込み
				
				if ($filename != $filename_org) {//サムネイル作成
					$CI->load->library('image_lib');
					$CI->image_lib->make_thumb($cache_dir.$filename_org, $cache_dir.$filename, $w, $trim);
				}
			} else {
				return nopic($w, $trim);
			}
		}
		$url = $cache_url.$filename;
		return (defined('SSL_MODE') && SSL_MODE === true) ? str_replace(BASE_URL, base_url(true), $url) : $url;
	}
}

if ( ! function_exists( 'img_url' ) ) {
	function img_url($img_id, $w = 0, $trim = "", $h = 0) {
		if ($img_id > 0) {
			$CI =& get_instance();
			$CI->load->library('file');
			
			$CI->db->flush_cache();
			$CI->db->where('file_id', $img_id);
			$q = $CI->db->get(DB_TBL_FILE);
			$r = $q->result();
			
			if (!is_numeric($w)) $w = $CI->setting->get('img_size_'.$w);
			
			$filename = $img_id;
			if ($w > 0) $filename .= "_".(int)$w;
			if ($h > 0) $filename .= "x".$h;
			if (!empty($trim)) {
				if ($trim == 't') $trim = 'trim';
				$filename .= "_".$trim;
			}
			
			if (count($r) > 0) {
				$filename .= $r[0]->file_ext;
				$filename_org = $img_id.$r[0]->file_ext;
				
				$org_path_arr	= $CI->file->_make_path($img_id, FILE_FOLDER);
				$org_path = $org_path_arr['full'].$filename_org;
				
				$path	= $org_path_arr['full'].$filename;
				
				$url_arr	= $CI->file->_make_path($img_id, substr(FILE_URL, 0, -1));
				$url = $url_arr['full'].$filename;
			} else {
				return nopic($w, $trim);
			}
			
			if (!is_file($path)) {//サムネイルが無い場合は、作成する
				$CI->load->library('image_lib');
				$CI->image_lib->make_thumb($org_path, $path, $w, $trim, $h);
			}
			
			return (defined('SSL_MODE') && SSL_MODE === true) ? str_replace(BASE_URL, base_url(true), $url) : $url;
		}
	}
}

if ( ! function_exists( 'nopic' ) ) {
	function nopic($w = 0, $trim = "") {
		$CI =& get_instance();
		return extimg_url(FILE_URL.'_nopic/'.$CI->setting->get('img_notfound'), $w, $trim);
	}
}

if ( ! function_exists( 'get_uri_string' )) {
	function get_uri_string($url = "", $explode_param = true) {
		if ($url == "") {
			$CI =& get_instance();
			$url = $CI->uri->uri_string();
		}
		$u = explode('?', $url);
		if (!isset($u[1])) $u[1] = "";
		return array(
			'uri'	=> trim($u[0], '/'),
			'param'	=> ($explode_param == true) ? explode_param($u[1]) : $u[1]
		);
	}
}

if ( ! function_exists( 'explode_param' )) {
	function explode_param($str = "", $delimiter = "=") { 
		$arr = array();
		if (!empty($str)) {
			foreach(explode('&', $str) as $p) {
				$tmp_arr = explode($delimiter, $p);
				$arr[$tmp_arr[0]] = (isset($tmp_arr[1])) ? $tmp_arr[1] : true;
			}
		}
		return $arr;
	}
}

if ( ! function_exists( 'format_alias' ) ) {
	function format_alias($str) {
		$CI =& get_instance();
		$url_delimiter = $CI->setting->get('url_delimiter');
		
		$str = strtolower(mb_convert_kana($str, "a"));
		
		mb_regex_encoding('UTF-8');
		$m = mb_split("[^a-zA-Z0-9]", $str);
		
		foreach($m as $k=>$v) {
			if ($v != "") {
				if (!isset($token)) {
					$token = $v;
				} else {
					$token .= $url_delimiter.$v;
				}
			}
		}
		return (isset($token)) ? $token : '';
	}
}

?>
