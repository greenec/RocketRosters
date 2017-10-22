$(document).ready(function() {

	var tournamentPlayers = $('#tournamentPlayers').DataTable({
		'order': [[2, 'asc']]
	});

	// add a player to a tournament
	$('body').on('click', 'button.addPlayer', function(event) {
		$('.text-danger').remove();
		var id = this.id;
		var formData = {
			'playerID': id,
			'teamID': getUrlVars()["teamID"],
			'tournamentID': getUrlVars()["tournamentID"],
			'action': 'add'
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateTournamentPlayers.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				$('#tournamentPlayers').after('<p class="text-danger">' + data.errors.error + '</p>');
			} else {
				$('button[id="' + id +'"]').html('Remove');
				$('button[id="' + id +'"]').toggleClass('addPlayer removePlayer');
				$('tr[id="' + id +'"]').toggleClass('danger success');
				tournamentPlayers.row('[id="' + id + '"]').invalidate().draw();
			}
		});
		event.preventDefault();
	});

	// remove a player from a tournament
	$('body').on('click', 'button.removePlayer', function(event) {
		$('.text-danger').remove();
		var id = this.id;
		var formData = {
			'playerID': id,
			'teamID': getUrlVars()["teamID"],
			'tournamentID': getUrlVars()["tournamentID"],
			'action': 'remove'
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateTournamentPlayers.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				$('#tournamentPlayers').after('<p class="text-danger">' + data.errors.error + '</p>');
			} else {
				$('button[id="' + id +'"]').html('Add');
				$('button[id="' + id +'"]').toggleClass('removePlayer addPlayer');
				$('tr[id="' + id +'"]').toggleClass('success danger');
				tournamentPlayers.row('[id="' + id + '"]').invalidate().draw();
			}
		});
		event.preventDefault();
	});

});

function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m, key, value) {
		vars[key] = value;
	});
	return vars;
}
