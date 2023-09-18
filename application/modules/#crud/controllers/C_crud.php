<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_crud extends CI_Controller {
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

	public function viewData(){
		$this->load->model("M_crud");
		$data = $this->M_crud->viewData();
		$i = 0;

		echo '<table id="example1" class="table table-bordered table-striped">
				<thead style="text-align: center;">
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Email</th>
					<th>Create</th>
				</tr>
				</thead>
				<tbody>';

		foreach($data as $row){
			$i++;
			echo '<tr>';
				echo '<td style="text-align: center;">'.$row->user_id.'</td>';
				echo '<td>'.$row->user_name.'</td>';
				echo '<td>'.$row->user_email.'</td>';
				echo '<td style="text-align: center;">'.$row->user_created_at.'</td>';
			echo '</tr>';
		}

		echo 	'</tbody>
			</table>';
	}

	public function viewInput(){
		$this->load->view('V_input');
		// echo 	'<form id="quickForm">
		// 			<div class="card-body">
		// 				<div class="form-group">
		// 					<label for="exampleInputEmail1">Email address</label>
		// 					<input type="email" name="email" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
		// 				</div>
		// 				<div class="form-group">
		// 					<label for="exampleInputPassword1">Password</label>
		// 					<input type="password" name="password" class="form-control" id="exampleInputPassword1" placeholder="Password">
		// 				</div>
		// 			</div>
		// 			<!-- /.card-body -->
		// 			<div class="card-footer">
		// 				<button type="submit" class="btn btn-primary">Submit</button>
		// 			</div>
		// 		</form>';
	}
}