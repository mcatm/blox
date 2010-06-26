<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Mail {
	
	var $msg;
	
	function get($user_param = array()) {
		$CI =& get_instance();
		
		$param = array(//デフォルトの設定
			'auth'			=> 0,
			'base_url'		=> base_url(),
			'encrypt'		=> true,
			'id'			=> 0,
			'label'			=> 'mail',
			'neighbor'		=> false,
			'num_links'		=> 4,
			'offset'		=> 0,
			'order'			=> 'desc',
			'qty'			=> 40,
			'query'			=> "",
			'sort'			=> 'createdate',
			'stack'			=> true,
			'uri_segment'	=> 2,
			'where'			=> ""
		);
		
		$param = array_merge($param, $user_param);
		
		$path = 'mail';
		if (isset($param['div'])) $path .= '/'.$param['div'];
		
		$CI->log->get($path, $param);
	}
	
	function count($user_param = array()) {
		$user_param['count'] = true;
		return $this->get($user_param);
	}
	
	var $validation_rule = array(
			array(
				'field'   => 'label',
				'label'   => 'label',
				'rules'   => 'xss_clean'
			),
			array(
				'field'   => 'name',
				'label'   => 'lang:system_user_label_name',
				'rules'   => 'trim|required|xss_clean'
			),
			array(
				'field'   => 'email',
				'label'   => 'lang:system_user_label_email',
				'rules'   => 'trim|valid_email|required|xss_clean'
			),
			array(
				'field'   => 'content[]',
				'label'   => 'lang:system_post_label_title',
				'rules'   => 'trim|xss_clean'
			)
		);
	
	function set() {
		$CI =& get_instance();
		
		$CI->load->library(array('form_validation', 'user', 'spam_filter'));
		$label = $CI->input->post('label');
		
		if (!$CI->setting->get('sendmail_'.$label.'_required')) {//必須項目を設定可能
			if ($req = $CI->input->post('require')) {
				foreach ($req as $k => $v) {
					$this->validation_rule[] = array(
						'field'   => 'require['.$k.']',
						'label'   => 'lang:system_mail_label_require',
						'rules'   => 'trim|required|min_length[1]|xss_clean'
					);
				}
			}
		} else {
			
		}
		
		$CI->form_validation->set_error_delimiters(//エラーメッセージの囲み
			$CI->setting->get('output_error_open'),
			$CI->setting->get('output_error_close')
		);
		
		$CI->spam_filter->filter();
		
		$CI->load->helper(array('form', 'date'));
		$CI->form_validation->set_rules($this->validation_rule);
		$now = now();
		
		$ip = $CI->input->ip_address();
		
		if ($ip == '0.0.0.0') {
			$CI->load->helper('date');
			log_message('error', 'posted a spam message : '.now());
			return false;
		}
		
		if ($CI->form_validation->run() == FALSE) {
			$this->msg = array(
				'result'	=> 'error',
				'msg'		=> $CI->lang->line('system_post_error')
			);
		} else {
			$mailbody = array();
			
			$mailbody['name']		= set_value('name');
			$mailbody['email']		= set_value('email');
			$mailbody['ip']			= $ip;
			$mailbody['content']	= set_value('content[]');
			if ($CI->input->post('require')) $mailbody['require']	= $CI->input->post('require');
			
			$label = (isset($_POST['label'])) ? set_value('label') : 'contact';
			$mail_id = $CI->log->set_mail($label, 0, $mailbody);
			
			//メールを送る設定になっているか確認（settingで、「flg_sendmail_label_{label}」をtrueに）
			if ($CI->setting->get('email_admin')) {
				$this->get(array('id' => $mail_id));
				$CI->load->library('email');
				$CI->setting->set('theme', '_mail');
				
				//送信者に対してメール
				$CI->email->from($CI->setting->get('email_admin'), $CI->setting->get('title'));
				$CI->email->to($mailbody['email']);
				
				$title = ($CI->setting->get('email_title_send_'.$label)) ? $CI->setting->get('email_title_send_'.$label) : $CI->lang->line('system_email_title_send');
				$CI->email->subject($title);
				
				$tpl = ($CI->setting->get('email_tpl_'.$label)) ? $CI->setting->get('email_tpl_'.$label) : 'send.default.php';
				$message = $CI->load->view($tpl, $mailbody, true);
				$CI->email->message($message);
				
				$CI->email->send();
				
				#exit($CI->email->print_debugger());
				
				//管理人に対してメール
				$CI->email->from($mailbody['email'], $mailbody['name']);
				$CI->email->to($CI->setting->get('email_admin'));
				
				$title = ($CI->setting->get('email_title_receive_'.$label)) ? $CI->setting->get('email_title_receive_'.$label) : $CI->lang->line('system_email_title_receive');
				$CI->email->subject($title);
				
				$tpl = ($CI->setting->get('email_tpl_'.$label)) ? $CI->setting->get('email_tpl_'.$label) : 'receive.default.php';
				$message = $CI->load->view($tpl, $mailbody, true);
				$CI->email->message($message);
				
				$CI->email->send();
			}
			
			$this->msg = array(
				'result'	=> 'success',
				'msg'		=> $CI->lang->line('system_post_success')
			);
			if (isset($id)) $this->msg['id'] = $id;
		}
		return $this->msg;
	}
	
	function set_spam($mail_id) {
		if (!is_array($mail_id)) {
			$id[] = $mail_id;
		} else {
			$id = $mail_id;
		}
		
		foreach($id as $v) {
			
		}
	}
	
	function BLX_Mail() {
		
	}
}

?>