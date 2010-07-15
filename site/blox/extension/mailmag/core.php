<?

class Mailmag extends Extension {
	
	function get($user_param = array()) {
		$CI =& get_instance();
		
		$param = array(//デフォルトの設定
			'auth'			=> 0,
			'base_url'		=> base_url(),
			'id'			=> 0,
			'label'			=> 'mailmag',
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
		
		$path = 'mailmag';
		if (isset($param['div'])) $path .= '/'.$param['div'];
		
		$CI->log->get($path, $param);
	}
	
	var $validation_rule = array(
			array(
				'field'   => 'title',
				'label'   => 'title',
				'rules'   => 'xss_clean|required'
			),
			array(
				'field'   => 'device',
				'label'   => 'device',
				'rules'   => 'trim|required|xss_clean'
			),
			array(
				'field'   => 'text',
				'label'   => 'text',
				'rules'   => 'trim|required|xss_clean'
			),
			array(
				'field'   => 'createdate',
				'label'   => 'createdate',
				'rules'   => 'trim|xss_clean'
			)
		);
	
	function set() {//登録
		$CI =& get_instance();
		
		$CI->load->library(array('form_validation', 'user'));
		$CI->load->helper(array('form', 'date'));
		$now = now();
		
		$CI->form_validation->set_error_delimiters(//エラーメッセージの囲み
			$CI->setting->get('output_error_open'),
			$CI->setting->get('output_error_close')
		);
		
		$mailmag_id = ($CI->input->post('id')) ? $CI->input->post('id') : 0;
		#print $mailmag_id;exit;
		
		$CI->form_validation->set_rules($this->validation_rule);
		
		if ($CI->form_validation->run() == FALSE) {
			$this->msg = array(
				'result'	=> 'error',
				'msg'		=> $CI->lang->line('system_post_error')
			);
		} else {
			$CI->load->library('encrypt');
			$CI->load->helper('array');
			
			$mailbody = array();
			
			$mailbody['title']		= set_value('title');
			$mailbody['device']		= set_value('device');
			$mailbody['text']		= set_value('text');
			
			$path = 'mailmag';
			$CI->log->set($path, compress_array($mailbody), $mailmag_id, $mailbody['title'], $mailbody['device']);
		}
	}
	
	function send($id, $type = "") {
		$CI =& get_instance();
		
		if ($type == 'test') {//テスト配信
			$maillist = explode(',', $CI->setting->get('extension_mailmag_maillist_test'));
		} elseif ($type != 'all') {//PCもしくは携帯に配信
			$CI->load->library('encrypt');
			$CI->load->helper('array');
			
			$maillist = decompress_array($CI->encrypt->decode($CI->setting->get('extension_mailmag_maillist_'.$type)));
		} else {//全てに配信
			$CI->load->library('encrypt');
			$CI->load->helper('array');
			
			$maillist_pc = decompress_array($CI->encrypt->decode($CI->setting->get('extension_mailmag_maillist_pc')));
			$maillist_mb = decompress_array($CI->encrypt->decode($CI->setting->get('extension_mailmag_maillist_mobile')));
			
			$maillist = array_merge($maillist_pc, $maillist_mb);
		}
		print_r($maillist);
		
		$this->get(array('id' => $id));
		#print_r();exit;
		
		if (isset($maillist) && !empty($maillist) && isset($CI->data->out['mailmag'][0])) {
			$CI->load->library('email');
			
			$config['wrapchars'] = 400;
			$config['bcc_batch_mode'] = true;
			$config['bcc_batch_size'] = 100;
			$CI->email->initialize($config);
			
			$CI->email->from($CI->setting->get('extension_mailmag_master_email'), $CI->setting->get('extension_mailmag_master_name'));
			$CI->email->to($CI->setting->get('extension_mailmag_master_email'), $CI->setting->get('extension_mailmag_master_name'));
			$CI->email->bcc($maillist);
			
			$CI->email->subject($CI->data->out['mailmag'][0]['value']['title']);
			$CI->email->message($CI->data->out['mailmag'][0]['value']['text']);
			
			$CI->email->send();
			
			exit('success');
		}
		
		exit('error');
	}
	
	function Mailmag() {
		parent::Extension();
		$this->init('mailmag');//初期化
	}
}

?>