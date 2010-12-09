<?php

class Blox extends Model {
	
	function get($user_param = array()) {
		$CI =& get_instance();
		
		$param = array(//default paramaters
			'label'			=> 'blox',
			'offset'		=> 0,
			'qty'			=> 1,
			'stack'			=> true,
			'where'			=> ""
		);
		
		$param = array_merge($param, $user_param);
		
		// ------ make SQL
		
		$CI->db->start_cache();
		if (!isset($param['deleted'])) $CI->db->where('blox_deleted', 0);//don't get the deleted datas.
		
		// ------ SQL : select
		
		$CI->db->select('*');
		
		// ------ SQL : where
		
		// if it got the where query directly
		if (!is_array($param['where']) && !empty($param['where'])) $param['where'] = array($param['where']);
		if (!empty($param['where'])) {
			foreach($param['where'] as $k => $v) {
				if (is_numeric($k)) {
					$CI->db->where('('.$w.')');
				} else {
					$CI->db->where($k, $v);
				}
			}
		}
		
		$CI->db->stop_cache();
		$count = $CI->db->count_all_results(DB_TBL_BLOX, true);
		
		if ($param['qty'] == 'all') $param['qty'] = $count;//qtyが'all'の場合は、全てを選択
		
		if (isset($param['count'])) {
			$CI->db->flush_cache();
			return $count;//カウントを返すだけ
		}
		
		#print_r($CI->db->last_query());
		
		if ($count > 0) {//if the datas exist
			
			// ------ it insert datas to publish var.
			if ($param['stack']) {
				$CI->output->set($CI->db->get(DB_TBL_BLOX, $param['qty'], $param['offset']), $param);
			} else {
				$out = $CI->output->set($CI->db->get(DB_TBL_BLOX, $param['qty'], $param['offset']), $param);
				$CI->db->flush_cache();
				return $out;
			}
			
			// ------ get extra datas.
			if (isset($CI->output->dat[$param['label']])) {
				foreach($CI->output->dat[$param['label']] as $k => $v) {
					$CI->output->dat[$param['label']][$k]['url'] = base_url().$v['alias'].'/';//permalink
				}
			}
		}
		$CI->db->flush_cache();
		/*
		$CI->db->select('*, post_id AS id, post_status AS status, post_createdate AS createdate, post_type AS type');
		$CI->db->join(DB_TBL_LINX, 'post_id = linx_a');
		$CI->db->group_by('post_id');
		
		if (isset($param['div'])) {
			$CI->db->where('linx_type', 'post2div');
		} else {
			$CI->db->where('linx_type', 'post2user');
		}
		
		
		if ($param['user'] > 0) $CI->db->where('linx_b', $param['user']);//ユーザー指定
		
		if (isset($param['div'])) $CI->db->where('linx_b', $param['div']);//セクション指定
		
		if (isset($param['alias'])) $CI->db->where('post_alias', $param['alias']);
		if (isset($param['parent'])) $CI->db->where('post_parent', $param['parent']);//親記事
		if (isset($param['status'])) $CI->db->where('post_status', $param['status']);//状態
		
		if (isset($param['id'])) {
			if (!is_array($param['id']) && !empty($param['id'])) {
				$CI->db->where($param['id_type'], $param['id']);//個別ID
			} elseif (is_array($param['id'])) {
				$where_id = "";
				foreach ($param['id'] as $p_id) $where_id .= ' OR '.$param['id_type'].' = '.$p_id;
				$where_id = "(".substr($where_id, 4).")";
				$CI->db->where($where_id);
			}
		}
		
		if (isset($param['number'])) $CI->db->where('post_number', $param['number']);
		if (isset($param['type'])) $CI->db->where('post_type', $param['type']);//記事タイプ
		if (!empty($param['query'])) {//検索キー
			$CI->db->like('post_meta', $param['query']);
			$CI->data->out['query'] = $param['query'];
		}
		
		if ($param['auth'] !== 'cron') {
			$auth_where = '(post_status <= '.$param['auth'];
			if (!empty($CI->data->out['me']['id']) && defined('ADMIN_MODE') && !isset($param['div'])) $auth_where .= ' OR linx_b = '.$CI->data->out['me']['id'];
			$auth_where .= ')';
			$CI->db->where($auth_where);
		}*/
		
		
		
		
		
		
		
		/*$CI->load->library(array('pagination', 'user'));
		$label = explode(',', $CI->setting->get('url_alias_post'));//記事のラベル
		
		$param = array(//デフォルトの設定
			'auth'			=> 0,
			'base_url'		=> base_url(),
			'comment'		=> false,
			'ext'			=> true,
			'file'			=> false,
			'file_main'		=> true,
			'file_main_arr'	=> array(),
			'history'		=> false,
			'id'			=> 0,
			'id_type'		=> 'id',
			'label'			=> 'post',
			'neighbor'		=> false,
			'num_links'		=> 4,
			'offset'		=> 0,
			'order'			=> 'desc',
			'pager'			=> true,
			'get_parent'	=> false,
			'qty'			=> $CI->setting->get('post_max_qty_per_page'),
			'query'			=> "",
			'schedule'		=> false,
			'sort'			=> 'createdate',
			'stack'			=> true,
			'tag'			=> true,
			'uri_segment'	=> 2,
			'user'			=> 0,
			'where'			=> ""
		);
		
		
		$param['id_type'] = 'post_'.$param['id_type'];
		
		
		
		
		
		// ---------------------- 条件定義
		
		// ---------------------- 条件定義ココまで
		
		
			$CI->pagination->initialize(array(
				'base_url'		=> $param['base_url'],
				'total_rows'	=> $count,
				'uri_segment'	=> $param['uri_segment'],
				'num_links'		=> $param['num_links'],
				'per_page'		=> $param['qty']
			));
			
			if ($param['order'] == 'rand' || $param['order'] == 'random') {
				$CI->db->order_by('rand()');
			} else {
				$CI->db->order_by('post_'.$param['sort'], $param['order']);
			}
			
			
			
			$CI->db->flush_cache();
			
			//get a status of pages.
			if ($param['pager']) {
				$CI->data->set_array('page', array(
					'total'			=> $CI->pagination->total_rows,
					'current'		=> $CI->pagination->cur_page,
					'qty'			=> $CI->pagination->per_page,
					'offset'		=> $param['offset'],
					'pager'			=> $CI->pagination->create_links()
				));
			}
			
			if (isset($CI->data->out[$param['label']])) {
				foreach($CI->data->out[$param['label']] as $k => $v) {//add extra datas
					
					$linx = array();
					$linx['all'] = $CI->data->set($CI->linx->get_related('post', $v['id']), array('label' => 'linx', 'stack' => false));
					
					if (!empty($linx['all'])) {
						foreach ($linx['all'] as $l) {
							switch ($l['type']) {
								case 'post2user'://authors
									$linx['user'][] = $l;
								break;
								
								case 'post2tag'://tags
									$linx['tag'][] = $l;
								break;
								
								case 'post2file'://files
									$linx['file'][] = $l;
								break;
								
								case 'post2div'://div
									$linx['div'][] = $l;
								break;
								
								case 'post2ext'://ext
									$linx['ext'][] = $l;
								break;
							}
						}
					}
							
					//authors
					$user_where = array();
					if (isset($linx['user']) && is_array($linx['user'])) {
						$user_where = array();
						foreach($linx['user'] as $k2 => $v2) $user_where[] = $v2['b'];
						$CI->user->get(array('id' => $user_where, 'label' => 'tmp_author', 'pager' => false));
						if (isset($CI->data->out['tmp_author'])) {
							$CI->data->out[$param['label']][$k]['author'] = $CI->data->out['tmp_author'];
							unset($CI->data->out['tmp_author']);
						} else {
							$CI->data->out[$param['label']][$k]['author'] = $CI->user->get_anonymous();//if an author wasn't set.
						}
					} else {
						$CI->data->out[$param['label']][$k]['author'] = $CI->user->get_anonymous();//if an author wasn't set.
					}
					$CI->data->out[$param['label']][$k]['author_id'] = $user_where;
					
					//main files
					if ($param['file_main'] && isset($linx['file'])) {
						$CI->load->library('file');
						if (!empty($linx['file']) && is_array($linx['file'])) {
							foreach ($linx['file'] as $fl) {
								$file_label = (!empty($fl['status'])) ? 'file_'.$fl['status'] : 'file';
								$CI->data->out[$param['label']][$k][$file_label] = $CI->file->get(array('id' => $fl['b'], 'stack' => false));
							}
						}
					}
					
					//tags
					if ($param['tag'] && isset($linx['tag'])) {
						$CI->load->library('tag');
						if (is_array($linx['tag'])) {
							$tag_where = array();
							foreach($linx['tag'] as $k2 => $v2) $tag_where[] = $v2['b'];
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
								'label' => 'post',
								'tagstr' => $CI->data->out[$param['label']][$k]['tagstr'],
								'content' => htmlspecialchars($v['title']).'/'.htmlspecialchars($v['text']),
								'qty'	=> $param['related'],
								'id'	=> $v['id']
							));
						}
					}
					
					//sections and categories
					$div_where = array();
					if (isset($linx['div'])) {
						$CI->load->library('div');
						foreach($linx['div'] as $dk => $dv) {
							$div_where[] = $dv['b'];
						}
						$div_value = $CI->div->get(array('id' => $div_where, 'stack' => false));
						if (isset($div_value) && !empty($div_value)) {
							foreach ($div_value as $divv) {
								switch ($divv['type']) {
									case 'section':
									$CI->data->out[$param['label']][$k]['section'][] = $divv;
									break;
									
									case 'category':
									$CI->data->out[$param['label']][$k]['category'][] = $divv;
									break;
									
									default:
									$CI->data->out[$param['label']][$k]['div'][] = $divv;
									break;
								}
							}
						}
					}
					$CI->data->out[$param['label']][$k]['div_id'] = $div_where;
					
					if ($param['ext'] && isset($linx['ext'])) {//extra contents
						$CI->load->helper('array');
						if (!empty($linx['ext']) && is_array($linx['ext'])) {
							foreach ($linx['ext'] as $ex) {
								$ex_value = decompress_array($ex['param']);
								if (!empty($ex_value) && is_array($ex_value)) $CI->data->out[$param['label']][$k][$ex['status']] = $ex_value['value'];
							}
						}
					}
					
					if ($param['neighbor']) {
						$neighbor_set = array(
							array(
								'label' => 'next',
								'where' => 'post_createdate > "'.$v['createdate'].'" AND post_status = 0',
								'order' => 'asc'
							), array(
								'label' => 'prev',
								'where' => 'post_createdate < "'.$v['createdate'].'" AND post_status = 0',
								'order' => 'desc'
							)
						);
						foreach ($neighbor_set as $n) {
							$n_type = (isset($param['type'])) ? $param['type'] : 0;
							$this->get(array(
								'where' => $n['where'],
								'qty' => 1,
								'type'	=> $n_type,
								'order' => $n['order'],
								'label' => 'tmp_neighbor'
							));
							if (!empty($CI->data->out['tmp_neighbor'])) {
								$CI->data->out[$param['label']][$k]['neighbor'][$n['label']] = $CI->data->out['tmp_neighbor'][0];
								unset($CI->data->out['tmp_neighbor']);
							}
						}
					}
					
					if ($param['comment']) {//comment
						$this->get(array(
							'type'		=> 1,
							'parent'	=> $v['id'],
							'label'		=> 'tmp_comment'
						));
						if (!empty($CI->data->out['tmp_comment'])) {
							$CI->data->out[$param['label']][$k]['comment'] = $CI->data->out['tmp_comment'];
							unset($CI->data->out['tmp_comment']);
						}
					}
					
					if ($param['get_parent'] && !empty($v['parent'])) {//get a parent
						$this->get(array(
							'id'	=> $v['parent'],
							'pager'	=> false,
							'label'	=> 'tmp_parent'
						));
						if (!empty($CI->data->out['tmp_parent'])) {
							$CI->data->out[$param['label']][$k]['parent'] = $CI->data->out['tmp_parent'][0];
							unset($CI->data->out['tmp_parent']);
						}
					}
					
					if (defined('ADMIN_MODE') && ADMIN_MODE === true) {
						$CI->data->out[$param['label']][$k]['admin_url'] = base_url().'admin/post/'.$v['id'].'/';//url for admin
						$CI->data->out[$param['label']][$k]['edit_url'] = base_url().'admin/post/edit/'.$v['id'].'/';//url for admin to edit
					}
					
					if ($v['title'] == "") $CI->data->out[$param['label']][$k]['title'] = mb_substr(strip_tags($v['text']), 0, 40, 'utf-8');//if it don't have a title.
					if ($param['history']) {//change histories
						$CI->log->get('history/post/'.$v['id'].'/', array('label' => 'history'));
						if (isset($CI->data->out['history'])) $CI->data->out[$param['label']][$k]['history'] = $CI->data->out['history'];
						unset($CI->data->out['history']);
					}
					
					//text
					$text = htmlspecialchars_decode($v['text']);
					$separator = $CI->setting->get_formattag('page');
					$CI->data->out[$param['label']][$k]['text'] = $text;
					$CI->data->out[$param['label']][$k]['paragraph'] = explode($separator, $text);
					
					//page
					if (isset($param['page'])) {
						$p = ($param['page']) ? (int)$param['page'] - 1 : 0;
						$CI->data->out[$param['label']][$k]['page'] = $p;
						
						if (count($CI->data->out[$param['label']][$k]['paragraph']) > 1) {
							$CI->data->out[$param['label']][$k]['pager'] = array(
								'current'	=> $p + 1,
								'base_url'	=> trim(self_url(), '/').'/?p',
								'total'		=> count($CI->data->out[$param['label']][$k]['paragraph'])
							);
						}
					}
				}
			}
		}
		*/
	}
	
	function set() {
		
	}
}

?>