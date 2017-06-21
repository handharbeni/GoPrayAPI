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
		
		$this->keyMuslimSalat = "ffab61dcf338b971ae323f12520497f4";
	}

	public function index_get($option = '')
	{
		// $this->logdata['method'] = 'GET';
		// catatLog($this->logdata);

		$accessToken = $this->get('access_token');

		$checkUser = $this->db->get_where('m_akun', array('key' => $accessToken));
		$checkFamily = $this->db->get_where('m_family', array('key' => $accessToken));

		if ( ! $accessToken)
		{
			$result = array(
					'return' => false,
					'error_message' => 'Access token tidak valid!'
				);
		}
		elseif ( ! $checkUser->num_rows() > 0 && ! $checkFamily->num_rows() > 0)
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
					$dataUser = $checkUser->result();

					$masterStiker = $this->db->order_by('tanggal DESC','jam DESC')->get('m_stiker');

					$results = array();

					foreach($masterStiker->result() as $data)
					{
						$checkPayment = @$this->db->select( array('kd_stiker','status_payment'))->from('t_avail_stiker')->where( array('kd_user' => $dataUser->id,'kd_stiker' => $data->id))->get();

						$childStiker = @$this->db->select( array('id','stiker','nomer','tanggal','jam'))->from('t_stiker')->where ( array('kd_stiker' => $data->id))->get();

						$results[] = array(
								'id_stiker' => $data->id,
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
							'id_paket' => $data->id,
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
					->where( array('id_user' => $checkUser->result()[0]->id))
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

				case 'pesan':
					$requestFrom = ($checkUser->num_rows() > 0) ? $checkUser->result()[0] : $checkFamily->result()[0]; 

					$final = array();

					if ( $checkUser->num_rows() > 0)
					{
						$self = $requestFrom;
						$sql = "select m_akun.id AS id_user , m_akun.nama , m_akun.email , m_akun.key , m_akun.profile_picture ,
								t_message.id AS id_pesan , t_message.id_kerabat , t_message.message , t_message.gambar , t_message.tanggal AS tgl_pesan , t_message.jam AS jam_pesan
						 		from m_akun inner join t_closest_family on t_closest_family.id_user = m_akun.id
								inner join t_message on t_message.id_kerabat = t_closest_family.id_kerabat
								where m_akun.key = '".$self->key."' ORDER BY t_message.tanggal , t_message.jam";

						$query = $this->db->query($sql)->result();

						foreach($query as $row)
						{
							$kerabat = $this->db->get_where('m_family' , array(
									'id' => $row->id_kerabat
								))->result()[0];

							$final[] = array(
									'id_pesan' => $row->id_pesan,
									'id_user' => $row->id_user,
									'kerabat' => array(
											'id_kerabat' => $row->id_kerabat,
											'hubungan' => $kerabat->kerabat,
											'nama' => $kerabat->nama,
											'email' => $kerabat->email,
											'no_hp' => $kerabat->no_hp,
											'foto' => $kerabat->gambar,
											'terdaftar' => $kerabat->tanggal.' '.$kerabat->jam
										),
									'pesan' => $row->message,
									'gambar' => $row->gambar,
									'type' => ( $row->gambar == "nothing") ? 1 : 2,
									'tanggal' => $row->tgl_pesan,
									'jam' => $row->jam_pesan
								);
						}
					}
					
					if ( $checkFamily->num_rows() > 0)
					{	
						$self = $requestFrom;
						$sql = "SELECT * FROM m_family";
						$sql.= " WHERE child IN (".$self->child.")";

						$query = $this->db->query($sql);
						$x = $query->num_rows() - 1;

						$id_kerabat = null;

						foreach($query->result() as $num => $row)
						{
							if ( $num == $x )
							{
								$id_kerabat .= $row->id;
							}
							else
							{
								$id_kerabat .= $row->id.',';
							}
						}

						$selectMessage = "SELECT * FROM t_message";
						$selectMessage.= " WHERE id_kerabat IN (".$id_kerabat.")";
						$selectMessage.= " ORDER BY tanggal DESC , jam DESC";
						
						$x = $this->db->query($selectMessage)->result();

						foreach($x as $row)
						{
							$kerabat = $this->db->get_where('m_family' , array(
									'id' => $row->id_kerabat
								))->result()[0];

							$final[] = array(
									'id_pesan' => $row->id,
									'id_user' => $row->id_user,
									'kerabat' => array(
											'id_kerabat' => $row->id_kerabat,
											'hubungan' => $kerabat->kerabat,
											'nama' => $kerabat->nama,
											'email' => $kerabat->email,
											'no_hp' => $kerabat->no_hp,
											'foto' => $kerabat->gambar,
											'terdaftar' => $kerabat->tanggal.' '.$kerabat->jam
										),
									'pesan' => $row->message,
									'gambar' => $row->gambar,
									'type' => ( $row->gambar == "nothing") ? 1 : 2,
									'tanggal' => $row->tanggal,
									'jam' => $row->jam
								);
						}
					}

					$result = array(
							'return' => true,
							'data' => $final,
						);
				break;

				case 'periode':
					$me = $checkFamily->result()[0];

					if ( ! $this->get('parameter'))
					{
						$result = array(
								'return' => false,
								'error_message' => 'Masih ada parameter kosong!'
							);
					}
					else
					{
						if ( $checkFamily->num_rows() > 0)
						{
							$x = str_replace( array('[',']') , '' , $this->get('parameter'));
							$x = explode(":" , $x);

							$this->user_id = $x[0];
							$this->bulan = $x[1];
							$this->tahun = $x[2];

							$start = $this->tahun.'-'.$this->bulan.'-1';
							$end = $this->tahun.'-'.$this->bulan.'-31';

							$sql = "SELECT SUM(point) AS point FROM t_timeline";
							$sql.= " WHERE id_user = ".$this->user_id."";
							$sql.= " AND tanggal BETWEEN '".$start."' AND '".$end."'";

							$query = $this->db->query($sql)->result()[0];

							$result = array(
									'return' => true,
									'point' => $query->point,
								);
						}
						else
						{
							$result = array(
									'return' => false,
									'error_message' => 'Access token orang tua dibutuhkan!'
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
		}

		$this->response($result);
	}

	public function index_post($option = '')
	{
		switch( trimLower($option))
		{
			case 'paketstiker':
				$nama = $this->post('nama_paket');
				$harga = $this->post('harga');
				$tanggal = date('Y-m-d');
				$waktu = date('H:i:s');

				if ( ! $nama || ! $harga)
				{
					$result = array(
							'return' => false,
							'error_message' => 'Parameter nama dan harga harus diisi.'
						);
				}
				else
				{
					$data = array(
							'name' => $nama,
							'price' => $harga,
							'tanggal' => $tanggal,
							'jam' => $waktu
						);

					$this->db->insert('m_paket_stiker' , $data);

					$result = array(
							'return' => true,
							'message' => 'Paket stiker berhasil ditambahkan.'
						);
				}
			break;

			case 'stiker':
				$nama = $this->post('nama_stiker');
				$harga = $this->post('harga');
				$tanggal = date('Y-m-d');
				$waktu = date('H:i:s');

				if ( ! $nama || ! $harga || ! $_FILES['cover'])
				{
					$result = array(
							'return' => false,
							'error_message' => 'Paramater masih ada yang kosong!'
						);
				}
				else
				{
					$stikerdir = FCPATH.'resources/stiker/';
					$x = explode("." , $_FILES['cover']['name']);

					$fileEncrypt = generate_image($_FILES['cover']['name']);

					$fileName = $stikerdir.$fileEncrypt.'.'.end($x);
					$_FILES['cover'] ? move_uploaded_file($_FILES['cover']['tmp_name'], $fileName) : null;

					$path = $_FILES['cover'] ? $fileEncrypt.'.'.end($x) : 'default.jpg';

					$data = array(
							'nama' => $nama,
							'cover' => base_url("resources/stiker/".$path),
							'price' => $harga,
							'tanggal' => $tanggal,
							'jam' => $waktu
						);

					$this->db->insert('m_stiker' , $data);
					
					$result = array(
							'return' => true,
							'message' => 'Stiker berhasil ditambah!'
						);	
				}
			break;

			case 'childstiker':
				$kd_stiker = $this->post('kd_stiker');
				$gambar = $_FILES['gambar'];
				$nomer = $this->post('nomer');
 
				if ( ! $gambar || ! $kd_stiker || ! $nomer)
				{
					$result = array(
							'return' => false,
							'error_message' => 'Parameter masih ada yang kosong!'
						);
				}
				else
				{
					$stikerdir = FCPATH.'resources/stiker/';

					$x = explode("." , $_FILES['gambar']['name']);

					$fileEncrypt = generate_image($_FILES['gambar']['name']);

					$fileName = $stikerdir.$fileEncrypt.'.'.end($x);
					$_FILES['gambar'] ? move_uploaded_file($_FILES['gambar']['tmp_name'], $fileName) : null;

					$path = $_FILES['gambar'] ? $fileEncrypt.'.'.end($x) : 'default.jpg';

					$data = array(
							'kd_stiker' => $kd_stiker,
							'stiker' => base_url("resources/stiker/".$path),
							'nomer' => $nomer,
							'tanggal' => date('Y-m-d'),
							'jam' => date('H:i:s')
						);

					$this->db->insert('t_stiker' , $data);

					$result = array(
							'return' => true,
							'message' => 'Berhasil ditambahkan!'
						);
				}
			break;

			case 'pesan':
				$accessToken = $this->post('access_token');

				$checkUser = $this->db->get_where('m_family', array('key' => $accessToken));
				if ( ! $accessToken)
				{
					$result = array(
							'return' => false,
							'error_message' => 'Access token tidak valid!'
						);
				}
				elseif ( ! $checkUser->num_rows() > 0)
				{
					$result = array(
							'return' => false,
							'error_message' => 'Access token salah atau tidak ditemukan!'
						);
				}else
				{
					$postdata = array(
							'pesan' => $this->post('pesan'),
							'gambar' => $_FILES['gambar']
						);

					if ( ! $postdata['pesan'])
					{
						$result = array(
								'return' => false,
								'error_message' => 'Masih ada parameter kosong!!'
							);
					}
					else
					{
						$this->pesan = $postdata['pesan'];

						if ( $_FILES['gambar'])
						{
							$upload_dir = FCPATH.'resources/uploads/';

							$x = explode("." , $_FILES['gambar']['name']);

							$fileEncrypt = generate_image($_FILES['gambar']['name']);

							$fileName = $upload_dir.$fileEncrypt.'.'.end($x);
							$_FILES['gambar'] ? move_uploaded_file($_FILES['gambar']['tmp_name'], $fileName) : null;

							$path = $fileEncrypt.'.'.end($x);
						}

						$data = array(
								'id_kerabat' => $checkUser->result()[0]->id,
								'id_user' => 0,
								'message' => ( ! $this->pesan) ? null : $this->pesan,
								'gambar' => ( $_FILES['gambar']) ? base_url("resources/uploads/".$path) : 'nothing',
								'tanggal' => date('Y-m-d'),
								'jam' => date('H:i:s')
							);

						$this->db->insert('t_message' , $data);

						$result = array(
								'return' => true,
								'message' => 'Berhasil ditambahkan!'
							);
					}
				}
			break;

			case 'jadwalsholat':
				$method = $this->post('method');

				$listMethod = array('monthly','yearly');

				if ( ! in_array($method,$listMethod))
				{
					$result = array(
							'return' => false,
							'error_message' => 'Metode tidak ditemukan!'
						);
				}
				else
				{
					$this->load->library('curl');

					$uri = "http://muslimsalat.com";
					$path = ( $method == 'monthly') ? '/jakarta/monthly.json' : '/jakarta/yearly.json';
					$path .= "?key=".$this->keyMuslimSalat;

					$dataGet = $this->curl->simple_get($uri.$path);

					$fromResource = json_decode($dataGet);

					foreach($fromResource->items as $row)
					{
						$x = $this->db
						->get_where('t_jadwal_sholat' , array('tanggal' => $row->date_for));

						if ( $x->num_rows() == 0)
						{
							$data = array(
									'id_jadwal' => 1,
									'tanggal' => $row->date_for,
									'subuh' => ampm_to_24($row->fajr),
									'dhuha' => ampm_to_24($row->shurooq),
									'dhuhur' => ampm_to_24($row->dhuhr),
									'ashar' => ampm_to_24($row->asr),
									'maghrib' => ampm_to_24($row->maghrib),
									'isya' => ampm_to_24($row->isha),
								);

							$this->db->insert('t_jadwal_sholat' , $data);
						}
					}

					$result = array(
						'return' => true,
						'status' => 'Sukses Sinkron'
					);
				}
			break;
		}

		$this->response($result);
	}

}

/* End of file Master.php */
/* Location: ./application/controllers/Master.php */