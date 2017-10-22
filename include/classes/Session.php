<?php

class Session {
	private $db;

	public function __construct($conn) {
		$this->db = $conn;

		// set handler to overide SESSION
		session_set_save_handler(
			array($this, '_open'),
			array($this, '_close'),
			array($this, '_read'),
			array($this, '_write'),
			array($this, '_destroy'),
			array($this, '_cleanup')
		);

		session_start();
	}

	public function _open() {
		if($this->db) {
			return true;
		}
		return false;
	}

	public function _close() {
		return true;
	}

	public function _read($id) {
		$stmt = $this->db->prepare('SELECT data FROM sessions WHERE id = ?');
		$stmt->bind_param('s', $id);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($row);

		while($stmt->fetch()) {
			return $row;
		}
	}

	public function _write($id, $data) {
		$access = time();

		$stmt = $this->db->prepare(
			"INSERT INTO sessions (id, access, data) VALUES (?, ?, ?)
				ON DUPLICATE KEY UPDATE access = ?, data = ?"
		);
		$stmt->bind_param('sisis', $id, $access, $data, $access, $data);
		$stmt->execute();

		return true;
	}

	public function _destroy($id) {
		$stmt = $this->db->prepare('DELETE FROM sessions WHERE id = ?');
		$stmt->bind_param('s', $id);
		$stmt->execute();
		return true;
	}

	public function _cleanup($max) {
		$old = time() - $max;

		$stmt = $this->db->query('DELETE FROM sessions WHERE access < ?');
		$stmt->bind_param('i', $old);
		$stmt->execute();
		return true;
	}
}
