<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

if ( ! function_exists( 'compress_array' ) ) {
	function compress_array($data = array()) {
		if (is_array($data)) {
			foreach ($data as $key => $val) {
				$data[$key] = str_replace('\\', '{{slash}}', $val);
			}
		} else {
			$data = str_replace('\\', '{{slash}}', $data);
		}
		return serialize($data);
	}
}

if ( ! function_exists( 'decompress_array' ) ) {
	function decompress_array($data = "") {
		if ($data != "") {
			if (function_exists("strip_slashes")) $data = strip_slashes($data);
			$data = @unserialize($data);
			if (is_array($data)) {
				foreach ($data as $key => $val) {
					$data[$key] = str_replace('{{slash}}', '\\', $val);
				}
				return $data;
			}
			return str_replace('{{slash}}', '\\', $data);
		}
	}
}

?>
