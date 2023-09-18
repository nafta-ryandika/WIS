<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_crud extends CI_model {
	public function viewData(){
		$this->db->select("*");
		$this->db->from("m_user");		
		$query = $this->db->get();
		$data = $query->row();

		if (!empty($data)) {
			return $query->result();
		} 
		else {
			return FALSE;
		}
	}
}
