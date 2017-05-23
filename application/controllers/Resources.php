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
		// catatLog($this->logdata);

		$result = array(
			'return' => false, 
			'error_message' => 'Parameter tidak ditemukan.'
			);

		return $this->response($result);
	}

}

/* End of file Resources.php */
/* Location: ./application/controllers/Resources.php */