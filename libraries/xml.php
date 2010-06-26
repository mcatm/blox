<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

###################################################################################
#
# XML Library, by Keith Devens, version 1.2b
# http://keithdevens.com/software/phpxml
#
# This code is Open Source, released under terms similar to the Artistic License.
# Read the license at http://keithdevens.com/software/license
#
# and a lot of changes for the Codeigniter framework by HAMADA,Satoshi
#
###################################################################################

class Xml {
	
	var $parser;		//xmlパーサー
	var $dat;			//xmlデータを格納する配列
	var $tmp;			//tmp
	var $tag_last;		//最新のタグ
	var $parent;		//親配列
	var $stack;			//スタック
	
	function Xml() {//コンストラクタ
		$this->parser = &xml_parser_create();
		xml_parser_set_option(&$this->parser, XML_OPTION_CASE_FOLDING, false);//xmlパーサーの大文字設定を解除
		xml_set_object(&$this->parser, $this);//xmlパーサーをオブジェクト内部で使用可能に設定
		xml_set_element_handler(&$this->parser, 'open', 'close');
		xml_set_character_data_handler(&$this->parser, 'data');
	}
	
	function parse($xml, $rss = false) {//xmlを解析
		$this->document		= array();
		$this->stack		= array();
		$this->parent		= &$this->document;
		$this->dat = @xml_parse(&$this->parser, &$xml, TRUE) ? $this->document : NULL;
		$this->free();
	}
	
	function open(&$parser, $tag, $attr) {//start_element_handler
		if ($this->rss_flg) $this->parse_rss(&$parser, $tag, $attr);//RSSパース用
		
		$this->tmp = '';//初期化
		$this->tag_last = $tag;//最新のタグを記録
		if(is_array($this->parent) AND array_key_exists($tag, $this->parent)) {//今のタグが既出の場合
			if(is_array($this->parent[$tag]) AND array_key_exists(0, $this->parent[$tag])){//キーが数字の場合、出現回数をカウントして、キーにする
				$key = $this->count_numeric_items($this->parent[$tag]);
			} else {
				if(array_key_exists("$tag attr",$this->parent)){
					$arr = array('0 attr'=>&$this->parent["$tag attr"], &$this->parent[$tag]);
					unset($this->parent["$tag attr"]);
				} else {
					$arr = array(&$this->parent[$tag]);
				}
				$this->parent[$tag] = &$arr;
				$key = 1;
			}
			$this->parent = &$this->parent[$tag];
		} else {
			$key = $tag;
		}
		
		if($attr) $this->parent["$key attr"] = $attr;
		$this->parent = &$this->parent[$key];
		$this->stack[] = &$this->parent;
	}
	
	function close(&$parser, $tag) {//end_element_handler
		if($this->tag_last == $tag){
			$this->parent = $this->tmp;
			$this->tag_last = NULL;
		}
		array_pop($this->stack);
		if($this->stack) $this->parent = &$this->stack[count($this->stack)-1];
	}
	
	function data(&$parser, $value) {//データ挿入
		if($this->tag_last != NULL) $this->tmp .= $value;
	}
	
	function free() {//パーサーを解放
		@xml_parser_free(&$this->parser);
		$this->xml();
	}
	
	function count_numeric_items(&$array){//配列中の数字をカウント
		return is_array($array) ? count(array_filter(array_keys($array), 'is_numeric')) : 0;
	}
	
	function map_attr($k, $v) {
		return "$k=\"$v\"";
	}
	
	//配列をXML変換
	function &xml_serialize(&$data, $level = 0, $prior_key = NULL) {
		if($level == 0){ ob_start(); echo '<?xml version="1.0" ?>',"\n"; }
		while(list($key, $value) = each($data))
			if(!strpos($key, ' attr')) #if it's not an attribute
				#we don't treat attributes by themselves, so for an empty element
				# that has attributes you still need to set the element to NULL

				if(is_array($value) and array_key_exists(0, $value)){
					$this->xml_serialize($value, $level, $key);
				}else{
					$tag = $prior_key ? $prior_key : $key;
					echo str_repeat("\t", $level),'<',$tag;
					if(array_key_exists("$key attr", $data)){ #if there's an attribute for this element
						while(list($attr_name, $attr_value) = each($data["$key attr"]))
							echo ' ',$attr_name,'="',htmlspecialchars($attr_value),'"';
						reset($data["$key attr"]);
					}
					
					if(is_null($value)) echo " />\n";
					else if(!is_array($value)) echo '>',htmlspecialchars($value),"</$tag>\n";
					else echo ">\n",$this->xml_serialize($value, $level+1),str_repeat("\t", $level),"</$tag>\n";
				}
		reset($data);
		if($level == 0){ $str = &ob_get_contents(); ob_end_clean(); return $str; }
	}
}
?>