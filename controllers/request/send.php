<?php

class Send extends Controller {
	
	function mail() {
		$this->load->library('mail');
		#exit('jsjhfdka');
		$this->mail->set();
		
		$redirect = ($this->input->post('redirect')) ? $this->input->post('redirect') : base_url();
		header('location:'.$redirect);
	}
	
	function index() {
		header('location:'.base_url());
	}
	
	function Send() {
		parent::Controller();
	}
}