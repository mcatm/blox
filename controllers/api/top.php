<?php

class Top extends Controller {
	
	function Index() {
		header('location:'.base_url());
	}
	
	function Top() {
		parent::Controller();
	}
}