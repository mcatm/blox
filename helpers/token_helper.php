<?php if ( ! defined( 'BASEPATH' ) ) exit( 'No direct script access allowed' );

if ( ! function_exists( 'make_token' ) ) {
	function make_token($length = 8) {
		srand((double)microtime() * 54234853);//乱数表のシードを決定
	
		//パスワード文字列の配列を作成
		$pwelemstr = "abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345679";
		$pwelem = preg_split("//", $pwelemstr, 0, PREG_SPLIT_NO_EMPTY);
		
		$token = "";
		for ($i=0; $i<$length; $i++ ) {
			$token .= $pwelem[array_rand($pwelem, 1)];//パスワード文字列を生成
		}
		
		return $token;
	}
}


?>
