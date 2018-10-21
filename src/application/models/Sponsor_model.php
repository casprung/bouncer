<?php
class Sponsor_model extends CI_Model {

	//GID is sha1 of username and salt
	public function gid_is_pending($gid) {
		$this->db->where("SHA1(CONCAT(username, salt)) = ".$this->db->escape($gid));
		$query = $this->db->get('pending_guests');
		// A single result, go on.
		return ($query->num_rows() === 1) ? TRUE : FALSE;
	}

	public function gid_is_active($gid) {
		$this->db->where("SHA1(CONCAT(username, salt)) = ".$this->db->escape($gid));
		$query = $this->db->get('active_guests');
		// A single result, go on.
		return ($query->num_rows() === 1) ? TRUE : FALSE;
	}

	public function activate_guest($gid, $duration) {
		switch ($duration) {
			case "today":
				$sql  = "INSERT INTO active_guests (created, expiry, username, hashedpwd, salt) ";
				$sql .= "SELECT created, DATE_ADD(NOW(), INTERVAL 24 HOUR), username, hashedpwd, salt ";
				$sql .= "FROM pending_guests ";
				$sql .= "WHERE SHA1(CONCAT(username, salt)) = ".$this->db->escape($gid);
				break;
			case "week":
				$sql  = "INSERT INTO active_guests (created, expiry, username, hashedpwd, salt) ";
				$sql .= "SELECT created, DATE_ADD(NOW(), INTERVAL 7 DAY), username, hashedpwd, salt ";
				$sql .= "FROM pending_guests ";
				$sql .= "WHERE SHA1(CONCAT(username, salt)) = ".$this->db->escape($gid);
				break;
		}
		$this->db->trans_start();
		$this->db->query($sql);
		$this->db->query("DELETE FROM pending_guests WHERE SHA1(CONCAT(username, salt)) = ".$this->db->escape($gid));
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function get_pending_guest($gid) {
		return $this->db->select('name, email, mobile, password, sponsor')->from('pending_guests')->where("SHA1(CONCAT(username, salt)) = ".$this->db->escape($gid))->limit(1)->get()->row_array();
	}	
	
	public function get_active_guest_timestamps($gid) {
		return $this->db->select("DATE_FORMAT(created,\"%d.%m.%Y %H:%i\") AS start, DATE_FORMAT(expiry,\"%d.%m.%Y %H:%i\") AS stop")->from('active_guests')->where("SHA1(CONCAT(username, salt)) = ".$this->db->escape($gid))->limit(1)->get()->row_array();
	}
	
	public function delete_pending_guest($gid) {
		$this->db->where("SHA1(CONCAT(username, salt)) = ".$this->db->escape($gid));
		return $this->db->delete('pending_guests');
	}

	public function delete_active_guest($gid) {
		$sql  = "DELETE endpoints, active_guests, radcheck ";
		$sql .= "FROM active_guests ";
		$sql .= "LEFT JOIN endpoints ON endpoints.username = active_guests.username ";
		$sql .= "LEFT JOIN radcheck ON radcheck.username = active_guests.username ";
		$sql .= "WHERE SHA1(CONCAT(active_guests.username, active_guests.salt)) = ".$this->db->escape($gid);
		return $this->db->query($sql);;
	}
}