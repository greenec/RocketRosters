<?php

class Tournament {
  function Tournament($tournamentName, $tournamentID, $tournamentKey = null, $date = null) {
    $this->tournamentName = e($tournamentName);
    $this->tournamentID = $tournamentID;
    $this->tournamentKey = $tournamentKey;

		if(isset($date)) {
			$this->date = date("m/d/Y", strtotime($date));
		}
  }

  function fullName() {
    return "$this->tournamentName ($this->tournamentID)";
  }
}
