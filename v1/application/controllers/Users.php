<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Users extends REST_Controller {

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

	public function index_get($option = '' , $action = '')
	{
		$this->logdata['method'] = 'GET';
		catatLog($this->logdata);

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
						// $this->db->where('key' , $accessToken);

						// $query = $this->db->select( array('nama','email','profile_picture','key'))->from('m_akun')->get()->result();
						
						// $result = array(
						// 		'return' => true,
						// 		'data' => $query
						// 	);

						$this->db->where('key' , $accessToken);

						$query = $this->db->select( array('nama','email','profile_picture','key','tanggal'))->from('m_akun')->get()->result();
						
						$result = array(
								'return' => true,
								'data' => $query
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
								$sql = "SELECT m_akun.nama AS nama_user, m_aktivitas.nama AS nama_aktivitas, m_sholat.nama AS nama_ibadah, t_timeline.tempat , t_timeline.bersama , t_timeline.point , t_timeline.tanggal , t_timeline.jam FROM m_akun , m_aktivitas , m_sholat , t_timeline WHERE m_akun.key = '".$accessToken."' AND t_timeline.id_user = '".$check->result()[0]->id."'";

								$query = $this->db->query($sql);

								if ( $query->num_rows() == 0)
								{
									$result = array(
											'return' => true,
											'data' => 'Data timeline masih kosong.'
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
								echo $check->result()[0]->id;
								// $sql = "SELECT DISTINCT t_closest_family.nama AS nama_kerabat , t_message.message AS pesan , t_message.tanggal , t_message.jam FROM t_closest_family , t_message , m_akun WHERE m_akun.key = '".$accessToken."' AND t_message.id_user = '".$check->result()[0]->id."'";

								$sql = "SELECT t_closest_family.nama AS nama_kerabat , t_message.message AS pesan , t_message.tanggal , t_message.jam FROM  t_closest_family , t_message , m_akun WHERE m_akun.key = '".$accessToken."' AND t_message.id_user = '".$check->result()[0]->id."' AND t_closest_family.id_user = '".$check->result()[0]->id."'";

								$query = $this->db->query($sql);

								print_r($query->result());

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

						// Sholat section
						case 'sholat':
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
								
								$query = $this->db->select( array('id','nama'))->from('m_sholat')->get();

								if ( $query->num_rows() == 0)
								{
									$result = array(
											'return' => true,
											'data' => 'Data sholat masih kosong.'
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
						
						default:
							$result = array(
									'return' => false,
									'error_message' => 'Aksi tidak ditemukan'
								);
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

						$query = $this->db->select( array('nama','email','profile_picture','key'))->from('m_akun')->get()->result_array();

						$result = array(
								'return' => true,
								'data' => $query
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

							$query = $this->db->select( array('nama','email','profile_picture','key'))->from('m_akun')->get()->result();
							$result = array(
									'return' => true,
									'data' => $query
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
		catatLog($this->logdata);

		$accessToken = $this->post('access_token');

		switch( trimLower($option))
		{
			case 'self':
				if ( $action != null)
				{
					switch( trimLower($action))
					{
						case 'login':
							$result = array(
									'return' => true,
									'data' => 'ok '.@$accessToken
								);
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
						'error_message' => 'Metode tidak diperbolehkan!'
					);
			break;
		}

		$this->response($result);
	}

}

/* End of file Users.php */
/* Location: ./application/controllers/Users.php */