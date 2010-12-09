<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Output extends CI_Output {
	
	var $dat = array();
	
	function __construct() {
		parent::CI_Output();
	}
	
	function set($obj, $user_param = array()) {//DBオブジェクトをプロパティに変換
		$param = array(//デフォルトの設定
			'label' => 'blox',
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
					#print $k2.' : '.$v2.'<br />';
					if (!in_array($k2, $cfg['ng'])) {
						$out[$k][$label]	= (!in_array($label, $decompress)) ? $v2 : decompress_array($v2);
					}
				}
			}
			if ($cfg['stack'] === true) $this->dat[$cfg['label']] = $out;//一時データの場合、溜め込まないように
			return $out;
		}
		return false;
	}
	
	function set_cache($path, $output, $expire = 60) {//キャッシング
		$CI =& get_instance();
		$cache_path = ($CI->config->item('cache_path') == '') ? BASEPATH.'cache/' : $CI->config->item('cache_path');
		
		if (!is_dir($cache_path) OR !is_really_writable($cache_path)) return FALSE;
		
		//$pathからURIを作成〜ファイル名生成
		$path = md5($path);
		$dirname = substr($path, 0, 3);
		$dirpath = $cache_path.'/'.$dirname.'/';
		$filepath = $dirpath.$path;
		
		if (!is_dir($dirpath)) mkdir($dirpath, 0777);
		
		if (!$fp = @fopen($filepath, FOPEN_WRITE_CREATE_DESTRUCTIVE)) {
			log_message('error', "Unable to write cache file: ".$filepath);
			return;
		}
		
		$expire = time() + ($expire * 60);
		
		if (flock($fp, LOCK_EX)) {
			fwrite($fp, $expire.'TS--->'.$output);
			flock($fp, LOCK_UN);
		} else {
			log_message('error', "Unable to secure a file lock for file at: ".$filepath);
			return;
		}
		fclose($fp);
		@chmod($filepath, DIR_WRITE_MODE);

		log_message('debug', "Cache file written: ".$filepath);
	}
	
	function get_cache($path) {//キャッシュを取得
		$CI =& get_instance();
		$cache_path = ($CI->config->item('cache_path') == '') ? BASEPATH.'cache/' : $CI->config->item('cache_path');
		
		if (!is_dir($cache_path) OR !is_really_writable($cache_path)) return FALSE;
		
		//$pathからURIを作成〜ファイル名生成
		$path = md5($path);
		$dirname = substr($path, 0, 3);
		$dirpath = $cache_path.'/'.$dirname.'/';
		$filepath = $dirpath.$path;
		
		if (!is_dir($dirpath)) mkdir($dirpath, 0777);//フォルダが無ければ作るだけさ！
		if (!@file_exists($filepath)) return FALSE;//ファイルが存在しない場合
		if (!$fp = @fopen($filepath, FOPEN_READ)) return FALSE;//ファイルが開けない場合
		flock($fp, LOCK_SH);
		
		$cache = '';
		if (filesize($filepath) > 0) $cache = fread($fp, filesize($filepath));//ファイル読込
	
		flock($fp, LOCK_UN);
		fclose($fp);
					
		if (!preg_match("/(\d+TS--->)/", $cache, $match)) return FALSE;//タイムスタンプを確認
		
		if (time() >= trim(str_replace('TS--->', '', $match['1']))) { 		
			@unlink($filepath);
			log_message('debug', "Cache file has expired. File deleted");
			return FALSE;
		}
		
		log_message('debug', "Cache file is current. Sending it to browser.");
		return str_replace($match['0'], '', $cache);
	}
	
	function ajax($dat = array(), $param = array()) {
		$CI =& get_instance();
		$type = (isset($param['datatype'])) ? $param['datatype'] : $param['type'];
		switch ($type) {
			case 'json':
				header("Content-type: application/json");
				print json_encode($dat);
			break;
			
			case 'html':
				if (!empty($param['theme']) && !empty($param['tpl'])) {
					$CI->setting->set('theme', $param['theme']);
					print $CI->load->view($param['tpl'].'.php', array('dat' => $dat), true);
					break;
				}
			
			default:
				print_r($dat);
			break;
		}
	}
}