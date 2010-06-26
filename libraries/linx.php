<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Linx {
	
	function get($type, $user_param = array()) {//リンクを取得
		$CI =& get_instance();
		
		$param = array(//デフォルトの設定
			'id'		=> 0,
			'order'		=> 'desc',
			'sort'		=> 'createdate',
			'stack'		=> false,
			'where'		=> ""
		);
		
		$param = array_merge($param, $user_param);//ユーザーパラメータで書き換え
		
		if ($param['where'] != "") {
			$CI->db->where('('.$param['where'].')');
		} else {
			$CI->db->where('linx_type', $type);
		}
			
		if ($param['id'] != 0) $CI->db->where('linx_id', $param['id']);
		if (isset($param['a'])) $CI->db->where('linx_a', $param['a']);
		if (isset($param['b'])) $CI->db->where('linx_b', $param['b']);
		if (isset($param['status'])) $CI->db->where('linx_status', $param['status']);
		
		$CI->db->order_by('linx_'.$param['sort'], $param['order']);
		
		if (isset($param['count'])) return $CI->db->count_all_results(DB_TBL_LINX);//カウントを返すだけ
		
		return $CI->data->get($CI->db->get(DB_TBL_LINX), $param);
	}
	
	function count($type, $user_param = array()) {
		$user_param['count'] = true;
		return $this->get($type, $user_param);
	}
	
	function set($type, $user_param = array()) {//リンクを書き込む
		$CI =& get_instance();
		$CI->load->helper(array('date', 'array'));
		$now = now();
		
		$param = array(//デフォルトの設定
			'id'		=> 0,
			'a'			=> 0,
			'b'			=> 0,
			'status'	=> "",
			'param'		=> "",
			'unique'	=> false
		);
		
		$param = array_merge($param, $user_param);//ユーザーパラメータで書き換え
		$param['param'] = (is_array($param['param'])) ? compress_array($param['param']) : $param['param'];
		
		if ($param['unique'] != "") {
			$u_param = $param;
			$u_param['id'] = 0;
			if ($param['unique'] !== true) unset($u_param[$param['unique']]);
			$arr = $this->get($type, $u_param);
			if (isset($arr)) {
				$u_param['status'] = "";
				$u_param['unique'] = false;
				foreach($arr as $uni) {
					$u_param['id'] = $uni['id'];
					if ($param['unique'] !== true) $u_param[$param['unique']] = $uni['b'];
					#print_r($u_param);exit;
					$this->set($type, $u_param);
				}
			}
		}
		
		if($param['id'] == 0) {//新規追加
			$set = array(
				'linx_type'			=> $type,
				'linx_a'			=> $param['a'],
				'linx_b'			=> $param['b'],
				'linx_status'		=> $param['status'],
				'linx_param'		=> $param['param'],
				'linx_createdate'	=> $now,
				'linx_update'		=> $now
			);
			$CI->db->insert(DB_TBL_LINX, $set);
		} else {//編集
			$CI->db->where('linx_id', $param['id']);
			$set = array(
				'linx_a'		=> $param['a'],
				'linx_b'		=> $param['b'],
				'linx_status'	=> $param['status'],
				'linx_param'	=> $param['param'],
				'linx_update'	=> $now
			);
			$CI->db->update(DB_TBL_LINX, $set);
		}
		
		return true;
	}
	
	function unlink($label, $id) {//関連項目を全て削除
		$CI =& get_instance();
		
		$CI->db->where("(linx_type LIKE '".$label."2%' AND linx_a = ".$id.") OR (linx_type LIKE '%2".$label."' AND linx_b = ".$id.")");
		$q = $CI->db->get(DB_TBL_LINX);
		$result = $q->result();
		
		if (!empty($result)) {
			foreach ($result as $r) {
				$this->delete($r->linx_type, array('id' => $r->linx_id));
			}
		}
	}
	
	function delete($type, $user_param = array()) {//リンクを削除
		$CI =& get_instance();
		$CI->load->helper(array('date', 'array'));
		$now = now();
		
		$param = array(//デフォルトの設定
			'id'		=> 0,
			'where'		=> ""
		);
		
		$param = array_merge($param, $user_param);//ユーザーパラメータで書き換え
		
		$CI->db->flush_cache();
		if ($param['where'] != "") {
			$CI->db->where('('.$param['where'].')');
		} else {
			$CI->db->where('linx_type', $type);
			
			if ($param['id'] != 0) $CI->db->where('linx_id', $param['id']);
			if (isset($param['a'])) $CI->db->where('linx_a', $param['a']);
			if (isset($param['b'])) $CI->db->where('linx_b', $param['b']);
			if (isset($param['status'])) $CI->db->where('linx_status', $param['status']);
		}
		
		$CI->db->delete(DB_TBL_LINX);
		
		return true;
	}
}

?>