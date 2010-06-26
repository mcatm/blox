<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BLX_amazon {
	
	var $param;
	var $api_basepath = 'http://ecs.amazonaws.jp/onca/xml';
	
	function get($param) {
		$CI =& get_instance();
		$CI->load->library('xml');
		
		$this->param = array_merge($this->param, $param);
		
		//Cacheパス作成
		$cache_path = 'amazon/';
		foreach($this->param as $k => $p) {
			if (in_array($k, $this->cache_key)) $cache_path .= $p.'/';
		}
		
		$xml = $CI->output->get_cache($cache_path);
		if (!$xml) {
			$api_path = $this->_get_path($this->param);
			$xml = file_get_contents($api_path);
			$CI->output->set_cache($cache_path, $xml, 600);
		}
		
		$CI->xml->parse($xml);
		$this->init();
		return $CI->xml->dat;
	}
	
	function get_asin($url) {
		preg_match("(http://www\.amazon\.(.*?)/(.*?)(dp\/ASIN|ASIN|dp)/(.*[/|\r|\n]?))", $url, $mt);
		$asin = trim(substr($mt[4], 0, 10));//ASINは10文字ってことでいいのかなあ？
		return $asin;
	}
	
	function _get_path($param = array()) {
		$CI =& get_instance();
		
		ksort($param);//パラメータの順序を昇順に並び替えます
		
		$q = '';
		foreach ($param as $ak => $av) $q .= '&'.$this->urlencode_rfc3986($ak).'='.$this->urlencode_rfc3986($av);
		$q = substr($q, 1);

		//署名を作成
		$parsed_url = parse_url($this->api_basepath);
		$string_to_sign = "GET\n{$parsed_url['host']}\n{$parsed_url['path']}\n{$q}";
		$signature = base64_encode(hash_hmac('sha256', $string_to_sign, $CI->setting->get('amazon_dev_secret'), true));
		
		return $this->api_basepath.'?'.$q.'&Signature='.$this->urlencode_rfc3986($signature);
	}
	
	function init() {
		$CI =& get_instance();
		$this->param = array(
			'Service'		=> 'AWSECommerceService',
			'Version'		=> '2009-08-15'
		);
		$this->param['AWSAccessKeyId']	= $CI->setting->get('amazon_dev_id');
		$this->param['Timestamp']		= gmdate('Y-m-d\TH:i:s\Z');
		if ($CI->setting->get('amazon_associate_id')) $this->param['AssociateTag'] = $CI->setting->get('amazon_associate_id');
	}
	
	function __construct() {
		$this->init();
	}
	
	function urlencode_rfc3986($str) {//RFC3986 形式でURLエンコードする関数(via. p4life)
		return str_replace('%7E', '~', rawurlencode($str));
	}
	
	//キャッシュパスに含めるパラメータ
	var $cache_key = array(
		'ItemId',
		'AssociateTag',
		'MerchantId',
		'SearchIndex',
		'Keywords',
		'ListId',
		'Operation',
		'ProductPage',
		'ResponseGroup'
	);
}

?>