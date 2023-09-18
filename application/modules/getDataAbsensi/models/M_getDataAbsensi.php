<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_getDataAbsensi extends CI_model {
	public function viewData(){
		$query = $this->db->query("SELECT data_id, data_name, data_txt, data_user, data_date, data_address, c.department_name, d.division_name
						 FROM m_data a 
						 LEFT JOIN m_user b ON a.data_user = b.user_id
						 LEFT JOIN m_department c ON b.user_department_id = c.department_id
						 LEFT JOIN m_division d ON b.user_division_id = d.division_id
						 WHERE a.data_id = 'getdataabs'");

		$data = $query->row();

		if (!empty($data)) {
			return $query->result();
		} 
		else {
			return FALSE;
		}
	}

	public function processData($inDate1,$inDate2){
		$res = array();

		$conn2 = [
			'dsn'	=> '',
			'hostname' => '192.168.10.75',
			'username' => 'user',
			'password' => 'user@MMP',
			'database' => 'lks_fingerprint',
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => (ENVIRONMENT !== 'production'),
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'save_queries' => TRUE,
			'port'     => 3306,
		];

		$db2 = $this->load->database($conn2, TRUE);

		$conn3 = [
			'dsn'	=> '',
			'hostname' => '192.168.11.4',
			'username' => 'root',
			'password' => '**asdf123',
			'database' => 'personaliammp',
			'dbdriver' => 'mysqli',
			'dbprefix' => '',
			'pconnect' => FALSE,
			'db_debug' => (ENVIRONMENT !== 'production'),
			'cache_on' => FALSE,
			'cachedir' => '',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt' => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'save_queries' => TRUE,
			'port'     => 3306,
		];

		$db3 = $this->load->database($conn3, TRUE);

		$inDate1 = date("Y/m/d", strtotime($inDate1));
		$inDate2 = date("Y/m/d", strtotime($inDate2));

		$begin = new DateTime($inDate1);
		$end = new DateTime($inDate2. "+1 days");
		$interval = DateInterval::createFromDateString('1 day');
		
		$period = new DatePeriod($begin, $interval, $end);
		
		foreach ($period as $dt) {
			$datex = $dt->format("Y/m/d");
			$datey = new DateTime($datex. "+1 days");
			$datey = $datey->format("Y/m/d");
			
			$sql = "SELECT 
					dt1.card_id AS cardID,
					dt2.date_ku AS dateIN,
					dt2.time_ku AS timeIN,
					dt2.waktu AS waktuIN,
					dt2.machine_id AS machine_idIN, 
					CASE
						WHEN dt2.waktu < dt3.waktu THEN dt3.date_ku 
						WHEN dt2.waktu > dt3.waktu THEN dt4.date_ku
						WHEN ISNULL(dt3.waktu) THEN dt4.date_ku
						WHEN (ISNULL(dt2.waktu) AND !ISNULL(dt3.waktu)) AND dt3.time_ku > '09:00:00' THEN dt3.date_ku
						ELSE ''
					END AS dateOUT,
					CASE
						WHEN dt2.waktu < dt3.waktu THEN dt3.time_ku 
						WHEN dt2.waktu > dt3.waktu THEN dt4.time_ku
						WHEN ISNULL(dt3.waktu) THEN dt4.time_ku 
						WHEN (ISNULL(dt2.waktu) AND !ISNULL(dt3.waktu)) AND dt3.time_ku > '09:00:00'  THEN dt3.time_ku
						ELSE ''
					END AS timeOUT,
					CASE
						WHEN dt2.waktu < dt3.waktu THEN dt3.waktu 
						WHEN dt2.waktu > dt3.waktu THEN dt4.waktu
						WHEN ISNULL(dt3.waktu) THEN dt4.waktu 
						WHEN (ISNULL(dt2.waktu) AND !ISNULL(dt3.waktu)) AND dt3.time_ku > '09:00:00' THEN dt3.waktu
						ELSE ''
					END AS waktuOUT,
					CASE
						WHEN dt2.waktu < dt3.waktu THEN dt3.machine_id 
						WHEN dt2.waktu > dt3.waktu THEN dt4.machine_id
						WHEN ISNULL(dt3.waktu) THEN dt4.machine_id 
						WHEN (ISNULL(dt2.waktu) AND !ISNULL(dt3.waktu)) AND dt3.time_ku > '09:00:00' THEN dt3.machine_id
						ELSE ''
					END AS machine_idOUT,
					dt2.*, dt3.*, dt4.* 
					FROM (
						SELECT card_id FROM lks_logs_csv 
						WHERE 
						date_ku = '".$datex."' AND
						fkey IN (0,1) 
						GROUP BY card_id
					)dt1
					LEFT JOIN (
						SELECT card_id, date_ku, MAX(time_ku) AS time_ku, waktu, machine_id FROM lks_logs_csv 
						WHERE 
						date_ku = '".$datex."' AND
						fkey IN (0) 
						GROUP BY card_id
					)dt2 
					ON dt1.card_id  = dt2.card_id 
					LEFT JOIN (
						SELECT card_id, date_ku, MIN(time_ku) AS time_ku, waktu, machine_id FROM lks_logs_csv 
						WHERE 
						date_ku = '".$datex."' AND
						fkey IN (1) 
						GROUP BY card_id
					)dt3 
					ON dt1.card_id  = dt3.card_id
					LEFT JOIN (
						SELECT card_id, date_ku, MIN(time_ku) AS time_ku, waktu, machine_id FROM lks_logs_csv 
						WHERE 
						date_ku = '".$datey."' AND
						fkey IN (1) 
						GROUP BY card_id
					)dt4 
					ON dt1.card_id  = dt4.card_id";

			$query = $db2->query($sql);	
			
			foreach ($query->result() as $data){

				// $res["test"] .= $data->waktuIN;
				$card_id = $data->cardID;
				$card_id = sprintf("%06d",$card_id);
				$waktuIN = $data->waktuIN;
				$machine_idIN = $data->machine_idIN;

				$datax = array(
								'Tgl' => $waktuIN,
								'Nip' => $card_id,
								'Mesin' => $machine_idIN
							);

				$res['datax'] = $datax;
			
				$db3->db_debug = false;
			
				if($db3->insert('absensiraw', $datax)){
					$res['res'] = 'success';
				}
				else {
					$res['err'] =  $db3->error();
					// $res['err'] = $res['res']['message'];
				}

				$waktuOUT =  $data->waktuOUT;
				$machine_idOUT =  $data->machine_idOUT;

				$datay = array(
								'Tgl' => $waktuOUT,
								'Nip' => $card_id,
								'Mesin' => $machine_idOUT
							);

				$db3->db_debug = false;

				if($db3->insert('absensiraw', $datay)){
					$res['res'] = 'success';
				}
				else {
					$res['err'] =  $db3->error();
					// $res['err'] = $res['res']['message'];
				}
			}

		}

		$dataz = array(
			'data_name' => $this->session->userdata('user_name'),
			'data_txt' => date("d/m/Y", strtotime($inDate1))." - ".date("d/m/Y", strtotime($inDate2)),
			'data_user' => $this->session->userdata('user_id'),
			'data_date' => date("Y-m-d H:i:s"),
			'data_address' => gethostbyaddr($_SERVER['REMOTE_ADDR'])." - ".$_SERVER['REMOTE_ADDR']
		);

		$this->db->where("data_id", "getdataabs");
		$this->db->update("m_data",$dataz);

		// $res['res'] = $res;
		echo json_encode($res);
	}
}