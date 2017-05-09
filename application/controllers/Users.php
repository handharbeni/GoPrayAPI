<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
foreach( scandir(FCPATH.'resources/src') as $dir)
{
	if ( ! str_replace( array('.','...') , '' , $dir))
	{
		continue;
	}

	require FCPATH.'resources/src/'.$dir;
}

use Restserver\Libraries\REST_Controller;
use GDText\Box;
use GDText\Color;

class Users extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->helper('file');

		$this->logdata = array(
				'method' => 'NOT_SET',
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'requestUri' => $_SERVER['REQUEST_URI']	
			);
	}

	public function index_get($option = '' , $action = '')
	{
		$this->logdata['method'] = 'GET';
		// $this->logdata['method'] = 'GET';
		// catatLog($this->logdata);

		$accessToken = $this->get('access_token');
		$q = $this->get('q');

		switch( trimLower($option))
		{
			case 'self':
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
						$this->db->where('key' , $accessToken);

						$query = $this->db->get('m_akun');
						$data = array();

						foreach($query->result() as $row)
						{
							$data[] = array(
									'nama' => $row->nama,
									'email' => $row->email,
									'profile_picture' => ( $row->profile_picture == null) ? 'null' : $row->profile_picture,
									'tanggal' => $row->tanggal,
									'key' => $row->key
								);
						}

						$result = array(
								'return' => true,
								'data' => $data
							);
					}
				}

				if ( $action != null)
				{
					switch ( trimLower($action)) {
						// Timeline section
						case 'timeline':
							$this->db->where('key' , $accessToken);

							$check = $this->db->get('m_akun');

							if ( ! $check->num_rows() > 0)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Access token salah atau tidak ditemukan!'
									);
							}
							else
							{
								$sql = "SELECT * FROM m_aktivitas , m_akun , t_timeline WHERE m_akun.key = '".$accessToken."' AND t_timeline.id_user = '".$check->result()[0]->id."' AND t_timeline.id_aktivitas = m_aktivitas.id ORDER BY t_timeline.tanggal DESC , t_timeline.jam DESC";

								$hsl = $this->db->query($sql)->result();

								$results = array();

								foreach($hsl as $num => $data)
								{
									$queryNew = "SELECT 
													m_aktivitas.nama as nama_aktivitas,
													m_aktivitas.nama_ibadah as nama_ibadah
												FROM t_timeline
												INNER JOIN m_aktivitas ON t_timeline.id_aktivitas = m_aktivitas.id";
									$aktivitas = $this->db->query($queryNew)->result();
									// $aktivitas = $this->db->query("SELECT m_aktivitas.nama AS nama_aktivitas , m_aktivitas.nama_ibadah AS nama_ibadah FROM m_aktivitas , t_timeline WHERE t_timeline.id_aktivitas = m_aktivitas.id")->result();

									$sQuery = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'gopray_api' AND table_name = 'm_".$hsl[$num]->prefix_table."'";

									$checkTable = $this->db->query($sQuery);
									$namaIbadah = "";
									switch ($checkTable->num_rows()) {
										case 0 :
											$namaIbadah = $data->nama_ibadah;
											break;
										case 1 :
										case 2 :									
										default:
											$ibadah = $this->db->get('m_'.$hsl[$num]->prefix_table)->result();
											$namaIbadah = $ibadah[0]->nama;
											break;
									}
									$results[] = array(
											'id_timeline' =>$data->id,
											'id_user' =>$data->id_user,
											'id_aktivitas' => $data->id_aktivitas,
											'id_ibadah' => $data->id_ibadah,
											'nama_aktivitas' => $data->nama_ibadah,
											'image' => $data->image,
											'ibadah' => $namaIbadah,
											'tempat' => $data->tempat,
											'bersama' => $data->bersama,
											'nominal' => $data->nominal,
											'point' => $data->point,
											'tanggal' => $data->tanggal,
											'jam' => $data->jam
										);									
								}

								$result = array(
 										'return' => true,
										'data' => $results
									);
							}
						break;

						// Message section
						case 'pesan':
							$this->db->where('key' , $accessToken);

							$check = $this->db->get('m_akun');

							if ( ! $check->num_rows() > 0)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Access token salah atau tidak ditemukan!'
									);
							}
							else
							{
								$sql = "SELECT t_closest_family.nama AS nama_kerabat , t_message.message AS pesan , t_message.tanggal , t_message.jam FROM  t_closest_family , t_message , m_akun WHERE m_akun.key = '".$accessToken."' AND t_message.id_user = '".$check->result()[0]->id."' AND t_closest_family.id_user = '".$check->result()[0]->id."' AND t_closest_family.id = t_message.id_kerabat AND t_message.id_user = t_closest_family.id_user";

								$query = $this->db->query($sql);

								if ( $query->num_rows() == 0)
								{
									$result = array(
											'return' => true,
											'data' => 'Data pesan masih kosong.'
										);
								}
								else
								{
									$result = array(
											'return' => true,
											'data' => $query->result()
										);
								}
							}
						break;

						// Kerabat section
						case 'kerabat':
							$this->db->where('key' , $accessToken);

							$check = $this->db->get('m_akun');

							if ( ! $check->num_rows() > 0)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Access token salah atau tidak ditemukan!'
									);
							}
							else
							{
								$this->db->where('id_user' , $check->result()[0]->id);

								$query = $this->db->get('t_closest_family');

								$results = array();

								foreach($query->result() as $data)
								{
									$results[] = array(
											'kerabat' => $data->kerabat,
											'nama' => $data->nama,
											'email' => $data->email,
											'gambar' => ( $data->gambar == null ) ? 'null' : $data->gambar,
											'no_hp' => $data->no_hp,
											'tanggal' => $data->tanggal,
											'jam' => $data->jam 
										);
								}

								$result = array(
										'return' => true,
										'data' => $results
									);
							}
						break;
					}
				}
			break;		

			case 'search':
				if ( ! $q)
				{
					$result = array( 
							'return' => false,
							'error_message' => 'Parameter q tidak boleh kosong'
						);
				}
				elseif( ! $accessToken)
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
						$this->db->like('nama' , $q);

						$query = $this->db->get('m_akun');
						$data = array();

						foreach($query->result() as $row)
						{
							$data[] = array(
									'nama' => $row->nama,
									'email' => $row->email,
									'profile_picture' => ( $row->profile_picture == null) ? 'null' : $row->profile_picture,
									'tanggal' => $row->tanggal,
									'key' => $row->key
								);
						}

						$result = array(
								'return' => true,
								'data' => $data
							);
					}
				}
			break;

			default:
				$this->db->where('id' , $option);

				if ( ! $this->db->get('m_akun')->num_rows() > 0)
				{
					$result = array( 
							'return' => false,
							'error_message' => 'Parameter tidak dikenal!'
						);
				}
				else
				{
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
							$this->db->where('id' , $option);

							$query = $this->db->get('m_akun');

							$data = array();

							foreach($query->result() as $row)
							{
								$data[] = array(
										'nama' => $row->nama,
										'email' => $row->email,
										'profile_picture' => ( $row->profile_picture == null) ? 'null' : $row->profile_picture,
										'tanggal' => $row->tanggal,
										'key' => $row->key
									);
							}

							$result = array(
									'return' => true,
									'data' => $data
								);
						}
					}
				}
			break;
		}

		$this->response($result);
	}

	public function index_post($option = '' , $action = '')
	{
		$this->logdata['method'] = 'POST';
		// $this->logdata['method'] = 'POST';
		// catatLog($this->logdata);

		$accessToken = $this->post('access_token');

		switch( trimLower($option))
		{
			case 'self':
				if ( $action != null)
				{
					switch( trimLower($action))
					{
						// Login section
						case 'login':
							$email = ( ! $this->post('email')) ? '' : $this->post('email');
							$password = ( ! $this->post('password')) ? '' : $this->post('password');

							if ( ! $email)
								{
								$result = array(
									'return' => false,
									'error_message' => 'Field email masih kosong'
									);
							}
							elseif( ! $password)
							{
								$result = array(
									'return' => false,
									'error_message' => 'Field password masih kosong'
									);
							}
							else
							{
								$this->db->where(array('email' => $email , 'password' => md5($password)));

								$query = $this->db->select( array('nama','email','profile_picture','key'))->from('m_akun')->get();

								if ($query->num_rows() > 0)
								{
									$result = array(
										'return' => true,
										'data' => $query->result()
										);
								}
								else
								{
									$result = array(
										'return' => false,
										'error_message' => 'Email atau password salah!'
										);
								}
							}
						break;

						case 'daftar':
							$nama = $this->post('nama');
							$email = $this->post('email');
							$password = $this->post('password');

							if ( ! $nama || ! $email || ! $password)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Field masih ada yang kosong.'
									);
							}
							else
							{
								$this->db->where('email' , $email);

								$query = $this->db->get('m_akun');

								if ( $query->num_rows() > 0)
								{
									$result = array(
											'return' => false,
											'error_message' => 'Email sudah digunakan!'
										);
								}
								else
								{
									$this->load->library('email');

									$config = array();
									$config['protocol'] = "";
									$config['smtp_host'] = "";
									$config['smtp_user'] = "";
									$config['smtp_pass'] = "";
									$config['useragent'] = "GoPray";
									$config['smtp_port'] = "465";
									$config['wordwrap'] = TRUE;
									$config['mailtype'] = "text";
									$config['newline'] = "\r\n";
									$config['charset'] = "utf-8";

									$this->email->initialize($config);
									// $this->load->library('email');

									// $config = array();
									// $config['protocol'] = "";
									// $config['smtp_host'] = "";
									// $config['smtp_user'] = "";
									// $config['smtp_pass'] = "";
									// $config['useragent'] = "GoPray";
									// $config['smtp_port'] = "465";
									// $config['wordwrap'] = TRUE;
									// $config['mailtype'] = "text";
									// $config['newline'] = "\r\n";
									// $config['charset'] = "utf-8";

									// $this->email->initialize($config);

									$data = array(
											'nama' => $nama,
											'email' => $email,
											'password' => md5($password),
											'key' => generate_key(),
											'profile_picture' => null,
											'tanggal' => date('Y-m-d H:i:s'),
											'verifikasi' => 'N'
										);

									$this->db->insert('m_akun' , $data);

							        // $this->email->from('reksarw@gmail.com', 'Reksa Rangga');
							        // $this->email->to('test@email.com');

							        // $this->email->subject('Email Test');
							        // $this->email->message('Testing the email class.');  

									$result = array(
										'return' => true,
										'data' => $data
										// 'debugger' => ( $this->email->send()) ? 'Email terkirim' : $this->email->print_debugger()
									);
								}
							}
						break;

						// insert/update kerabat section
						case 'kerabat':
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
								$metode = $this->post('metode');
								$kerabat = $this->post('kerabat');
								$nama = $this->post('nama');
								$gambar = $this->post('gambar');
								$noHp = $this->post('no_hp');
								$email = $this->post('email');

								if ( ! $accessToken || ! $kerabat || ! $nama || ! $noHp || ! $email || ! $metode || ! $gambar)
								{
									$result = array(
											'return' => false,
											'error_message' => 'Masih ada field yang kosong!'
										);
								}
								else
								{
									$listMetode = array('insert','update');

									if ( ! in_array($metode , $listMetode))
									{
										$result = array(
												'return' => false,
												'error_message' => 'Metode tidak diperbolehkan'
											);
									}
									else
									{
										$this->db->where('key' , $accessToken);

										$query = $this->db->get('m_akun');

										$data = array(
											'id_user' => $query->result()[0]->id,
											'kerabat' => $kerabat,
											'nama' => $nama,
											'email' => $email,
											'gambar' => $gambar,
											'no_hp' => $noHp,
											'tanggal' => date('Y-m-d'),
											'jam' => date('H:i:s')
										);

										if ( $metode == 'insert')
										{
											if ( $this->db->insert('t_closest_family' , $data))
											{
												$status = 'sukses';
											}
											else
											{
												$status = 'gagal';
											}
										}

										$result = array(
												'return' => ( $status == 'sukses') ? true : false,
												'status' =>  ( $status == 'sukses') ? 0 : 1
											);
									}
								}
							}
						break;

						case 'timeline':
							$id_aktivitas = $this->post('id_aktivitas');
							$id_ibadah = $this->post('id_ibadah');
							$tempat = $this->post('tempat');
							$bersama = $this->post('bersama');
							$gambar = $this->post('gambar');
							$point = $this->post('point');
							$tanggal = $this->post('tanggal');
							$jam = $this->post('jam');

							if ( ! $accessToken || ! $id_aktivitas || ! $id_ibadah || ! $tempat || ! $bersama || ! $gambar || ! $point)
							{
								$result = array(
									'return' => false,
									'error_message' => 'Masih ada field yang kosong!'
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
									$query = $this->db->get('m_akun');

									$data = array(
											'id_user' => $query->result()[0]->id,
											'id_aktivitas' => $id_aktivitas,
											'id_ibadah' => $id_ibadah,
											'tempat' => $tempat,
											'bersama' => $bersama,
											'image' => $gambar,
											'point' => $point,
											'tanggal' => $tanggal,
											'jam' => $jam
										);

									if ( $this->db->insert('t_timeline' , $data))
									{
										$status = 'sukses';
									}
									else
									{
										$status = 'gagal';
									}

									$result = array(
											'return' => true,
											'status' => $status
										);
								}
							}
						break;

						case 'meme':
							$this->db->where('key' , $accessToken);

							$akun = $this->db->get('m_akun');

							if ( ! $akun->num_rows() > 0 || ! $accessToken)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Access token salah atau tidak ditemukan!'
									);
							}
							else
							{
								$imagedir = FCPATH.'resources/images/';
								$images = glob($imagedir . '*.{jpg,jpeg,png,gif}',GLOB_BRACE);
								$randomimage = $images[array_rand($images)];

								$text = ( ! $this->post('text')) ? null : substr($this->post('text'),0,50);
								$gambar = ( ! $this->post('gambar')) ? $randomimage : $this->post('gambar');
								$mime = get_mime_by_extension($gambar);
								$mimeAccepted = array('image/jpeg' ,'image/png');

								if ( ! $this->post('text') || $text == null)
								{
									$result = array(
											'result' => false,
											'error_message' => 'Field text tidak boleh kosong!'
										);
								}
								elseif( ! in_array($mime,$mimeAccepted))
								{
									$result = array(
											'result' => false,
											'error_message' => 'Gambar hanya boleh berekstensi JPG/PNG'
										);
								}
								else
								{
									$this->textMeme = $text;
									$this->gambarMeme = ($mime == 'image/png') ? imagecreatefrompng($gambar) : imagecreatefromjpeg($gambar);

									/* Create meme */
									$box = new Box($this->gambarMeme);
									$box->setFontSize(100);
									$box->setFontFace(FCPATH.'resources/fonts/arial.ttf');
									$box->setFontColor(new Color(255, 255, 255));
									$box->setTextShadow(
									    new Color(0, 0, 0, 80),
									    0,
									    -1
									);

									$box->setBox(
									    0,
									    0,
									    imagesx($this->gambarMeme),
									    imagesy($this->gambarMeme)
									);

									// $box->setBackgroundColor(new Color(0,0,0,80));
									$box->setTextAlign('center', 'center');
									$box->draw($this->textMeme);
									/* Create meme */

									/* GoPray Watermark */
									$box = new Box($this->gambarMeme);
									$box->setFontSize(100);
									$box->setFontFace(FCPATH.'resources/fonts/arial.ttf');
									$box->setFontColor(new Color(255, 255, 255)); 
									$box->setBox(
									    -60,
									    -60,
									    imagesx($this->gambarMeme),
									    imagesy($this->gambarMeme)
									);

									$box->setTextAlign('right','bottom');
									$box->draw('GoPray');

									/* GoPray Watermark */

									$upload_dir = FCPATH.'resources/';

									if ( ! is_dir($upload_dir.'uploads'))
									{
										mkdir(FCPATH.'uploads/');
										@chmod ( FCPATH.'uploads/' , 0754);
									}

									$filenameUpload = $upload_dir.'uploads/'.generate_image($this->gambarMeme).'png';
									imagepng($this->gambarMeme, $filenameUpload, 9);
									imagedestroy($this->gambarMeme);

									/* image filename */
									$x = explode('/' , $filenameUpload);
									$count = count($x) - 1;
									/* image filename */

									$data = array(
											'id_user' => $akun->result()[0]->id,
											'path_meme' => $x[$count],
											'tanggal' => date('Y-m-d'),
											'jam' => date('H:i:s')
										);

									if ( $this->db->insert('t_meme' , $data))
									{
										$status = 'Meme berhasil dibuat!';
									}
									else
									{
										$status = 'Meme gagal dibuat!';
									}

									$query = $this->db
									->select( array('path_meme','tanggal','jam'))
									->from('t_meme')
									->where( array('id_user' => $akun->result()[0]->id))
									->order_by('tanggal DESC','jam DESC')
									->get();

									$result = array(
											'return' => true,
											'status' => $status,
											'data_meme' => $query->result()
										);
								}
							}
						break;

						default:
							$result = array(
									'return' => false,
									'error_message' => 'Metode tidak diperbolehkan!'
								);
						break;
					}
				}
			break;

			default:
				$result = array(
						'return' => false,
						'error_message' => 'Metode tidak ditemukan!'
					);
			break;
		}

		$this->response($result);
	}

}

/* End of file Users.php */
/* Location: ./application/controllers/Users.php */