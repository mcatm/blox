<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_User {
	
	function get($user_param = array()) {
		$CI =& get_instance();
		$CI->load->library(array('pagination'));
		$label = $CI->setting->get('url_alias_user');//ユーザーのラベル
		
		$param = array(//デフォルトの設定
			'base_url'	=> base_url(),
			'ext'		=> true,
			'file'		=> false,
			'file_main'	=> true,
			'file_main_arr'	=> array(),
			'history'	=> false,
			'id'		=> 0,
			'label'		=> 'user',
			'num_links'	=> 4,
			'offset'	=> 0,
			'order'		=> 'desc',
			'pager'		=> true,
			'qty'		=> $CI->setting->get('post_max_qty_per_page'),
			'query'		=> "",
			'sort'		=> 'createdate',
			'stack'		=> true,
			'uri_segment'	=> 2,
			'user'		=> 0,
			'where'		=> ""
		);
		
		$param = array_merge($param, $user_param);//ユーザーパラメータで書き換え
		
		$CI->db->start_cache();
		
		if (!empty($param['where'])) $CI->db->where('('.$param['where'].')');
		
		if ($param['id'] != 0) {
			if (is_array($param['id'])) {//複数検索
				foreach($param['id'] as $u) {
					$CI->db->or_where('user_id', $u);
				}
			} else {
				$CI->db->where('user_id', $param['id']);
			}
		}
		
		if (isset($param['type'])) $CI->db->where('user_type', $param['type']);
		if (isset($param['account'])) $CI->db->where('user_account', $param['account']);
		
		if (isset($param['extapp'])) {//外部APPのユーザー（emailとpasswordを持たない）
			$CI->db->where('user_email IS NULL');
			$CI->db->where('user_password IS NULL');
		}
		
		if (isset($param['reject_tmp_user'])) {//仮会員は含めない
			$CI->db->where('(user_type != "" AND user_type IS NOT NULL)');
		}
		
		if (isset($param['auth'])) {//権限別
			$CI->db->select('*, user_id AS id, user_name AS name');
			$CI->db->join(DB_TBL_USERTYPE, 'user_type = usertype_id');
			$atype = array();
			if (!is_array($param['auth'])) {
				$atype[] = $param['auth'];
			} else {
				$atype = $param['auth'];
			}
			$at_where = "(";
			foreach ($atype as $at) $at_where .= "usertype_type = '".$at."' OR ";
			$at_where = substr($at_where, 0, -4).')';
			$CI->db->where($at_where);
		}
		
		if ($param['query'] != "") $CI->db->like('user_name', $param['query']);//検索キー
		
		$CI->db->stop_cache();
		$count = $CI->db->count_all_results(DB_TBL_USER);
		
		if (isset($param['count'])) {
			$CI->db->flush_cache();
			return $count;
		}
		
		if ($count > 0) {//ユーザーが存在した場合
			if ($param['qty'] == 0) $param['qty'] = $count;
			
			$CI->pagination->initialize(array(
				'base_url'		=> $param['base_url'],
				'total_rows'	=> $count,
				'uri_segment'	=> $param['uri_segment'],
				'num_links'		=> $param['num_links'],
				'per_page'		=> $param['qty']
			));
			
			$CI->db->order_by('user_'.$param['sort'], $param['order']);
			
			if ($param['stack']) {
				$CI->data->set($CI->db->get(DB_TBL_USER, $param['qty'], $param['offset']), $param);
			} else {
				$out = $CI->data->get($CI->db->get(DB_TBL_USER, $param['qty'], $param['offset']), $param);
				$CI->db->flush_cache();
				return $out;
			}
			
			$CI->db->flush_cache();
			
			if ($param['pager']) {
				$CI->data->set_array('page', array(
					'total'			=> $CI->pagination->total_rows,
					'current'		=> $CI->pagination->cur_page,
					'qty'			=> $CI->pagination->per_page,
					'pager'			=> $CI->pagination->create_links()
				));
			}
			
			foreach($CI->data->out[$param['label']] as $k => $v) {//追加データ付与
				$CI->db->where('usertype_id', $v['type']);
				$tmp_usertype = $CI->data->get($CI->db->get(DB_TBL_USERTYPE, 1));
				$CI->data->out[$param['label']][$k]['usertype'] = $tmp_usertype[0];
				
				if ($param['file_main']) {//メインファイル
					$CI->load->library('file');
					$file_linx = $CI->linx->get('user2file', array('a' => $v['id'], 'status' => 'main'));
					if (is_array($file_linx)) {
						$file_where = array();
						foreach($file_linx as $k2 => $v2) $file_where[] = $v2['b'];
						$CI->data->out[$param['label']][$k]['file_main'] = $CI->file->get(array('id' => $file_where, 'stack' => false));
						$param['file_main_arr'] = $CI->data->out[$param['label']][$k]['file_main'];
					}
				}
				
				if (isset($param['extapp'])) {//外部アプリ
					$CI->data->out[$param['label']][$k]['extapp'] = $CI->linx->get('user2extapp', array(
						'a'	=> $v['id']
					));
				}
				
				if ($param['ext']) {//拡張
					$CI->load->helper('array');
					$ext_link = array();
					$ext_linx = $CI->linx->get('user2ext', array('a' => $v['id']));
					if (!empty($ext_linx) && is_array($ext_linx)) {
						foreach ($ext_linx as $ex) {
							$ex_value = decompress_array($ex['param']);
							if (!empty($ex_value) && is_array($ex_value)) $CI->data->out[$param['label']][$k][$ex['status']] = $ex_value['value'];
						}
					}
				}
				
				if ($param['file']) {//ファイル
					$CI->load->library('file');
					$file_linx = $CI->linx->get('user2file', array('a' => $v['id']));
					if (is_array($file_linx)) {
						$file_where = array();
						foreach($file_linx as $k2 => $v2) $file_where[] = $v2['b'];
						$CI->data->out[$param['label']][$k]['file'] = $CI->file->get(array('id' => $file_where, 'stack' => false));
					}
				}
			}
		}
		$CI->db->flush_cache();
		if (isset($CI->data->out[$param['label']])) {
			return $CI->data->out[$param['label']];
		} else {
			return;
		}
	}
	
	function count($user_param = array()) {
		$user_param['count'] = true;
		return $this->get($user_param);
	}
	
	var $validation_rule = array(
			array(
				'field'   => 'name',
				'label'   => 'lang:system_user_label_name',
				'rules'   => 'trim|required|xss_clean'
			),
			array(
				'field'   => 'type',
				'label'   => 'lang:system_user_label_type',
				'rules'   => 'trim|numeric|required|xss_clean'
			),
			array(
				'field'   => 'description',
				'label'   => 'lang:system_user_label_description',
				'rules'   => 'xss_clean'
			),
			array(
				'field'   => 'file[]',
				'label'   => 'lang:system_post_label_file',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'title',
				'label'   => 'lang:system_user_label_title',
				'rules'   => 'xss_clean'
			)
		);
	
	function set($id = 0) {
		$CI =& get_instance();
		
		$CI->load->library(array('form_validation', 'ext'));
		
		$CI->form_validation->set_error_delimiters($CI->setting->get('output_error_open'), $CI->setting->get('output_error_close'));//エラーメッセージの囲み
		
		$CI->load->helper(array('form', 'date'));
		$CI->data->set_array('usertype', $CI->auth->get_usertype());
		$now = now();
		
		if ($id == 0 && $CI->input->post('id')) $id = $CI->input->post('id');//POSTからIDを取得
		
		//記事拡張を読込
		$CI->ext->get(array('stack' => true, 'div' => 'user'));
		if (isset($CI->data->out['ext'])) {
			$ext = $CI->data->out['ext'];
			if (!empty($ext) && is_array($ext)) {
				foreach ($ext as $e) {
					$this->validation_rule[] = array(
						'field'		=> 'ext_'.$e['field'],
						'rules'		=> $e['rule'],
						'label'		=> $e['label']
					);
				}
			}
		}
		
		if ($id > 0) {//編集
			$this->get(array('id' => $id));//ユーザーを取得
			$auth = ($CI->auth->check_auth('user')) ? true : false;//権限設定
			$this->validation_rule[] = array(
				'field'   => 'account',
				'label'   => 'lang:system_user_label_account',
				'rules'   => 'trim|alpha_dash|xss_clean'
			);
		} else {//新規作成
			$auth = ($CI->auth->check_auth('user')) ? true : false;//権限設定
			$this->validation_rule[] = array(
				'field'   => 'account',
				'label'   => 'lang:system_user_label_account',
				'rules'   => 'trim|alpha_dash|callback_check_account|required|xss_clean'
			);
			$this->validation_rule[] = array(
				'field'   => 'email',
				'label'   => 'lang:system_user_label_email',
				'rules'   => 'trim|valid_email|callback_check_email|required|xss_clean'
			);
			$this->validation_rule[] = array(
				'field'   => 'password',
				'label'   => 'lang:system_user_label_password',
				'rules'   => 'alpha_numeric|min_length[5]|required|xss_clean'
			);
			$this->validation_rule[] = array(
				'field'   => 'password_confirm',
				'label'   => 'lang:system_user_label_password_confirm',
				'rules'   => 'alpha_numeric|matches[password]|required|xss_clean'
			);
		}
		
		$CI->form_validation->set_rules($this->validation_rule);
		
		if ($id != $CI->data->out['me']['id'] && !$CI->auth->check_auth('user')) $msg_stop_post = $CI->lang->line('system_user_error_auth');//他人の情報を編集する権限がない場合
		
		if (isset($msg_stop_post) && $auth !== true) {
			$this->msg = array(
				'result'	=> 'error',
				'msg'		=> $msg_stop_post
			);
		} else {
			if ($CI->form_validation->run() == FALSE) {
				$this->msg = array(
					'result'	=> 'error',
					'msg'		=> $CI->lang->line('system_user_error')
				);
			} else {
				if ($id == 0) {//新規投稿
					$arr = array(
						'user_name'			=> set_value('name'),
						'user_account'		=> set_value('account'),
						'user_title'		=> set_value('title'),
						'user_password'		=> $this->make_password(set_value('password')),
						'user_email'		=> set_value('email'),
						'user_type'			=> set_value('type'),
						'user_description'		=> set_value('description'),
						'user_createdate'		=> $now,
						'user_modifydate'		=> $now
					);
					
					$CI->db->insert(DB_TBL_USER, $arr);
					$id = $CI->db->insert_id();
					
					//ファイル
					$file = set_value('file[]');
					if (isset($file) && is_array($file) & !empty($file)) {
						$CI->load->library('linx');
						foreach($file as $fk => $fv) {
							$set = array(
								'a'		=> $id,
								'b'		=> $fv
							);
							if ($fk == 0) $set['status'] = 'main';
							$CI->linx->set('user2file', $set);
						}
					}
				} else {//ユーザー編集
					$arr = array(
						'user_name'			=> set_value('name'),
						'user_description'	=> set_value('description'),
						'user_type'			=> set_value('type'),
						'user_title'		=> set_value('title'),
						'user_modifydate'	=> $now
					);
					
					$CI->db->where('user_id', $id);
					$CI->db->update(DB_TBL_USER, $arr);
										
					//ファイル
					$file = set_value('file[]');
					if (isset($file) && is_array($file)) {
						$CI->load->library('linx');
						foreach($file as $fk => $fv) {
							$ch = $CI->linx->count('user2file', array(
								'a'	=> $id,
								'b'	=> $fv
							));
							if ($ch == 0) {
								$set = array(
									'a'		=> $id,
									'b'		=> $fv
								);
								$CI->linx->set('user2file', $set);
							}
						}
					}
				}
				
				if (!empty($ext) && is_array($ext)) {//記事拡張
					foreach($ext as $e) {
						$ext_value = set_value('ext_'.$e['field']);
						if (isset($ext_value)) {
							$set = array(
								'a'		=> $id,
								'status'	=> $e['field']
							);
							$ch = $CI->linx->get('user2ext', $set);
							
							if (!empty($ch)) $set['id'] = $ch[0]['id'];
							$set['param'] = array('value' => $ext_value);
							
							$CI->linx->set('user2ext', $set);
						}
					}
				}
				
				$CI->log->set_history('user', $id, $arr);//ヒストリーログを残す
				
				$this->msg = array(
					'id'	=> $id,
					'result'	=> 'success',
					'msg'		=> $CI->lang->line('system_user_success')
				);
			}
		}
		return $this->msg;
	}
	
	function set_password($id = 0) {
		$CI =& get_instance();
		
		$CI->load->library(array('form_validation'));
		$CI->form_validation->set_error_delimiters($CI->setting->get('output_error_open'), $CI->setting->get('output_error_close'));//エラーメッセージの囲み
		
		$CI->load->helper(array('form', 'date'));
		$CI->data->set_array('usertype', $CI->auth->get_usertype());
		$now = now();
		
		if ($id == 0 && $CI->input->post('id')) $id = $CI->input->post('id');//POSTからIDを取得
		if ($id == 0) return false;
		
		$this->get(array('id' => $id));//エントリを取得
		$auth = ($CI->auth->check_auth('user')) ? true : false;//権限設定
		$this->validation_rule = array(
			array(
				'field'   => 'pwd',
				'label'   => 'lang:system_user_label_password',
				'rules'   => 'alpha_numeric|min_length[5]|required|xss_clean'
			),
			array(
				'field'   => 'pwd_old',
				'label'   => 'lang:system_user_label_password_confirm',
				'rules'   => 'alpha_numeric|matches[pwd_old]|required|xss_clean'
			),
			array(
				'field'   => 'pwd_confirm',
				'label'   => 'lang:system_user_label_password_confirm',
				'rules'   => 'alpha_numeric|matches[pwd_old]|required|xss_clean'
			)
		);
		
		$CI->form_validation->set_rules($this->validation_rule);
		
		if ($id != $CI->data->out['me']['id'] && !$CI->auth->check_auth('user')) $msg_stop_post = $CI->lang->line('system_user_error_auth');//他人の情報を編集する権限がない場合
		
		if (isset($msg_stop_post) && $auth !== true) {
			$this->msg = array(
				'result'	=> 'error',
				'msg'		=> $msg_stop_post
			);
		} else {
			if ($CI->form_validation->run() == FALSE) {
				$this->msg = array(
					'result'	=> 'error',
					'msg'		=> $CI->lang->line('system_user_error')
				);
			} else {
				$arr = array(
					'user_password'			=> $this->make_password(set_value('pwd')),
					'user_modifydate'	=> $now
				);
				
				$CI->db->where('user_id', $id);
				$CI->db->update(DB_TBL_USER, $arr);
			}
			
			$this->msg = array(
				'id'	=> $id,
				'result'	=> 'success',
				'msg'		=> $CI->lang->line('system_user_success')
			);
		}
		return $this->msg;
	}
	
	function invite() {
		$CI =& get_instance();
		
		$CI->load->helper(array('form', 'date', 'token'));
		$CI->data->set_array('usertype', $CI->auth->get_usertype());
		$now = now();
		$result = array();
		
		if ($CI->input->post('email') && $CI->input->post('type')) {
			$emails = explode(',', $CI->input->xss_clean($CI->input->post('email')));
			
			foreach ($emails as $email) {
				if ($this->_check_unique('user_email', $email)) {//過去に登録されていない場合
					$email = trim($email);
					
					$path = 'user/invite';
					if (isset($CI->data->out['me']['id'])) $path .= '/'.$CI->data->out['me']['id'];
					
					$token = make_token(16);
					$email_key = $this->_make_email_key($email);
					
					#print $email_key.'<br />';
					$hash = sha1($email_key.$token);
					#print $hash.'<hr />';
					
					$CI->log->set($path, $hash, 0, $email, $CI->input->post('type'));
					
					$result[] = array(
						'email'	=> $email,
						'hash'	=> $hash,
						'key'	=> $email_key,
						'url'	=> base_url().'login/confirm/'.$email_key.'/'.$token.'/',
						'token'	=> $token
					);
				}
			}
			
			/*print_r($result);
			print '<hr />';
			print $CI->input->post('message');
			print $CI->input->post('type');
			exit();*/
		}
	}
	
	function confirm($key, $token) {
		$CI =& get_instance();
		
		$hash = sha1($key.$token);
		
		print_r($CI->log->get('user/invite', array('value' => $hash, 'stack' => false)));
		
		
	}
	
	function clear() {//新規作成用
		$CI =& get_instance();
		$CI->load->library(array('form_validation'));
		$CI->form_validation->set_rules($this->validation_rule);
		$CI->data->set_array('usertype', $CI->auth->get_usertype());
		$CI->form_validation->run();
	}
	
	function get_ext($stack = false) {
		$CI =& get_instance();
		
		$CI->db->where('ext_div', 'user');
		$CI->db->order_by('ext_order', 'asc');
		return $CI->data->set($CI->db->get(DB_TBL_EXT), array('stack' => $stack, 'label' => 'ext'));
	}
	
	function get_anonymous() {
		$CI =& get_instance();
		$a = $CI->auth->get_usertype('anonymous');
		return array(
			0 => array(
				'id'	=> $a[0]['id'],
				'name'	=> $a[0]['name']
			)
		);
	}
	
	function action($user_id) {
		if ($user_id > 0) {
			$CI =& get_instance();
			$CI->load->helper('date');
			$CI->db->where('user_id', $user_id);
			$CI->db->update(DB_TBL_USER, array('user_actiondate' => now()));
		}
	}
	
	function _check_unique($field, $str, $exc = false) {
		$CI =& get_instance();
		$CI->db->where($field, $str);
		if ($exc > 0) $CI->db->where($field.' != '.$exc);//除外
		return ($CI->db->count_all_results(DB_TBL_USER) == 0) ? true : false;
	}
	
	function make_password($str) {
		$CI =& get_instance();
		$str = crypt($str, $CI->setting->get('crypt_salt'));
		return substr($str, 2);
	}
	
	function _make_email_key($email) {
		return str_replace(array('@','.','+'), '' ,$email);
	}
	
	function BLX_User() {
		
	}
}

?>