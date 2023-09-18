<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_data extends CI_Controller {
	public function __construct() {        
        parent::__construct();

        //cek session login
        if($this->session->userdata("user_id") == "") {
            redirect('index.php/login/C_login');
        }

    }

	public function index(){
		$this->load->view('V_header');
		$this->load->view('V_navbar');
		$this->load->view('V_content');
		$this->load->view('V_footer');
	}
}