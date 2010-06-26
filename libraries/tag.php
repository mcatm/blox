<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Tag {
	
	var $msg;
	
	function get($user_param = array()) {
		$CI =& get_instance();
		$CI->load->library(array('pagination', 'user'));
		
		$label = $CI->setting->get('url_alias_tag');//タグのラベル
		
		$param = array(//デフォルトの設定
			'auth'		=> 0,
			'base_url'	=> base_url(),
			'id'		=> 0,
			'label'		=> 'tag',
			'num_links'	=> 4,
			'offset'	=> 0,
			'order'		=> 'desc',
			'pager'			=> true,
			'qty'		=> $CI->setting->get('post_max_tag_per_page'),
			'query'		=> "",
			'sort'		=> 'createdate',
			'stack'		=> true,
			'uri_segment'	=> 2,
			'user'		=> 0,
			'where'		=> ""
		);
		
		$param = array_merge($param, $user_param);//ユーザーパラメータで書き換え
		
		$CI->db->start_cache();
		
		//条件定義
		if ($param['where'] != "") {//where文が直接書き込まれている場合
			$CI->db->where('('.$param['where'].')');
		} else {
			if ($param['id'] != 0) {
				if (is_array($param['id'])) {//複数検索
					foreach($param['id'] as $t) {
						$CI->db->or_where('tag_id', $t);
					}
				} else {
					$CI->db->where('tag_id', $param['id']);
				}
			}
			if ($param['query'] != "") $CI->db->like('tag_name', $param['query']);//検索キー
		}
		
		$CI->db->stop_cache();
		$count = $CI->db->count_all_results(DB_TBL_TAG);
		
		if (isset($param['count'])) {
			$CI->db->flush_cache();
			return $count;//カウントを返すだけ
		}
		
		if ($count > 0) {//記事が存在した場合
			$CI->pagination->initialize(array(
				'base_url'		=> $param['base_url'],
				'total_rows'	=> $count,
				'uri_segment'	=> $param['uri_segment'],
				'num_links'		=> $param['num_links'],
				'per_page'		=> $param['qty']
			));
			
			$CI->db->order_by('tag_'.$param['sort'], $param['order']);
			
			if ($param['stack']) {
				$CI->data->set($CI->db->get(DB_TBL_TAG, $param['qty'], $param['offset']), $param);
			} else {
				$out = $CI->data->get($CI->db->get(DB_TBL_TAG, $param['qty'], $param['offset']), $param);
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
				
			}
		}
		$CI->db->flush_cache();
	}
	
	function count($user_param = array()) {
		$user_param['count'] = true;
		return $this->get($user_param);
	}
	
	function set($tagstr = "", $type = '', $row_id, $author_id = 0) {//タグの登録処理
		$this->_unlink_tag($type, $row_id);
		if ($tagstr != "") {
			$tag = $this->_separate_tag($tagstr);
			$this->_update_tag($tag, $type, $row_id, $author_id);
		}
	}
	
	function _update_tag($tag, $type = "", $row_id, $author_id = 0) {
		$CI =& get_instance();
		$CI->load->library('linx');
		$CI->load->helper('date');
		$now = now();//日付取得
		foreach ($tag as $k => $v) {
			if ($v) {
				$CI->db->where('tag_name', $v);
				if ($CI->db->count_all_results(DB_TBL_TAG) == 0) {//未登録のタグ
					//タグを登録
					$set = array(
						'tag_name'		=> $v,
						'tag_count'		=> 1,
						'tag_createdate'	=> $now,
						'tag_update'		=> $now
					);
					$CI->db->insert(DB_TBL_TAG, $set);
					$tag_id = $CI->db->insert_id();
					
					//タグをエントリと関連づける
					$lnx = array(
						'a'		=> $row_id,
						'b'		=> $tag_id
					);
					if ($author_id > 0) $lnx['status'] = $author_id;
					$CI->linx->set($type, $lnx);
				} else {//登録済みのタグ
					$CI->db->where('tag_name', $v);
					$q = $CI->db->get(DB_TBL_TAG);
					$r = $q->result();
					$tag_id = $r[0]->tag_id;
					
					$where = array(
						'a'	=> $row_id,
						'b'	=> $tag_id
					);
					$c = $CI->linx->count($type, $where);
					
					if ($c == 0) {//関連付けられていない場合、タグを関連づける
						$lnx = array(
							'a'		=> $row_id,
							'b'		=> $tag_id
						);
						if ($author_id > 0) $lnx['status'] = $author_id;
						$CI->linx->set($type, $lnx);
					}
					
					//タグのデータを更新
					$new_count = $CI->linx->count($type, array('b' => $tag_id));
					$CI->db->where('tag_id', $tag_id);
					$CI->db->update(DB_TBL_TAG, array(
						'tag_count'		=> $new_count,
						'tag_update'	=> $now
					));
				}
			}
		}
	}
	
	function _separate_tag($str) {//文字列をタグの配列に変換
		$CI =& get_instance();
		$arr = explode($CI->setting->get('tag_delimiter'), $str);
		foreach ($arr as $k => $v) {
			$v = trim($v);
			if($v != "") $dat[$k] = htmlspecialchars(trim(strip_tags($v)));
		}
		return $dat;
	}
	
	function _unlink_tag($type = '', $row_id) {//タグを解除
		if ($type != "") {
			$CI =& get_instance();
			$CI->load->library('linx');
			
			$CI->linx->delete($type, array(
				'a'	=> $row_id
			));
		}
	}
	
	function _merge_tag($arr) {//カンマ区切り
		$CI =& get_instance();
		$n = 0;
		$str = "";
		if (count($arr)) {
			foreach ($arr as $k=>$v) {
				if ($n) $str .= $CI->setting->get('tag_delimiter');
				$str .= $v['name'];
				$n++;
			}
		}
		return $str;
	}
	
	function _get_related($tag = array(), $user_param = array()) {
		$CI =& get_instance();
		if (!empty($tag) && $CI->setting->get('flg_get_related')) {
			$CI->load->helper('array');
			$post = array();
			$param = array(
				'label'		=> 'post',
				'datatype'	=> 'php',
				'content'	=> '',
				'qty'		=> 10,
				'total_pie'	=> 0,
				'tag_qty'	=> count($tag),
				'id'		=> 0
			);
			$param = array_merge($param, $user_param);
			
			$path = 'related/'.$param['label'].'/'.$param['id'].'/';//cache用パスを作成
			
			$dat = $CI->output->get_cache($path);
			if (!$dat) {
				if ($param['content'] != "") {
					foreach ($tag as $k => $t) {
						$c = preg_match_all('('.$t['name'].')', $param['content'], $mt);
						$tag[$k]['appearance'] = $c;
						$param['total_pie'] += ($c + 1);
					}
				}
				
				foreach ($tag as $k => $t) {
					$CI->load->library('post');
					$tag[$k]['rate'] = round($t['appearance'] / $param['total_pie'], 4);
					
					$CI->db->select('*, post_id AS id');
					$CI->db->join(DB_TBL_LINX, 'linx_a = post_id');
					$CI->db->where('linx_b', $t['id']);
					$CI->db->where('linx_type', 'post2tag');
					$CI->db->where('post_id != '.$param['id']);
					$CI->db->where('post_deleted', 0);
					$CI->db->where('post_status', 0);
					$q = $CI->db->get(DB_TBL_POST, $param['qty']);
					#print_r($q->result());
					foreach ($q->result() as $pk => $pv) {
						if (isset($post[$pv->post_id]['appearance'])) {
							$post[$pv->post_id]['appearance']	+= preg_match_all('('.$t['name'].')', $pv->post_meta, $mt);
							$post[$pv->post_id]['level']		+= ($post[$pv->post_id]['appearance'] + 1) * (1 + $tag[$k]['rate']);
						} else {
							$CI->post->get(array('id' => $pv->post_id, 'qty' => 1, 'label' => 'tmp_related'));
							$post[$pv->post_id] = $CI->data->out['tmp_related'][0];
							unset($CI->data->out['tmp_related'][0]);
							$post[$pv->post_id]['appearance']	= preg_match_all('('.$t['name'].')', $pv->post_meta, $mt);
							$post[$pv->post_id]['level']		= ($post[$pv->post_id]['appearance'] + 1) * (1 + $tag[$k]['rate']);
						}
					}
				}
				uasort($post, "sort_related");
				$dat = $post;
				$CI->output->set_cache($path, compress_array($post), 120);
			} else {
				$dat = decompress_array($dat);
			}
			
			return $dat;
		} else {
			return array();
		}
	}
	
	function delete($id = array()) {
		$CI =& get_instance();
		
		if (!empty($id) && is_array($id)) {
			$tag_id = $id;
		} else {
			if ($CI->input->post('id[]')) $tag_id = $CI->input->post('id[]');
			if ($CI->input->post('id')) $tag_id[] = $CI->input->post('id');
		}
		
		if (is_array($tag_id)) {
			foreach ($tag_id as $id) {
				$CI->db->where('tag_id', $id);
				$CI->db->delete(DB_TBL_TAG);
					
				$CI->linx->unlink('tag', $id);//リンクを削除
			}
		}
	}
	
	function BLX_Tag() {
		
	}
}

function sort_related($a, $b) {
	if ($a['level'] < $b['level']) {
		return 1;
	} elseif ($a['level'] > $b['level']) {
		return -1;
	}
	return 0;
}

?>