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
		// $this->logdata['method'] = 'GET';
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


				// Stiker section
				case 'stiker':
					$dataUser = $check->result();

					$masterStiker = $this->db->order_by('tanggal DESC','jam DESC')->get('m_stiker');

					$results = array();

					foreach($masterStiker->result() as $data)
					{
						$checkPayment = $this->db->select( array('kd_stiker','status_payment'))->from('t_avail_stiker')->where( array('kd_user' => $dataUser[0]->id,'kd_stiker' => $data->id))->get();

						$childStiker = $this->db->select( array('stiker','nomer','tanggal','jam'))->from('t_stiker')->where ( array('kd_stiker' => $data->id))->get();

						$results[] = array(
								'nama_stiker' => $data->nama,
								'cover' => $data->cover,
								'harga' => $data->price,
								'tanggal' => $data->tanggal,
								'jam' => $data->jam,
								'payment' => ( $checkPayment->num_rows() == 0) ? '0' : $checkPayment->result()[0]->status_payment,
								'items' => ( $childStiker->num_rows() == 0) ? 'null' : $childStiker->result()
 							);
					}

					$result = array(
							'return' => true,
							'total_stiker' => $masterStiker->num_rows(),
							'data' => $results
						);
				break;

				// Paket stiker section
				case 'paketstiker':
					$query = $this->db->get('m_paket_stiker');

					$results = array();

					foreach($query->result() as $data)
					{
						$queryPaketStiker = $this->db->get_where('t_paket_stiker' , array('kd_paket_stiker' => $data->id))->result();

						$listStiker = array();

						foreach($queryPaketStiker as $dataPaketStiker)
						{
							$dataStiker = $this->db->select( array('nama','cover','price','tanggal','jam'))->from('m_stiker')->where ( array('id' => $dataPaketStiker->kd_stiker))->get();

							$listStiker[] = $dataStiker->result()[0];
						}

						$results[] = array(
							'nama_paket' => $data->name,
							'harga' => $data->price,
							'tanggal' => $data->tanggal,
							'jam' => $data->jam,
							'list_stiker' => $listStiker,
						);
					}

					$result = array(
							'return' => true,
							'data' => $results
						);
				break;

				case 'meme':
					$query = $this->db
					->select( array('path_meme','tanggal','jam'))
					->from('t_meme')
					->where( array('id_user' => $check->result()[0]->id))
					->order_by('tanggal DESC','jam DESC')
					->get();

					if ( $query->num_rows() == 0)
					{
						$data = 'Data masih kosong!';
					}
					else
					{
						$data = $query->result();
					}

					$result = array(
							'return' => true,
							'data' => $data
						);
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