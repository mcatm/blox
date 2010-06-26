<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Data {
	
	var $out = array();
	
	function set($obj, $user_param = array()) {//DBオブジェクトをプロパティに変換
		$param = array(//デフォルトの設定
			'label' => 'data',
			'stack' => true
		);
		
		$cfg = array_merge($param, $user_param);
		
		$decompress = array();
		if (!isset($cfg['ng'])) $cfg['ng'] = array('user_password', 'post_meta');//NGを設定
		
		if ($obj->num_rows() > 0) {
			$CI =& get_instance();
			if (isset($cfg['array'])) {
				$CI->load->helper('array');
				if (!is_array($cfg['array'])) {
					$decompress[] = $cfg['array'];
				} else {
					$decompress = $cfg['array'];
				}
			}
			foreach ($obj->result() as $k => $r) {
				foreach($r as $k2 => $v2) {
					 $label = (!isset($param['keep_prefix'])) ? preg_replace("(^.*?_)", "", $k2) : $k2;//prefixを削除（「xxxx_」の部分）
					if (!in_array($k2, $cfg['ng'])) {
						$out[$k][$label]	= (!in_array($label, $decompress)) ? $v2 : decompress_array($v2);
					}
				}
			}
			if ($cfg['stack'] === true) $this->out[$cfg['label']] = $out;//一時データの場合、溜め込まないように
			return $out;
		}
		return false;
	}
	
	function get($obj, $cfg = array('stack' => false)) {//DBオブジェクト変換の簡易メソッド
		return $this->set($obj, $cfg);
	}
	
	function set_array($label, $arr) {//配列をそのままプロパティに変換
		$this->out[$label] = $arr;
	}
	
	function add_array($arr = array()) {//配列をプロパティに追加（上書きしないように）
		$this->out = array_merge_recursive($this->out, $arr);
	}
	
	function Data() {
		
	}
}

?>