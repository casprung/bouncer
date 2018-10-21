<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Network extends CI_Controller {

	public function index()
	{
		redirect(base_url());
	}

	public function aerohive($username='', $challenge='', $response='', $url='')
	{
		$data['username'] = $username;
		$data['challenge'] = $challenge;
		$data['response'] = $response;
		$data['url'] = $url;
		$this->load->view('network/aerohive', $data);
	}
	public function cisco($username='', $hashedpwd='', $switch_url='', $url='')
	{
		$data['username'] = $username;
		$data['hashedpwd'] = $hashedpwd;
		$data['switch_url'] = pack('H*', $switch_url);
		$data['url'] = pack('H*', $url);
		$this->load->view('network/cisco', $data);
	}
}