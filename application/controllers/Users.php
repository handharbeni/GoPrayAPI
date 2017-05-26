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
						// Profpic section
						case 'meme' :
							$this->db->where('key' , $accessToken);
							$check = $this->db->get('m_akun');
							if ( ! $check->num_rows() > 0){
								$result = array(
										'return' => false,
									);
							}else{
								$query = $this->db
								->select( array('id','path_meme','tanggal','jam'))
								->from('t_meme')
								->where( array('id_user' => $check->row()->id))
								->order_by('id DESC','jam DESC')
								->get();

								$result = array(
										'return' => true,
										'data' => $query->result()
									);
							}							

						break;
						case 'profile':
							$this->db->where('key' , $accessToken);
							$check = $this->db->get('m_akun');
							if ( ! $check->num_rows() > 0){
								$result = array(
										'return' => false,
										'error_message' => 'Access token salah atau tidak ditemukan!'
									);
							}else{
								$result = array(
										'return' => true,
										'picture'=>$check->row()->profile_picture
									);
							}
						break;
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
								$sqlNew = "SELECT t_timeline.id as id_timeline, t_timeline.*, m_aktivitas.*, t_timeline.tanggal as tgl FROM
											t_timeline
											LEFT JOIN m_akun ON m_akun.id = t_timeline.id_user
											LEFT JOIN m_aktivitas ON m_aktivitas.id = t_timeline.id_aktivitas
											WHERE m_akun.key = '".$accessToken."'
											ORDER BY t_timeline.tanggal DESC , t_timeline.jam DESC";

								$hsl = $this->db->query($sqlNew)->result();

								$results = array();

								foreach($hsl as $num => $data)
								{
									$sQuery = "SELECT table_name FROM information_schema.tables WHERE table_schema = 'gopray_db' AND table_name = 'm_".$hsl[$num]->prefix_table."'";

									$checkTable = $this->db->query($sQuery);
									$namaIbadah = $data->nama_ibadah;
									if ($checkTable->num_rows() > 0) {
										$this->db->where('id', $data->id_ibadah);
										$ibadah = $this->db->get('m_'.$hsl[$num]->prefix_table)->result();
										$namaIbadah = $ibadah[0]->nama;
									}
									$results[] = array(
											'id_timeline' =>$data->id_timeline,
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
											'tanggal' => $data->tgl,
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

			case 'parent':
				if ( ! $accessToken )
				{
					$result = array(
							'return' => false,
							'error_message' => 'Access token tidak valid!'
						);
				}

				if ( $action != null)
				{
					switch( trimLower($action))
					{
						case 'timeline':
							$query = $this->db->get_where('m_family' , array('key' => $accessToken));

							if ( $query->num_rows() == 0)
							{
								$result = array(
									'return' => false,
									'error_message' => 'Access token salah atau tidak ditemukan!'
								);
							}
							else
							{
								$dataParent = $query->result()[0];

								$sql = "SELECT t_timeline.id as id_timeline, t_timeline.*, m_aktivitas.*, t_timeline.tanggal as tgl FROM
										t_timeline
										LEFT JOIN m_akun ON m_akun.id = t_timeline.id_user
										LEFT JOIN m_aktivitas ON m_aktivitas.id = t_timeline.id_aktivitas
										WHERE t_timeline.id_user IN (".$dataParent->child.")
										ORDER BY t_timeline.tanggal DESC , t_timeline.jam DESC";

								$hsl = $this->db->query($sql)->result();
								
								$temp = array();

								foreach($hsl as $row)
								{									
									array_push($temp, $row);
								}
								
								array_merge($temp);

								$result = array(
									'return' => true,
									'data' => $temp
								);
							}
						break;

						default:
							$result = array(
									'return' => false,
									'error_message' => 'Parameter tidak ditemukan'
								);
						break;
					}
				}else
				{
					$result = array(
							'return' => false,
							'error_message' => 'Parameter tidak ditemukan'
						);
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

						case 'kerabat':
							$this->db->where('key' , $accessToken);

							$query = $this->db->get('m_akun');

							if ( ! $query->num_rows() > 0)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Access token salah atau tidak ditemukan!'
									);
							}
							else
							{
								$email = $this->post('email');

								if ( ! $accessToken || ! $email)
								{
									$result = array(
											'return' => false,
											'error_message' => 'Masih ada field yang kosong!'
										);
								}
								else
								{
									$checkKerabat = $this->db
									->get_where('t_closest_family' , array('email' => $email));

									if ( $checkKerabat->num_rows() > 0)
									{
										$result = array(
												'return' => false,
												'error_message' => 'Kerabat sudah ditambahkan.'
											);
									}
									else
									{
										$data = array(
												'id_user' => $query->result()[0]->id,
												'id_kerabat' => 0,
												'email' => $email
											);

										$this->db->insert('t_closest_family' , $data);

										$result = array(
												'return' => true,
												'message' => 'Kerabat berhasil ditambahkan.'
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
							$nominal = $this->post('nominal');
							$point = $this->post('point');
							$tanggal = $this->post('tanggal');
							$jam = $this->post('jam');

							if ( ! $accessToken || ! $id_aktivitas || ! $id_ibadah || ! $tempat 
								|| ! $bersama || ! $gambar || ! $point)
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
									$this->db->where('key' , $accessToken);
									$query = $this->db->get('m_akun');
									// echo $query->row()->id;
									$data = array(
											'id_user' => $query->row()->id,
											'id_aktivitas' => $id_aktivitas,
											'id_ibadah' => $id_ibadah,
											'tempat' => $tempat,
											'bersama' => $bersama,
											'image' => $gambar,
											'nominal' => $nominal,
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
								$imagedirtemp = FCPATH.'resources/tempimages/';
								$images = glob($imagedir . '*.{jpg,jpeg,png,gif,JPG,JPEG,PNG,GIF,}',GLOB_BRACE);
								$randomimage = $images[array_rand($images)];
								$text = ( ! $this->post('text')) ? null : substr($this->post('text'),0,50);
								$namaGambar = $imagedirtemp.$_FILES['gambar']['name'];
								$_FILES['gambar'] ? move_uploaded_file($_FILES['gambar']['tmp_name'], $namaGambar) : $randomimage;
								$gambar = ( ! isset($_FILES['gambar']) ) ? $randomimage : $namaGambar;
								// $mime = get_mime_by_extension($gambar);
								$mime = isset($_FILES['gambar']) ? $_FILES['gambar']['type'] : get_mime_by_extension($gambar);
								$mimeAccepted = array('image/jpeg' ,'image/png');
								if ( ! $this->post('text') || $text == null)
								{
									$result = array(
											'return' => false,
											'error_message' => 'Field text tidak boleh kosong!'
										);
								}
								elseif( ! in_array($mime,$mimeAccepted))
								{
									$result = array(
											'return' => false,
											'error_message' => 'Gambar hanya boleh berekstensi JPG/PNG'
										);
								}
								else
								{
									$this->textMeme = $text;
									$this->gambarMeme = ($mime == 'image/png') ? imagecreatefrompng($gambar) : imagecreatefromjpeg($gambar);
									/* Create meme */
									$box = new Box($this->gambarMeme);
									$box->setFontSize(40);
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

									// $direktori, $namaBaru, $watermark, $name
									/* Watermark Image */

									/* Watermark Image*/

									/* GoPray Watermark */
									// $box = new Box($this->gambarMeme);
									// $box->setFontSize(100);
									// $box->setFontFace(FCPATH.'resources/fonts/arial.ttf');
									// $box->setFontColor(new Color(255, 255, 255)); 
									// $box->setBox(
									//     -60,
									//     -60,
									//     imagesx($this->gambarMeme),
									//     imagesy($this->gambarMeme)
									// );
									// $box->setTextAlign('right','bottom');
									// $box->draw('GoPray');
									/* GoPray Watermark */
									
									$upload_dir = FCPATH.'resources/';
									if ( ! is_dir($upload_dir.'uploads'))
									{
										mkdir(FCPATH.'resources/uploads/');
										@chmod ( FCPATH.'resources/uploads/' , 0777);
									}
									$filenameUpload = $upload_dir.'uploads/'.generate_image($this->gambarMeme).'.png';
									imagepng($this->gambarMeme, $filenameUpload, 9);
									// imagedestroy($this->gambarMeme);
									/* image filename */
									$x = explode('/' , $filenameUpload);
									$count = count($x) - 1;
									/* image filename */
									$data = array(
											'id_user' => $akun->result()[0]->id,
											'path_meme' => base_url("resources/uploads/".$x[$count]),
											// 'path_meme' => $filenameUpload,
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
									->select( array('id','path_meme','tanggal','jam'))
									->from('t_meme')
									->where( array('id_user' => $akun->result()[0]->id))
									->order_by('id DESC','jam DESC')
									->get();
									$result = array(
											'return' => true,
											'status' => $status,
											'data_meme' => $query->result()
										);
								}
							}
						break;

						case 'deletetimeline':
							$this->db->where('key' , $accessToken);

							$query = $this->db->get('m_akun');

							if ( ! $query->num_rows() > 0)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Access token salah atau tidak ditemukan!'
									);
							}
							else
							{
								$id_timeline = $this->post('id_timeline');

								if ( ! $id_timeline)
								{
									$result = array(
											'return' => false,
											'error_message' => 'Masih ada field yang kosong!'
										);
								}
								else
								{
									$data = array(
											'id' => $id_timeline,
											'id_user' => $query->result()[0]->id
										);

									$checkDataTimeline = $this->db
									->get_where('t_timeline' , $data);


									if ( $checkDataTimeline->num_rows() == 0)
									{
										$result = array(
												'return' => false,
												'error_message' => 'Data tidak ditemukan atau sudah dihapus!'
											);
									}
									else
									{
										$this->db->delete('t_timeline' , $data);

										$result = array(
												'return' => true,
												'message' => 'Data timeline berhasil dihapus'
											);
									}
								}
							}
						break;

						case 'profile':
							$this->db->where('key' , $accessToken);

							$query = $this->db->get('m_akun');

							if ( ! $query->num_rows() > 0)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Access token salah atau tidak ditemukan!'
									);
							}
							else
							{
								$dataUser = $query->result()[0];

								$method = $this->post('method');

								$listMethod = array(
										1 => 'Detail Profile',
										2 => 'Picture Only',
										3 => 'Password Only'
									);

								if ( ! array_key_exists($method, $listMethod))
								{
									$result = array(
											'return' => false,
											'error_message' => 'Metode salah atau tidak ditemukan!'
										);
								}
								else
								{
									switch($method)
									{
										// Detail Profile
										case 1:
											$nama = ( $this->post('nama') && ! is_null($this->post('nama'))) 
											? $this->post('nama') : $dataUser->nama;
											$email = ( $this->post('email') && ! is_null($this->post('email'))) 
											? $this->post('email') : $dataUser->email;

											$dataUpdate = array(
													'nama' => $nama,
													'email' => $email
												);

											$this->db->set($dataUpdate);
											$this->db->where('id' , $dataUser->id);
											$this->db->update('m_akun');
											
											$result = array(
													'return' => true,
													'message' => 'Data berhasil diubah!'
												);
										break;

										// Picture Only
										case 2:
											if ( ! $_FILES['gambar'])
											{
												$result = array(
														'return' => false,
														'error_message' => 'Parameter masih ada yang kosong!'
													);
											}
											else
											{
												$mime = isset($_FILES['gambar']) ? $_FILES['gambar']['type'] : get_mime_by_extension($gambar);

												$mimeAccepted = array('image/png' , 'image/jpeg');

												if ( ! in_array($mime, $mimeAccepted))
												{
													$result = array(
															'return' => false,
															'error_message' => 'File gambar hanya boleh berekstensi JPG/PNG'
														);
												}
												else
												{
													$x = explode("." , $_FILES['gambar']['name']);
													$image_result = generate_image($_FILES['gambar']['name']).'.'.end($x);

													isset($image_result) ? 
													move_uploaded_file($_FILES['gambar']['tmp_name'], FCPATH.'resources/uploads/'.$image_result) : null;

													$dataUpdate = array(
															'profile_picture' 
															=> base_url('resources/uploads/'.$image_result)
														);

													$this->db->set($dataUpdate);
													$this->db->where('id' , $dataUser->id);
													$this->db->update('m_akun');

													$result = array(
															'return' => true,
															'message' => 'Foto profil berhasil diubah',
														);
												}
											}
										break;

										// Password Only
										case 3:
											if ( ! $this->post('password'))
											{
												$result = array(
														'return' => false,
														'error_message' => 'Parameter masih ada yang kosong!'
													);
											}
											else
											{
												$dataUpdate = array(
															'password' 
															=> md5($this->post('password'))
														);

												$this->db->set($dataUpdate);
												$this->db->where('id' , $dataUser->id);
												$this->db->update('m_akun');

												$result = array(
														'return' => true,
														'message' => 'Data password berhasil dirubah'
													);
											}
										break;
									}
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

			case 'parent':
				if ( $action != null)
				{
					switch( trimLower($action))
					{
						case 'login':
							$email = $this->post('email');
							$password = $this->post('password');

							if ( ! $email || ! $password)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Masih ada field yang kosong!'
									);
							}
							else
							{
								$query = $this->db
								->get_where('m_family' , array('email' => $email , 'password' => md5($password)));

								if ( $query->num_rows() == 0)
								{
									$result = array(
											'return' => false,
											'error_message' => 'Email atau password salah!'
										);
								}
								else
								{
									$result = array(
											'return' => true,
											'message' => 'Berhasil login'
										);
								}
							}
						break;

						case 'daftar':
							$kerabat = $this->post('kerabat');
							$nama = $this->post('nama');
							$email = $this->post('email');
							$no_hp = $this->post('no_hp');
							$password = md5($this->post('password'));

							if ( ! $kerabat || ! $nama || ! $email || ! $no_hp	 || ! $password)
							{
								$result = array(
										'return' => false,
										'error_message' => 'Masih ada parameter yang kosong!'
									);
							}
							else
							{
								$cekClosestFamily = $this->db->get_where('t_closest_family' , array('email' => $email));

								if ( $cekClosestFamily->num_rows() == 0)
								{
									$result = array(
											'return' => false,
											'error_message' => 'Email belum terdaftar'
										);
								}
								else
								{
									$id_user = $cekClosestFamily->result()[0]->id_user;

									$queryFamily = $this->db->get_where('m_family' , array('email' => $email));

									if ( $queryFamily->num_rows() > 0)
									{
										$result = array(
												'return' => false,
												'error_message' => 'Akun sudah terdaftar'
											);
									}
									else
									{
										$data = array(
												'kerabat' => $kerabat,
												'nama' => $nama,
												'email' => $email,
												'no_hp' => $no_hp,
												'password' => $password,
												'key' => generate_key(),
												'gambar' => null,
												'tanggal' => date('Y-m-d'),
												'jam' => date('H:i:s'),
												'child' => $id_user
											);

										$this->db->insert('m_family' , $data);

										$queryNewFamily = $this->db->get_where('m_family' , array('email' => $email));

										$dataUpdate = array(
												'id_kerabat' => $queryNewFamily->result()[0]->id
											);
										
										$updateWhere = array(
												'id_user' => $id_user, 
												'email' => $email
											);

										$this->db->update('t_closest_family' , $dataUpdate , $updateWhere);

										$result = array(
												'return' => true,
												'message' => 'Akun berhasil ditambahkan'
											);
									}
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