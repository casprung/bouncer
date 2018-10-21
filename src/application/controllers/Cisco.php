<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cisco extends CI_Controller {

	public function index()
	{
		$data['url'] = $this->input->get('redirect');
		$data['ssid'] = $this->input->get('wlan');
		$data['mac'] = $this->_maccleanup($this->input->get('client_mac'));
		$data['autherr'] = $this->input->get('statusCode');
		$data['switch_url'] = $this->input->get('switch_url');
		
		$config = array(
			array(
				'field' => 'url',
				'label' => 'Parameter 1',
				'rules' => 'trim|valid_url',
				'errors' => array(
					'valid_url' => 'Malformed value for %s.',
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
				'field' => 'switch_url',
				'label' => 'Parameter 5',
				'rules' => 'trim|required|valid_url',
				'errors' => array(
					'required' => '%s is missing.',
					'valid_url' => 'Malformed value for %s.'
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
			redirect(base_url('cisco/login'));
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
		
		//redirect to network logon
		$switch_url = $this->session->userdata('switch_url');
		$hexstr = unpack('H*', $switch_url);
		$switch_url = array_shift($hexstr);

		$url = $this->session->userdata('url');
		$hexstr = unpack('H*', $url);
		$url = array_shift($hexstr);
		
		$segments = array('network', 'cisco', $this->session->userdata('username'), $this->session->userdata('hashedpwd'), $switch_url, $url);
		$redirect_url = preg_replace('/^(https:\/\/)/i', 'http://', site_url($segments));
		$this->session->sess_destroy();
		redirect($redirect_url, 'refresh');
	}
	
	private function _maccleanup($mac)
	{
		return strtolower(str_replace(array(':', '-', '.'), '', $mac));
	}
}

