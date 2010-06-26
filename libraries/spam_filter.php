<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_spam_filter {
	
	function set($id = array()) {
		$CI =& get_instance();
		
		if (!empty($id) && is_array($id)) {
			$mail_id = $id;
		} else {
			if ($CI->input->post('id[]')) $mail_id = $CI->input->post('id[]');
			if ($CI->input->post('id')) $mail_id[] = $CI->input->post('id');
		}
		
		if (is_array($mail_id)) {
			$CI->load->library('mail');
			$CI->load->helper('file');
			
			$str_ip = read_file(APP_FOLDER.'/spam/ip.txt');
			$ip = explode(',', $str_ip);
			
			$CI->mail->get(array('id' => $mail_id));
			
			if (isset($CI->data->out['mail'])) {
				foreach($CI->data->out['mail'] as $k => $v) {
					if (!in_array($v['status_b'], $ip)) $str_ip .= ",".$v['status_b'];
				}
			}
			
			write_file(APP_FOLDER.'/spam/ip.txt', trim($str_ip, ','));
			
			$CI->log->delete($mail_id);
		}
	}
	
	function filter($type = 'mail') {
		$CI =& get_instance();
		
		switch($type) {
			case 'mail':
			default:
				$spam_flg = $this->_mail_filter();
			break;
		}
		
		if ($spam_flg) {
			log_message('error', 'Detected A Spam ---> '.$CI->input->ip_address);
			exit("maybe you posted a spam ".$type.". if some problems occured, please take a contact with administer");
		}
	}
	
	private function _mail_filter() {
		$CI =& get_instance();
		$CI->load->helper('file');
		$spam_flg = false;
		
		$ip = $CI->input->ip_address();//IPアドレス取得
		
		//ブラックリストIPを取得
		$str_ip = read_file(APP_FOLDER.'/spam/ip.txt');
		$black_ip = explode(',', $str_ip);
		
		if (in_array($ip, $black_ip) || $ip == '0.0.0.0') $spam_flg = true;//IPに不正があった場合、スパムフラグを立てる
		
		return $spam_flg;
	}
	
	function BLX_spam_filter() {
		
	}
}

?>