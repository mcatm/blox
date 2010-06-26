<?php

class Top extends Controller {

	function index() {//RSS出力
		exit('UUU');
	}
	
	function Top() {
		parent::Controller();
		define('HOME_MODE', true);//ホーム画面モード
	}
}