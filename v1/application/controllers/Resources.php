<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Resources extends REST_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->logdata = array(
				'method' => 'NOT_SET',
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'ip_address' => $_SERVER['REMOTE_ADDR']	
			);
	}

	public function index_get()
	{
		$this->logdata['method'] = 'GET';
		catatLog($this->logdata);

		return $this->response( array('return' => false , 'error_message' => 'Parameter tidak ditemukan.'));
	}

	public function index_post()
	{
		$this->logdata['method'] = 'POST';

		catatLog($this->logdata);

		$data = array(
				'return' => TRUE,
				'result' => 'test'
			);
		print_r($data);
	}

}

/* End of file Resources.php */
/* Location: ./application/controllers/Resources.php */