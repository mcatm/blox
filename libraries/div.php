<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_Div {
	
	var $msg;
	
	function get($user_param = array()) {
		$CI =& get_instance();
		$CI->load->library('pagination');
		$CI->load->helper('array');
		
		$param = array(//デフォルトの設定
			'array'		=> 'content',
			'base_url'	=> base_url(),
			'ext'		=> false,
			'id'		=> 0,
			'label'		=> 'div',
			'num_links'	=> 4,
			'offset'	=> 0,
			'order'		=> 'desc',
			'pager'		=> true,
			'related'	=> 3,
			'qty'		=> 20,
			'sort'		=> 'order',
			'stack'		=> true,
			'tag'		=> true,
			'uri_segment'	=> 2,
			'where'		=> ""
		);
		
		$param = array_merge($param, $user_param);//ユーザーパラメータで書き換え
		$site_prefix = $CI->setting->get_alias();
		
		// ---------------------- 条件定義
		$CI->db->start_cache();
		if ($param['where'] != "") $CI->db->where($param['where']);
		if ($param['id'] != 0) {
			if (is_array($param['id'])) {//複数検索
				foreach($param['id'] as $f) {
					$CI->db->or_where('div_id', $f);
				}
			} else {
				$CI->db->where('div_id', $param['id']);
			}
		}
		if (isset($param['type'])) $CI->db->where('div_type', $param['type']);
		if ($site_prefix) {
			$CI->db->like('div_alias', $site_prefix, 'after');
		} else {
			$CI->db->where('(div_alias NOT LIKE "[%")');
		}
		if (isset($param['alias'])) $CI->db->where('div_alias', $param['alias']);
		if (isset($param['query'])) $CI->db->where('(div_name LIKE "%'.$param['query'].'%" OR div_alias LIKE "%'.$param['query'].'%")');
		$CI->db->stop_cache();
		
		$count = $CI->db->count_all_results(DB_TBL_DIV);
		
		if (isset($param['count'])) {//カウントを返すだけ
			$CI->db->flush_cache();
			return $count;
		}
		
		if ($count > 0) {
			$CI->pagination->initialize(array(
				'base_url'		=> $param['base_url'],
				'total_rows'	=> $count,
				'uri_segment'	=> $param['uri_segment'],
				'num_links'		=> $param['num_links'],
				'per_page'		=> $param['qty']
			));
			
			$CI->db->order_by('div_'.$param['sort'], $param['order']);
			if ($param['stack']) {
				$CI->data->set($CI->db->get(DB_TBL_DIV, $param['qty'], $param['offset']), $param);
			} else {
				$out = $CI->data->get($CI->db->get(DB_TBL_DIV, $param['qty'], $param['offset']), $param);
				$CI->db->flush_cache();
				return $out;
			}
			$CI->db->flush_cache();
			
			foreach($CI->data->out[$param['label']] as $k => $v) {//追加データ付与
				//タイトル変換
				$CI->data->out[$param['label']][$k]['title'] = str_replace('{@sitename}', $CI->setting->get('title'), $CI->data->out[$param['label']][$k]['name']);
				
				//URL
				switch ($v['type']) {
					case 'category':
						$tmp_url = base_url().$CI->setting->get('url_alias_'.$v['type'])."/".$v['alias'].'/';
					break;
					
					default:
						$tmp_url = base_url().$v['alias'].'/';
					break;
				}
				$CI->data->out[$param['label']][$k]['url'] = trim(str_replace(array('@top/', $CI->setting->get_alias()), '', $tmp_url), '/').'/';
				#print $param['ext'].'<br />';
				if ($param['ext']) {//extra contents
					$CI->load->helper('array');
					$ext_linx = array();
					$ext_linx = $CI->linx->get('div2ext', array('a' => $v['id']));
					#exit($v['id']);
					#print_r($ext_linx);exit;
					if (!empty($ext_linx) && is_array($ext_linx)) {
						foreach ($ext_linx as $ex) {
							$ex_value = decompress_array($ex['param']);
							if (!empty($ex_value) && is_array($ex_value)) $CI->data->out[$param['label']][$k][$ex['status']] = $ex_value['value'];
						}
					}
				}
				
				if ($param['tag']) {//tags
					$CI->load->library('tag');
					$tag_linx = $CI->linx->get('div2tag', array('a' => $v['id']));
					if (is_array($tag_linx)) {
						$tag_where = array();
						foreach($tag_linx as $k2 => $v2) $tag_where[] = $v2['b'];
						$CI->data->out[$param['label']][$k]['tag'] = $CI->tag->get(array(
							'id' => $tag_where,
							'stack' => false,
							'pager' => false
						));
						
						$CI->data->out[$param['label']][$k]['tagstr'] = $CI->tag->_merge_tag($CI->data->out[$param['label']][$k]['tag']);
					} else {
						$CI->data->out[$param['label']][$k]['tag'] = array();
					}
					
					if (isset($param['related']) && !empty($CI->data->out[$param['label']][$k]['tag'])) {//related entries
						$CI->data->out[$param['label']][$k]['related'] = $CI->tag->_get_related($CI->data->out[$param['label']][$k]['tag'], array(
							'label' => 'div',
							'tagstr' => $CI->data->out[$param['label']][$k]['tagstr'],
							#'content' => htmlspecialchars($v['title']).'/'.htmlspecialchars($v['text']),
							'qty'	=> $param['related'],
							'id'	=> $v['id']
						));
						
						#print_r($CI->data->out[$param['label']][$k]['related']);exit;
					}
				}
			}
			
			if ($param['pager']) {
				$CI->data->set_array('page', array(
					'total'			=> $CI->pagination->total_rows,
					'current'		=> $CI->pagination->cur_page,
					'qty'			=> $CI->pagination->per_page,
					'pager'			=> $CI->pagination->create_links()
				));
			}
		}
		$CI->db->flush_cache();
	}
	
	function count($user_param = array()) {
		$user_param['count'] = true;
		return $this->get($user_param);
	}
	
	var $validation_rule = array(
			array(
				'field'   => 'name',
				'label'   => 'lang:system_post_label_title',
				'rules'   => 'trim|required|xss_clean'
			),
			array(
				'field'   => 'type',
				'label'   => 'lang:system_post_label_title',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'alias',
				'label'   => 'lang:system_post_label_title',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'theme',
				'label'   => 'lang:system_post_label_title',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'tpl',
				'label'   => 'lang:system_post_label_title',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'   => 'id_type',
				'label'   => 'lang:system_post_label_title',
				'rules'   => 'trim|xss_clean'
			),
			array(
				'field'		=> 'content[]',
				'label'  => 'lang:system_post_label_title'
			),
			array(
				'field'   => 'description',
				'label'   => 'lang:system_post_label_title',
				'rules'   => ''
			)
		);
	
	function set($id = 0) {
		$CI =& get_instance();
		
		$CI->load->library(array('form_validation', 'ext'));
		
		$CI->form_validation->set_error_delimiters($CI->setting->get('output_error_open'), $CI->setting->get('output_error_close'));//エラーメッセージの囲み
		
		$CI->lang->load('system');
		$CI->load->helper(array('form', 'date', 'array'));
		$now = now();
		
		$CI->form_validation->set_rules($this->validation_rule);
		
		if ($id == 0 && $CI->input->post('id')) $id = $CI->input->post('id');//POSTからIDを取得
		
		if ($id > 0) $this->get(array('id' => $id));//エントリを取得
		
		$this->get(array(
			'label'	=> 'div_option',
			'where'	=> '(div_type != "")'
		));
		
		//ページ拡張を読込
		$CI->ext->get(array('stack' => true, 'div' => 'div'));
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
		
		$CI->form_validation->set_rules($this->validation_rule);
		
		if (!$CI->auth->check_auth()) {
			if (isset($CI->data->out['me']['id'])) {
				if ($CI->input->post('type')=='category' && $CI->auth->check_auth('category')) {
					$flg_cnt_edit = true;
				}
				if ($CI->input->post('type')=='section' && $CI->auth->check_auth('section')) {
					$flg_cnt_edit = true;
				}
			}
		} else {
			$flg_cnt_edit = true;
		}
		
		if (!isset($flg_cnt_edit)) $msg_stop = $CI->lang->line('system_post_error_auth');
		
		//contentの設定
		$content = $CI->input->post('content');
		
		if ($content) {
			foreach ($content as $ct_key => $ct) {
				if (empty($ct['type'])) {
					unset($content[$ct_key]);
				} else {
					foreach($ct['param'] as $ctp_key => $ctp) {
						if (empty($ctp)) {
							unset($content[$ct_key]['param'][$ctp_key]);
						} else {
							if ($ctp == 'empty' || $ctp == 'none') $content[$ct_key]['param'][$ctp_key] == "";
							$content[$ct_key]['param'][$ctp_key] = $CI->form_validation->xss_clean(trim($content[$ct_key]['param'][$ctp_key]));
							if ($ctp_key == 'div' && $ctp == 'this') {
								$flg_update = true;
							}
						}
					}
				}
			}
		}
		
		if (isset($msg_stop) && !$CI->auth->check_auth('post') && $CI->input->post('type') != 1) {
			$this->msg = array(
				'result'	=> 'error',
				'msg'		=> $msg_stop_post
			);
		} else {
			if ($CI->form_validation->run() == FALSE) {
				$this->msg = array(
					'result'	=> 'error',
					'msg'		=> $CI->lang->line('system_post_error')
				);
			} else {
				$alias = (set_value('alias') == "" || set_value('alias') == $CI->setting->get_alias()) ? $CI->setting->get_alias().'@top' : set_value('alias');
				if ($id == 0) {//新規投稿
					$arr = array(
						'div_name'			=> set_value('name'),
						'div_alias'			=> $alias,
						'div_type'			=> set_value('type'),
						'div_theme'			=> set_value('theme'),
						'div_tpl'			=> set_value('tpl'),
						'div_content'		=> compress_array($content),
						'div_id_type'		=> set_value('id_type'),
						'div_description'	=> set_value('description')
					);
					
					$CI->db->insert(DB_TBL_DIV, $arr);
					$id = $CI->db->insert_id();
				} else {//記事編集
					$arr = array(
						'div_name'			=> set_value('name'),
						'div_alias'			=> $alias,
						'div_type'			=> set_value('type'),
						'div_theme'			=> set_value('theme'),
						'div_tpl'			=> set_value('tpl'),
						'div_id_type'		=> set_value('id_type'),
						'div_content'		=> compress_array($content),
						'div_description'	=> set_value('description')
					);
					
					$CI->db->where('div_id', $id);
					$CI->db->update(DB_TBL_DIV, $arr);
				}
				$this->msg = array(
					'result'	=> 'success',
					'msg'		=> $CI->lang->line('system_post_success')
				);
				if (isset($id)) $this->msg['id'] = $id;
				
				$CI->log->set_history('div', $id, $arr);//ヒストリーログを残す
				
				//タグ登録
				$CI->load->library('tag');
				$CI->tag->set($CI->input->post('tagstr'), 'div2tag', $id);
				
				if (isset($flg_update)) {
					if ($content) {
						foreach ($content as $ct_key => $ct) {
							foreach($ct['param'] as $ctp_key => $ctp) {
								if ($ctp_key == 'div' && $ctp == 'this') {
									$content[$ct_key]['param'][$ctp_key] = $id;
								}
							}
						}
					}
					
					$CI->db->where('div_id', $id);
					$CI->db->update(DB_TBL_DIV, array(
						'div_content'		=> compress_array($content)
					));
				}
				
				if (!empty($ext) && is_array($ext)) {//ページ拡張
					foreach($ext as $e) {
						$ext_value = set_value('ext_'.$e['field']);
						if (isset($ext_value)) {
							$set = array(
								'a'		=> $id,
								'status'	=> $e['field']
							);
							$ch = $CI->linx->get('div2ext', $set);
							
							if (!empty($ch)) $set['id'] = $ch[0]['id'];
							$set['param'] = array('value' => $ext_value);
							
							$CI->linx->set('div2ext', $set);
						}
					}
				}
			}
			
			
		}
		return $this->msg;
	}
	
	function delete($id = array()) {
		$CI =& get_instance();
		
		if (!empty($id) && is_array($id)) {
			$div_id = $id;
		} else {
			if ($CI->input->post('id[]')) $div_id = $CI->input->post('id[]');
			if ($CI->input->post('id')) $div_id[] = $CI->input->post('id');
		}
		
		if (is_array($div_id)) {
			foreach ($div_id as $id) {
				$CI->db->where('div_id', $id);
				$CI->db->delete(DB_TBL_DIV);
				
				$CI->linx->delete('post2div', array('id' => $id));
				
				//過去の履歴を削除
				$path = 'history/div/'.$id.'/';
				$history = $CI->log->get($path, array('stack' => false, 'qty' => 0));
				if (isset($hitsory)) {
					foreach ($history as $h) $del_id[] = $h['id'];
					$CI->log->delete($del_id);
				}
			}
		}
	}
	
	function clear() {//新規作成用
		$CI =& get_instance();
		$CI->load->library(array('form_validation'));
		$CI->form_validation->set_rules($this->validation_rule);
		$CI->form_validation->run();
	}
	
	function BLX_Div() {
		
	}
}

?>