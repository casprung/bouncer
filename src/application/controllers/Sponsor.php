<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsor extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('sponsor_model');
	}

	public function today($gid = FALSE) {
		$this->_activate($gid, 'today');
	}

	public function week($gid = FALSE) {
		$this->_activate($gid, 'week');
	}

	public function reject($gid = FALSE) {
		if($this->sponsor_model->gid_is_active($gid)) {
			if($this->sponsor_model->delete_active_guest($gid)) {
				$data['title'] = 'Guest Wifi - Account deleted';
				$data['header'] = 'Account deleted';
				$data['message'] = 'No information will be sent to the user';
				$this->load->view('templates/header', $data);
				$this->load->view('sponsor/delete', $data);
				$this->load->view('templates/footer');
			} else {
				$data['title'] = 'Guest Wifi - Failed to delete the registration';
				$data['header'] = 'Failed to delete the registration';
				$data['message'] = 'The system has failed to delete the request';
				$this->load->view('templates/header', $data);
				$this->load->view('sponsor/error', $data);
				$this->load->view('templates/footer');
			}
		} elseif($this->sponsor_model->gid_is_pending($gid)) {
			if($this->sponsor_model->delete_pending_guest($gid)) {
				$data['title'] = 'Guest Wifi - Registration deleted';
				$data['header'] = 'Registration deleted';
				$data['message'] = 'No information will be sent to the requestor';
				$this->load->view('templates/header', $data);
				$this->load->view('sponsor/reject', $data);
				$this->load->view('templates/footer');
			} else {
				$data['title'] = 'Guest Wifi - Failed to delete the registration';
				$data['header'] = 'Failed to delete the registration';
				$data['message'] = 'The system has failed to delete the request';
				$this->load->view('templates/header', $data);
				$this->load->view('sponsor/error', $data);
				$this->load->view('templates/footer');
			}
		} else {
			$data['title'] = 'Guest Wifi - Account not found';
			$data['header'] = 'Account not found';
			$data['message'] = 'The registration has expired or is invalid';
			$this->load->view('templates/header', $data);
			$this->load->view('sponsor/error', $data);
			$this->load->view('templates/footer');
		}
	}

	private function _activate($gid, $duration) {
		if($this->sponsor_model->gid_is_active($gid)) {
			$data['title'] = 'Guest Wifi - Account not pending';
			$data['header'] = 'Account not pending';
			$data['message'] = 'The account is already active';
			$this->load->view('templates/header', $data);
			$this->load->view('sponsor/error', $data);
			$this->load->view('templates/footer');
		} elseif($this->sponsor_model->gid_is_pending($gid)) {
			//Get name, email, and clear-text password from pending_guests table
			$data = $this->sponsor_model->get_pending_guest($gid);
			if($this->sponsor_model->activate_guest($gid, $duration)) {
				//Get start and stop timestamps from active_guests table
				$data = array_merge($data, $this->sponsor_model->get_active_guest_timestamps($gid));
				$this->_send_approval_mail($data);
				if (!empty($data['mobile'])) $this->_send_approval_sms($data);

				$data['title'] = 'Guest Wifi - Account activated';
				$data['header'] = 'Account activated';
				$data['message'] = 'The account was successfully activated';
				$this->load->view('templates/header', $data);
				$this->load->view('sponsor/success', $data);
				$this->load->view('templates/footer');
			} else {
				$data['title'] = 'Guest Wifi - Account not activated';
				$data['header'] = 'Account not activated';
				$data['message'] = 'The activation of this account has failed';
				$this->load->view('templates/header', $data);
				$this->load->view('sponsor/error', $data);
				$this->load->view('templates/footer');
			}
		} else {
			$data['title'] = 'Guest Wifi - Account not found';
			$data['header'] = 'Account not found';
			$data['message'] = 'The registration has expired or is invalid';
			$this->load->view('templates/header', $data);
			$this->load->view('sponsor/error', $data);
			$this->load->view('templates/footer');
		}
	}
	
	private function _send_approval_mail($data) {
		$this->load->library('email');
		$this->email->from('your.name@t-online.de', 'Your Name');
		$this->email->to($data['email']);
		if ($this->config->item('sponsorcc')) $this->email->cc($data['sponsor']);
		$this->email->subject('Guest Wifi - The sponsor has approved your registration');
		$this->email->message($this->load->view('sponsor/approvalmail_html', $data, TRUE));
		$this->email->set_alt_message($this->load->view('sponsor/approvalmail_text', $data, TRUE));
		return $this->email->send();
	}
	
	private function _send_approval_sms_playsms($data) {
		$service_url = 'https://your-playsms-server.com/playsms/index.php';
		$curl = curl_init($service_url);
		$curl_post_data = array(
			'app' => 'ws',
			'u'   => 'admin',
			'h'   => 'd237ddcefefc9de58742202c3cfe60d9',
			'op'  => 'pv',
			'to'  => $data['mobile'],
			'msg' => $this->load->view('sponsor/approvalsms_text', $data, TRUE)
		);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		return curl_exec($curl);
	}
	
	private function _send_approval_sms($data) {
		if (!getenv('SIPGATE_SMS_ID') || !getenv('SIPGATE_ACCESS_TOKEN')) return TRUE;
		$service_url = 'https://api.sipgate.com/v2/sessions/sms';
		$curl = curl_init($service_url);
		$curl_post_data = json_encode(array(
			'smsId' => getenv('SIPGATE_SMS_ID'),
			'recipient'  => $data['mobile'],
			'message' => $this->load->view('sponsor/approvalsms_text', $data, TRUE)
		));
		$request_headers = array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($curl_post_data),
			'Authorization: Bearer ' . getenv('SIPGATE_ACCESS_TOKEN')
		);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_TIMEOUT, 5);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $request_headers);
		$response = curl_exec($curl);
		curl_close($curl);
		return $response;
	}
}
