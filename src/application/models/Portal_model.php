<?php
class Portal_model extends CI_Model {

	public function login() {
		$email = $this->input->post('email');
		$password = $this->input->post('password');
		return $this->db->select('username, hashedpwd')->from('active_guests')
						->where("SHA1(CONCAT(".$this->db->escape($email).", salt)) = username")
						->where("SHA1(CONCAT(".$this->db->escape($password).", salt)) = hashedpwd")
						->limit(1)->get()->row_array();
	}

	public function compute_response($password, $challenge) {
		$hexchal = pack("H*", $challenge);
		$newchal = md5($hexchal.$this->config->item('uamsecret'), TRUE);
		return md5("\0".$password.$newchal);
	}
	
	public function create_endpoint($username, $ssid, $mac) {
		$sql = "INSERT INTO endpoints (username, ssid, mac) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE username = VALUES(username), ssid = VALUES(ssid)";
		$this->db->query($sql, array($username, $ssid, $mac));
	}
	
	public function get_endpoints($username, $ssid) {
		return $this->db->select('mac')->from('endpoints')->where('username', $username)->where('ssid', $ssid)->get()->result_array();
	}

	public function delete_endpoint($username, $ssid, $mac) {
		$this->db->where('username', $username);
		$this->db->where('ssid', $ssid);
		$this->db->where('mac', $mac);
		$this->db->delete('endpoints');
	}
	
	public function create_radcheck($username, $password) {
		$this->db->where('username', $username);
		$this->db->where('attribute', 'Cleartext-Password');
		$this->db->where('op', ':=');
		$this->db->where('value', $password);
		$query = $this->db->get('radcheck');
		if ( $query->num_rows() == 0 ) {
			$sql = "INSERT INTO radcheck (username, attribute, op, value) VALUES (?, ?, ?, ?)";
			$this->db->query($sql, array($username, 'Cleartext-Password', ':=', $password));
		}
	}

	public function create_pending_guest() {
		$salt = sha1(mt_rand());
		$password = $this->pwdgenerate();
		$data = array(
			'name'      => $this->input->post('name'),
			'email'     => $this->input->post('email'),
			'mobile'    => ($this->input->post('mobile') != FALSE) ? $this->input->post('mobile') : NULL,
			'password'  => $password,
			'sponsor'   => $this->input->post('sponsor'),
			'username'  => sha1($this->input->post('email').$salt),
			'hashedpwd' => sha1($password.$salt),
			'salt'      => $salt
		);
		$this->db->insert('pending_guests', $data);
		return $this->db->insert_id();
	}

	public function get_pending_guest($id) {
		return $this->db->select('name, email, username, salt')->from('pending_guests')->where('id', $id)->limit(1)->get()->row_array();
	}

	public function delete_pending_guest($id) {
		$this->db->delete('pending_guests', array('id' => $id));
	}

	public function pwdgenerate() {
		$upcase = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ"), 0, 3);
		$locase = substr(str_shuffle("abcdefghijkmnopqrstuvwxyz"), 0, 4);
		$digit = substr(str_shuffle("23456789"), 0, 2);
		$special = substr(str_shuffle("!$%+?-#"), 0, 1);
		return str_shuffle($upcase.$locase.$digit.$special);
	}
}