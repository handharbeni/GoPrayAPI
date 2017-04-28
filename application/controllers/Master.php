<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Master extends REST_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->logdata = array(
				'method' => 'NOT_SET',
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'requestUri' => $_SERVER['REQUEST_URI']	
			);
	}

	public function index_get($option = '')
	{
<<<<<<< HEAD:v1/application/controllers/Master.php
		$this->logdata['method'] = 'GET';
=======
		// $this->logdata['method'] = 'GET';
>>>>>>> 0f67a3fa910d2bded1b7d895bf4d148b6032c378:application/controllers/Master.php
		// catatLog($this->logdata);

		$accessToken = $this->get('access_token');

		$this->db->where('key' , $accessToken);

		$check = $this->db->get('m_akun');

		if ( ! $accessToken)
		{
			$result = array(
					'return' => false,
					'error_message' => 'Access token tidak valid!'
				);
		}
		elseif ( ! $check->num_rows() > 0)
		{
			$result = array(
					'return' => false,
					'error_message' => 'Access token salah atau tidak ditemukan!'
				);
		}else
		{
			switch( trimLower($option))
			{
				case 'sholat':
					$query = $this->db->get('m_sholat');

					if ( $query->num_rows() == 0)
					{
						$result = array(
							'return' => false,
							'error_message' => 'Data sholat masih kosong.'
							);
					}
					else
					{
						$results = array();

						foreach($query->result() as $num => $data)
						{
							$results[] = array(
									'nama_sholat' => $data->nama,
									'max_point' => $data->max_point
								);
						}

						$result = array(
							'return' => true,
							'data' => $results
							);
					}
				break;

				// Puasa section
				case 'puasa':
					$query = $this->db->get('m_puasa');

					if ( $query->num_rows() == 0)
					{
						$result = array(
							'return' => false,
							'error_message' => 'Data puasa masih kosong.'
							);
					}
					else
					{
						$results = array();

						foreach($query->result() as $num => $data)
						{
							$results[] = array(
									'nama_puasa' => $data->nama,
									'max_point' => $data->max_point
								);
						}

						$result = array(
							'return' => true,
							'data' => $results
							);
					}
				break;

				// Doa section
				case 'doa':
					$query = $this->db->get('m_doa');

					if ( $query->num_rows() == 0)
					{
						$result = array(
							'return' => false,
							'error_message' => 'Data doa masih kosong.'
							);
					}
					else
					{
						$results = array();

						foreach($query->result() as $num => $data)
						{
							$results[] = array(
									'nama_doa' => $data->nama,
									'max_point' => $data->max_point
								);
						}

						$result = array(
							'return' => true,
							'data' => $results
							);
					}
				break;

				// Aktivitas section
				case 'aktivitas':
					$query = $this->db->get('m_aktivitas');

					if ( $query->num_rows() == 0)
					{
						$result = array(
							'return' => false,
							'error_message' => 'Data aktivitas masih kosong.'
							);
					}
					else
					{
						$result = array(
							'return' => true,
							'data' => $query->result()
							);
					}
				break;

				// Jadwal sholat section
				case 'jadwalsholat':
					$tz = $this->get('timezone');

					$query = $this->db->get_where('m_jadwal_sholat' , array('timezone' => $tz));

					if ( $query->num_rows() == 0 || ! $tz)
					{
						$result = array(
								'return' => false,
								'error_message' => 'Timezone tidak ada!' 
							);
					}
					else
					{
						$jadwal = $this->db->select( array('tanggal','subuh','dhuha','dhuhur','ashar','maghrib','isya'))->from('t_jadwal_sholat')->get();

						$result = array(
							'return' => true,
							'title' => $query->result()[0]->title,
							'type' => $query->result()[0]->type,
							'method' => $query->result()[0]->method,
							'method_name' => $query->result()[0]->method_name,
							'daylight' => $query->result()[0]->daylight,
							'timezone' => $query->result()[0]->timezone,
							'mapimage' => $query->result()[0]->mapimage,
							'items' => ( $jadwal->num_rows() == 0) ? 'Jadwal belum ada!' : $jadwal->result()  
						);
					}
				break;

				default:
					$result = array(
							'return' => false,
							'error_message' => 'Opsi tidak ditemukan!'
						);
				break;
			}
		}

		$this->response($result);
	}

}

/* End of file Master.php */
/* Location: ./application/controllers/Master.php */