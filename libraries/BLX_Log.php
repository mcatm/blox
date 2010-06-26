<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Log extends CI_Log {
	
	function get($path, $user_param = array()) {
		$CI =& get_instance();
		$CI->load->library(array('pagination'));
		$CI->load->helper('array');
		
		$param = array(//デフォルトの設定
			'auth'			=> 0,
			'base_url'		=> base_url(),
			'id'			=> 0,
			'label'			=> 'log',
			'neighbor'		=> false,
			'num_links'		=> 4,
			'offset'		=> 0,
			'order'			=> 'desc',
			'pager'			=> true,
			'qty'			=> $CI->setting->get('post_max_qty_per_page'),
			'query'			=> "",
			'sort'			=> 'createdate',
			'stack'			=> true,
			'uri_segment'	=> 2,
			'where'			=> ""
		);
		
		$param = array_merge($param, $user_param);
		
		if (isset($param['encrypt'])) $CI->load->library('encrypt');//データを暗号化するか
		
		// ---------- 条件定義
		$CI->db->start_cache();
		$CI->db->like('log_path', $path, 'after');
		
		if (isset($param['id'])) {
			if (!is_array($param['id']) && !empty($param['id'])) {
				$CI->db->where('log_id', $param['id']);//個別ID
			} elseif (is_array($param['id'])) {
				$where_id = "";
				foreach ($param['id'] as $l_id) $where_id .= ' OR log_id = '.$l_id;
				$where_id = "(".substr($where_id, 4).")";
				$CI->db->where($where_id);
			}
		}
		
		if ($param['where'] != "") $CI->db->where('('.$param['where'].')');//where文の処理
		if (isset($param['value'])) $CI->db->where('log_value', $param['value']);
		if (isset($param['a'])) $CI->db->where('log_status_a', $param['a']);
		if (isset($param['b'])) $CI->db->where('log_status_b', $param['b']);
		
		$CI->db->stop_cache();
		// ---------- 条件定義ココまで
		
		$count = $CI->db->count_all_results(DB_TBL_LOG);
		
		if (isset($param['count'])) {
			$CI->db->flush_cache();
			return $count;//カウントを返すだけ
		}
		if ($param['qty'] == 0) $param['qty'] = $count;
		
		if ($count > 0) {//記事が存在した場合
			$CI->pagination->initialize(array(
				'base_url'		=> $param['base_url'],
				'total_rows'	=> $count,
				'uri_segment'	=> $param['uri_segment'],
				'num_links'		=> $param['num_links'],
				'per_page'		=> $param['qty']
			));
			
			$CI->db->order_by('log_'.$param['sort'], $param['order']);
		
			if ($param['stack']) {
				$CI->data->set($CI->db->get(DB_TBL_LOG, $param['qty'], $param['offset']), $param);
			} else {
				$out = $CI->data->get($CI->db->get(DB_TBL_LOG, $param['qty'], $param['offset']), $param);
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
			
			if (isset($CI->data->out[$param['label']])) {
				foreach($CI->data->out[$param['label']] as $k => $v) {
					//暗号化
					$CI->data->out[$param['label']][$k]['value'] = (isset($param['encrypt'])) ? decompress_array($CI->encrypt->decode($v['value'])) : decompress_array($v['value']);
					#header('Content-type:text/html; charset:utf8');
					#print_r($CI->encrypt->decode($v['value']));exit;
				}
			}
		}
		$CI->db->flush_cache();
	}
	
	function set_history($label, $id, $arr = array()) {
		$CI =& get_instance();
		$CI->load->helper('array');
		$path = 'history/'.$label.'/'.$id.'/';
		$user_id = (isset($CI->data->out['me']['id'])) ? $CI->data->out['me']['id'] : 0;
		$user_account = (isset($CI->data->out['me']['account'])) ? $CI->data->out['me']['account'] : 0;
		$this->set($path, compress_array($arr), 0, $user_id, $user_account);
		
		//過去の履歴を削除
		if ($count_level = $CI->setting->get('history_log_level')) {
			$count = $this->get($path, array('count' => true));
			if ($count > $count_level) {
				$history = $this->get($path, array('stack' => false, 'order' => 'asc', 'qty' => $count - $count_level));
				foreach ($history as $v) $del_id[] = $v['id'];
				$this->delete($del_id);
			}
		}
	}
	
	function set_mail($label, $target_id = 0, $arr = array()) {
		$CI =& get_instance();
		$CI->load->library('encrypt');
		$CI->load->helper('array');
		$path = 'mail/'.$label.'/'.$target_id.'/';
		return $this->set($path, $CI->encrypt->encode(compress_array($arr)), 0, $arr['name'], $arr['ip']);
	}
	
	function set_access() {
		if (isset($_SERVER['HTTP_USER_AGENT']) && !preg_match('/(bot[\\/\\-]|spider|crawl|slurp)/i', $_SERVER['HTTP_USER_AGENT'])) {//ボットでは無い場合
			$CI =& get_instance();
			$CI->load->helper(array('array', 'date'));
			$path = $this->_get_access_path();
			
			$CI->db->where('log_path', $path);
			$dat = $CI->data->set($CI->db->get(DB_TBL_LOG, 1), array('stack' => false));
			if (!$dat || count($dat) == 0) {//初
				$value = array();
				$value['count'] = array();
				$value['referer'] = array();
				$value['user_id'] = array();
				
				$value['count']['total'] = 1;
				$value['count']['rate'] = 1;
				
				$value['last_session'] = $CI->session->userdata('session_id');
				
				if (isset($_SERVER['HTTP_REFERER'])) $value['referer'][$_SERVER['HTTP_REFERER']] = 1;//リファラ
				if ($CI->session->userdata('id') !== false) $value['user_id'][$CI->session->userdata('id')] = 1;//ユーザーID
				$this->set($path, compress_array($value), 0, $value['count']['total'], $value['count']['rate']);
			} else {//追加
				$value = decompress_array($dat[0]['value']);
				if ($value['last_session'] != $CI->session->userdata('session_id') || (human_to_unix(now()) - human_to_unix($dat[0]['update'])) > 12) {//アクセス間隔が12秒以上経っているか、セッションIDが異なる場合、カウント
					if (isset($_SERVER['HTTP_REFERER'])) {
						if (array_key_exists($_SERVER['HTTP_REFERER'], $value['referer'])) {//リファラ
							$value['referer'][$_SERVER['HTTP_REFERER']]++;
						} else {
							$value['referer'][$_SERVER['HTTP_REFERER']] = 1;
						}
					}
					if ($CI->session->userdata('id') !== false) {
						if (array_key_exists($CI->session->userdata('id'), $value['user_id'])) {
							$value['user_id'][$CI->session->userdata('id')]++;//ユーザーID
						} else {
							$value['user_id'][$CI->session->userdata('id')] = 1;
						}
					}
					
					$value['count']['total']++;
					$value['count']['rate'] = round($value['count']['total'] / ((human_to_unix(now()) - human_to_unix($dat[0]['createdate'])) / 60 / 60 / 24), 2);//一日辺りのアクセス量
					
					$value['last_session'] = $CI->session->userdata('session_id');
					
					$this->set($path, compress_array($value), $dat[0]['id'], $value['count']['total'], $value['count']['rate']);
				}
			}
		}
	}
	
	function get_access($path = "") {
		$CI =& get_instance();
		$CI->load->helper('array');
		$path = ($path == "") ? $this->_get_access_path() : $path;
		$this->get($path, array('stack' => true, 'label' => 'access', 'pager' => false));
		$access = (isset($CI->data->out['access'])) ? $CI->data->out['access'] : array();
		unset($CI->data->out['access']);
		$CI->data->set_array('access', $this->_merge_access($access));
		#return $access;
	}
	
	function _merge_access($arr = array()) {
		if (empty($arr)) return false;
		#print_r($arr);exit;
		$res = array();
		foreach($arr as $k=>$v) {
			foreach ($v as $k2=>$v2) {
				switch ($k2) {
					case 'value':
						$res['value']['count']['total'] = (isset($res['value']['count']['total'])) ? $res['value']['count']['total'] + $v2['count']['total'] : $v2['count']['total'];
						$res['value']['count']['rate'] = (isset($res['value']['count']['rate'])) ? ($res['value']['count']['rate'] + $v2['count']['rate']) /2 : $v2['count']['rate'];
						$res['value']['referer'] = (isset($res['value']['referer']) && isset($v2['referer'])) ? array_merge($res['value']['referer'], $v2['referer']) : $v2['referer'];
						$res['value']['user_id'] = (isset($res['value']['user_id']) && isset($v2['user_id'])) ? array_merge($res['value']['user_id'], $v2['user_id']) : $v2['user_id'];
						$res['value']['lase_session'] = $v2['last_session'];
					break;
					
					case 'status_a':
						$res[$k2] = (isset($res[$k2])) ? $res[$k2] + $v2 : $v2;
					break;
					
					case 'status_b':
						$res[$k2] = (isset($res[$k2])) ? ($res[$k2] + $v2) / 2 : $v2;
					break;
					
					default:
						$res[$k2] = $v2;
					break;
				}
			}
		}
		#print_r($res);exit;
		return $res;
	}
	
	function set($path, $value = "", $id = 0, $a = NULL, $b = NULL) {
		$CI =& get_instance();
		
		$CI->load->helper('date');
		$now = now();
		
		$set = array(
			'log_path'		=> $path,
			'log_value'		=> $value,
			'log_update' => $now
		);
		if ($a != NULL) $set['log_status_a'] = $a;
		if ($b != NULL) $set['log_status_b'] = $b;
		if ($id > 0) {//更新
			$CI->db->where('log_id', $id);
			$CI->db->update(DB_TBL_LOG, $set);
			return $id;
		} else {//追加
			$set['log_createdate'] = $now;
			$CI->db->insert(DB_TBL_LOG, $set);
			return $CI->db->insert_id();
		}
	}
	
	function delete($id = array()) {
		$CI =& get_instance();
		
		if (!empty($id) && is_array($id)) {
			$log_id = $id;
		} else {
			if ($CI->input->post('id[]')) $log_id = $CI->input->post('id[]');
			if ($CI->input->post('id')) $log_id[] = $CI->input->post('id');
		}
		
		if (is_array($log_id)) {
			$where = "(";
			foreach ($log_id as $id) {
				$where .= 'log_id = '.$id.' OR ';
			}
			$where = substr($where, 0, -4).')';
			$CI->db->where($where);
			$CI->db->delete(DB_TBL_LOG);
		}
	}
	
	function get_mail($id) {
		$CI =& get_instance();
		$CI->load->library('encrypt');
		$CI->db->where('log_id', $id);
		$log = $CI->data->set($CI->db->get(DB_TBL_LOG), array('stack' => false));
		
		if ($log) {
			foreach($log as $k => $v) {
				$log[$k]['value'] = decompress_array($CI->encrypt->decode($v['value']));
			}
		}
		
		return $log;
	}
	
	function _get_access_path() {
		$CI =& get_instance();
		$path = 'access/';
		for($i=1;$CI->uri->segment($i);$i++) {
			$path .= $CI->uri->segment($i).'/';
		}
		$path .= ($CI->uri->segment(1) === false) ? 'top/' : "";
		return $path;
	}
	
	function BLX_Log() {	
		parent::CI_Log();
	}
}

?>