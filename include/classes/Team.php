<?php

class Team {
	function Team($teamName, $teamID, $teamKey = null, $date = null) {
		$this->teamName = e($teamName);
		$this->teamID = $teamID;
		$this->teamKey = $teamKey;

		if(isset($date)) {
			$this->date = date("m/d/Y", strtotime($date));
		}
	}
}
