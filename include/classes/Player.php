<?php

class Player {
	function Player($firstName, $lastName, $playerID, $playerKey, $playerInfo = array()) {
		$this->firstName = e($firstName);
		$this->lastName = e($lastName);
		$this->playerID = $playerID;
		$this->playerKey = $playerKey;

		if(!empty($playerInfo)) {
			$this->jersey = e($playerInfo['jersey']);
			$this->position = e($playerInfo['position']);
			$this->dob = $playerInfo['dob'];
			$this->graduating = $playerInfo['graduating'];

			// extra check to save memory, as viewing a team doesn't need these
			if(isset($playerInfo['address'])) {
				$this->parentEmail = e($playerInfo['parentEmail']);
				$this->playerEmail = e($playerInfo['playerEmail']);
				$this->phone = e($playerInfo['phone']);
				$this->address = e($playerInfo['address']);
				$this->city = e($playerInfo['city']);
				$this->state = e($playerInfo['state']);
				$this->zip = e($playerInfo['zip']);
			}
		}
	}

	function fullName() {
		return $this->firstName . ' ' . $this->lastName;
	}
	function age() {
		return calc_age($this->dob);
	}
	function address() {
		return "$this->address in $this->city, $this->state $this->zip";
	}
	function teams($conn) {
		if(!isset($this->teams)) {
			$teams = array();
			$stmt = $conn->prepare(
				"SELECT t.teamName, t.teamID
					FROM teams AS t
						JOIN teamPlayers AS tp
							ON t.joinKey = tp.teamID
					WHERE tp.playerID = ?"
			);
			$stmt->bind_param("i", $this->playerKey);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($teamName, $teamID);
			while($stmt->fetch()) {
				$team = new Team($teamName, $teamID);
				array_push($teams, $team);
			}
			$stmt->close();
			$this->teams = $teams;
		}
		return $this->teams;
	}
}
