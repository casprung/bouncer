<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aerohive extends CI_Controller {

	public function index()
	{
		$data['url'] = $this->input->get('url');
		$data['ssid'] = $this->input->get('ssid');
		$data['mac'] = $this->input->get('mac');
		$data['autherr'] = $this->input->get('autherr');
		$data['challenge'] = $this->input->get('challenge');
		$data['CalledStationId'] = $this->input->get('Called-Station-Id');
		$data['NASIPAddress'] = $this->input->get('NAS-IP-Address');
		$data['RADIUSNASIP'] = $this->input->get('RADIUS-NAS-IP');
		$data['CallingStation-Id'] = $this->input->get('Calling-Station-Id');
		$data['STAIP'] = $this->input->get('STA-IP');
		$data['NASID'] = $this->input->get('NAS-ID');
		
		$config = array(
			array(
				'field' => 'url',
				'label' => 'Parameter 1',
				'rules' => 'trim|alpha_numeric',
				'errors' => array(
					'alpha_numeric' => 'Malformed value for %s.',
				),
			),
			array(
				'field' => 'ssid',
				'label' => 'Parameter 2',
				'rules' => 'trim|required|max_length[32]',
				'errors' => array(
					'required' => '%s is missing.',
					'max_length' => 'Malformed value for %s.'
				),
			),
			array(
				'field' => 'mac',
				'label' => 'Parameter 3',
				'rules' => 'trim|required|regex_match[/^[0-9a-f]{12}$/]',
				'errors' => array(
					'required' => '%s is missing.',
					'regex_match' => 'Malformed value for %s.'
				),
			),
			array(
				'field' => 'autherr',
				'label' => 'Parameter 4',
				'rules' => 'trim|is_natural',
				'errors' => array(
					'is_natural' => 'Malformed value for %s.'
				),
			),
			array(
				'field' => 'challenge',
				'label' => 'Parameter 5',
				'rules' => 'trim|required|regex_match[/^[0-9a-f]*$/i]',
				'errors' => array(
					'required' => '%s is missing.',
					'regex_match' => 'Malformed value for %s.'
				),
			),
			array(
				'field' => 'CalledStationId',
				'label' => 'Parameter 6',
				'rules' => 'trim|regex_match[/^[0-9a-f]{12}$/]',
				'errors' => array(
					'regex_match' => 'Malformed value for %s.'
				),
			),
			array(
				'field' => 'NASIPAddress',
				'label' => 'Parameter 7',
				'rules' => 'trim|valid_ip[ipv4]',
				'errors' => array(
					'valid_ip' => 'Malformed value for %s.'
				),
			),
			array(
				'field' => 'RADIUSNASIP',
				'label' => 'Parameter 8',
				'rules' => 'trim|valid_ip[ipv4]',
				'errors' => array(
					'valid_ip' => 'Malformed value for %s.'
				),
			),
			array(
				'field' => 'CallingStationId',
				'label' => 'Parameter 9',
				'rules' => 'trim|regex_match[/^[0-9a-f]{12}$/]',
				'errors' => array(
					'regex_match' => 'Malformed value for %s.'
				),
			),
			array(
				'field' => 'STAIP',
				'label' => 'Parameter 10',
				'rules' => 'trim|valid_ip[ipv4]',
				'errors' => array(
					'valid_ip' => 'Malformed value for %s.'
				),
			),	
			array(
				'field' => 'NASID',
				'label' => 'Parameter 11',
				'rules' => 'trim|alpha_dash|max_length[32]',
				'errors' => array(
					'alpha_dash' => 'Malformed value for %s.',
					'max_length' => 'Malformed value for %s.'
				),
			),
		);
		
		$this->load->library('form_validation');
		$this->form_validation->set_data($data);
		$this->form_validation->set_rules($config);
		
		if ($this->form_validation->run() == FALSE) {
			$data['title'] = 'Guest Wifi - Bad Request';
			$data['message'] = 'Important parameters are invalid or missing';
			$this->load->view('templates/header', $data);
			$this->load->view('portal/error');
			$this->load->view('templates/footer');
		} else {
			$data['parameter_checked'] = TRUE;
			$this->session->set_userdata($data);
			redirect(base_url('aerohive/login'));
		}
	}

	public function login()
	{
		$this->load->library('portal');
		$this->portal->login();
	}

	public function endpoints()
	{
		$this->load->library('portal');
		$this->portal->endpoints();
	}
	
	public function register()
	{
		$this->load->library('portal');
		$this->portal->register();
	}
	
	public function _network_login() {
		//upsert data into radcheck table
		$this->portal_model->create_radcheck($this->session->userdata('username'), $this->session->userdata('hashedpwd'));
		
		//insert data into endpoint table
		$this->portal_model->create_endpoint($this->session->userdata('username'), $this->session->userdata('ssid'), $this->session->userdata('mac'));
		
		//generate response from username and challenge
		$response = $this->portal_model->compute_response($this->session->userdata('hashedpwd'), $this->session->userdata('challenge'));

		//redirect to network logon
		$segments = array('network', 'aerohive', $this->session->userdata('username'), $this->session->userdata('challenge'), $response, $this->session->userdata('url'));
		$redirect_url = preg_replace('/^(https:\/\/)/i', 'http://', site_url($segments));
		$this->session->sess_destroy();
		redirect($redirect_url, 'refresh');
	}
}

