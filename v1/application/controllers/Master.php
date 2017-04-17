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
		$this->logdata['method'] = 'GET';
		catatLog($this->logdata);

		$accessToken = $this->get('access_token');
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
					$result = array(
						'return' => true,
						'data' => $query->result()
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
					$result = array(
						'return' => true,
						'data' => $query->result()
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
				if ( ! $accessToken)
				{
					$result = array(
						'return' => false,
						'error_message' => 'Access token tidak valid!'
						);	
				}
				else
				{
					$this->db->where('key' , $accessToken);
					
					if ( ! $this->db->get('m_akun')->num_rows() > 0)
					{
						$result = array(
								'return' => false,
								'error_message' => 'Access token salah atau tidak ditemukan!'
							);
					}
					else
					{
						$this->db->where('tanggal >=', date('Y-m-d'));
						
						$query = $this->db->select( array('tanggal','subuh','dhuha','dhuhur','ashar','maghrib','isya'))->from('t_jadwal_sholat')->get();

						$result = array(
								'return' => true,
								'data' => $query->result()
							);
					}
				}
			break;

			default:
				$result = array(
						'return' => false,
						'error_message' => 'Opsi tidak ditemukan!'
					);
			break;
		}

		$this->response($result);
	}

}

/* End of file Master.php */
/* Location: ./application/controllers/Master.php */