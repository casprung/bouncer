<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}
	
	public function login()
	{
		if($this->CI->session->userdata('parameter_checked')) {
			$this->CI->load->library('form_validation');

			$this->CI->form_validation->set_rules('email', 'Email address', 'strtolower|required|trim|valid_email');
			$this->CI->form_validation->set_rules('password', 'Password', 'required|trim');
			
			if ($this->CI->form_validation->run() === FALSE) {
				$data['title'] = 'Guest Wifi - Login';
				$this->CI->load->view('templates/header', $data);
				$this->CI->load->view('portal/login', $this->CI->session->userdata());
				$this->CI->load->view('templates/footer');
			} else {
				$this->CI->load->model('portal_model');
				$loggedin = $this->CI->portal_model->login();
				if ($loggedin) {
					$this->CI->session->set_userdata($loggedin);
					// if there is an endpoint limit configured go check, else continue network logon
					if ($this->CI->config->item('endpoint_limit')) {
						$this->endpoints();
					} else {
						$this->CI->_network_login();
					}
				} else {
					$data['title'] = 'Guest Wifi - Login';
					$this->CI->load->view('templates/header', $data);
					$this->CI->load->view('portal/login', $this->CI->session->userdata());
					$this->CI->load->view('templates/footer');
				}
			}
		} else {
			$this->CI->index();
		}
	}

	public function endpoints()
	{
		// if valid session information and user is logged in
		if($this->CI->session->userdata('parameter_checked') && $this->CI->session->userdata('username')) {
			$this->CI->load->model('portal_model');

			$this->CI->load->library('form_validation');
			$this->CI->form_validation->set_rules('mac', 'MAC Address', 'trim|required|regex_match[/^[0-9a-f]{12}$/]');
			// if form was submitted successfully delete selected mac from database
			if ($this->CI->form_validation->run() === TRUE) {
				$this->CI->portal_model->delete_endpoint($this->CI->session->userdata('username'), $this->CI->session->userdata('ssid'), $this->CI->input->post('mac'));
			}

			$endpoints = $this->CI->portal_model->get_endpoints($this->CI->session->userdata('username'), $this->CI->session->userdata('ssid'));
			// if there are less endpoints logged in then allowed then continue logon to network
			if (count($endpoints) < $this->CI->config->item('endpoint_limit')) {
				$this->CI->_network_login();
			} else {
				// provide view to delete own endpoints
				$data['title'] = 'Guest Wifi - Device limit reached';
				$data['header'] = 'Device Limit Reached';
				$data['message'] = 'Only '.$this->CI->config->item('endpoint_limit').' concurrent devices allowed';
				$data['endpoints'] = $endpoints;
				$this->CI->load->view('templates/header', $data);
				$this->CI->load->view('portal/endpoints', $data);
				$this->CI->load->view('templates/footer');
			}
		} else {
			$this->CI->index();
		}
	}
	
	public function register()
	{
		if($this->CI->session->userdata('parameter_checked')) {
			$this->CI->load->library('form_validation');
			
			$this->CI->form_validation->set_rules('name', 'Your name', 'required|trim|min_length[3]|max_length[255]|is_unique[pending_guests.name]');
			$this->CI->form_validation->set_rules('email', 'Your email address', 'strtolower|required|trim|valid_email|is_unique[pending_guests.email]');
			$this->CI->form_validation->set_rules('mobile', 'Mobile phone number', 'trim|regex_match[/^\+49\d{4,15}$/]|is_unique[pending_guests.mobile]');
			$this->CI->form_validation->set_message('regex_match', 'The {field} field is not in the correct format. Use: +4915123456789');
			$this->CI->form_validation->set_rules('sponsor', 'Sponsor email address', 'strtolower|required|trim|valid_email|differs[email]|in_list[sponsor1@domain.com,sponsor2@domain.com,sponsor3@domain.com]');
			$this->CI->form_validation->set_message('in_list', 'The {field} is not on the approved sponsor list.');
			
			if ($this->CI->form_validation->run() === FALSE) {
				$data['title'] = 'Guest Wifi - Register new account';
				$this->CI->load->view('templates/header', $data);
				$this->CI->load->view('portal/register', $data);
				$this->CI->load->view('templates/footer');
			} else {
				$this->CI->load->model('portal_model');
				$create_pending_guest_result = $this->CI->portal_model->create_pending_guest();
				if ($create_pending_guest_result) {
					$data = $this->CI->portal_model->get_pending_guest($create_pending_guest_result);
					$data['gid'] = sha1($data['username'].$data['salt']);
					$this->CI->load->library('email');
					$this->CI->email->from('your.name@t-online.de', 'Your Name');
					$this->CI->email->to($this->CI->input->post('sponsor'));
					$this->CI->email->subject('Guest Wifi - Registration by '.$data['name'].' <'.$data['email'].'>');
					$this->CI->email->message($this->CI->load->view('portal/sponsormail_html', $data, TRUE));
					$this->CI->email->set_alt_message($this->CI->load->view('portal/sponsormail_text', $data, TRUE));
					if ( ! $this->CI->email->send(TRUE)) {
						$this->CI->portal_model->delete_pending_guest($create_pending_guest_result);
						$data['title'] = 'Guest Wifi - Bad Request';
						$data['message'] = 'Sending email to sponsor has failed';
						$this->CI->load->view('templates/header', $data);
						$this->CI->load->view('portal/error');
						//echo $this->CI->email->print_debugger();  //set $this->CI->email->send to FALSE otherwise the debugger wont show anything
						$this->CI->load->view('templates/footer');
					} else {
						$data['title'] = 'Guest Wifi - Registration complete';
						$this->CI->load->view('templates/header', $data);
						$this->CI->load->view('portal/success');
						$this->CI->load->view('templates/footer');
						$this->CI->session->sess_destroy();
					}
				} else {
					$data['title'] = 'Guest Wifi - Bad Request';
					$data['message'] = 'Database operation has failed';
					$this->CI->load->view('templates/header', $data);
					$this->CI->load->view('portal/error');
					$this->CI->load->view('templates/footer');
				}
			}
		} else {
			$this->CI->index();
		}
	}
}