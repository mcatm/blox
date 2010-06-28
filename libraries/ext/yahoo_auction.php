<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_yahoo_auction {
	
	var $param = array(
		'method'	=> 'search',
		'query'		=> "",
		'type'	=> ""//返す値のタイプ（php / xml / jsonp）
	);
	
	function auctionItem($user_param = array()) {//商品詳細
		$param = array('method'	=> 'auctionItem');
		return $this->get($this->_get_param($param, $user_param));
	}
	
	function search($user_param = array()) {//検索
		$param = array('method'	=> 'search');
		return $this->get($this->_get_param($param, $user_param));
	}
	
	function categoryTree($user_param = array()) {//カテゴリ
		$param = array('method'	=> 'categoryTree');
		return $this->get($this->_get_param($param, $user_param));
	}
	
	function sellingList($user_param = array()) {//出品リスト
		$param = array('method'	=> 'sellingList');
		return $this->get($this->_get_param($param, $user_param));
	}
	
	function contentsMatchItem($user_param = array()) {//コンテンツマッチアイテム
		$param = array('method'	=> 'contentsMatchItem', 'api_version' => 1);
		return $this->get($this->_get_param($param, $user_param));
	}
	
	function get($param) {
		$CI =& get_instance();
		
		$api_url = $this->_get_api_url($param);
		$dat = $CI->output->get_cache($api_url);
		
		if (!$dat) {
			$dat = @file_get_contents($api_url);
			if (!empty($dat)) $CI->output->set_cache($api_url, $dat);
		}
		
		if ($param['type'] === 'php') {
			$CI->load->helper('array');
			$dat = decompress_array($dat);
		}
		
		return $dat;
	}
	
	function _get_param($param, $user_param = array()) {
		return array_merge(array_merge($this->param, $param), $user_param);
	}
	
	function _get_api_url($param = array()) {
		$api_url = $param['api_url'].'V'.$param['api_version'].'/';
		$api_url .= ($param['type'] !== "") ? $param['type'].'/' : '';
		$api_url .= $param['method'];
		
		$q = "?";
		foreach ($param as $k=>$v) {
			if (in_array($k, $this->allowed[$param['method']])) $q .= $k ."=". $v ."&";
		}
		$api_url .= substr($q, 0, -1);
		
		return $api_url;
	}
	
	function __construct() {
		$CI =& get_instance();
		$this->param['api_url'] = $CI->setting->get('yahoo_auction_api_url');
		$this->param['api_version'] = $CI->setting->get('yahoo_auction_api_version');
		$this->param['appid']	= $CI->setting->get('yahoo_app_id');
	}
	
	var $allowed = array(//許可されたリクエストパラメータ
		'auctionItem' => array('appid', 'callback', 'auctionID'),
		'categoryTree'	=> array('appid', 'callback', 'category'),
		'contentsMatchItem' => array('appid', 'type', 'url', 'sentence', 'category', 'results', 'output'),
		'sellingList'	=> array('appid', 'callback', 'sellerID', 'page', 'sort', 'order', 'store', 'aucminprice', 'aucmaxprice', 'aucmin_bidorbuy_price', 'aucmax_bidorbuy_price', 'escrow', 'easypayment', 'ybank', 'freeshipping', 'wrappingicon', 'buynow', 'thumbnail', 'attn', 'english', 'point', 'gift_icon', 'item_status', 'offer'),
		'search'	=> array('appid', 'query', 'category', 'order', 'aucminprice', 'aucmaxprice', 'aucmin_bidorbuy_price', 'aucmax_bidorbuy_price', 'loc_cd', 'escrow', 'easypayment', 'ybank', 'new', 'freeshipping', 'wrappingicon', 'buynow', 'thumbnail', 'attn', 'english', 'point', 'gift_icon', 'item_status', 'offer')
	);
}

?>