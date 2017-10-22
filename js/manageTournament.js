$(document).ready(function() {

	var teams = $('#teams').DataTable();

	// remove a player from the team
	$('body').on('click', 'button.removeTeam', function(event) {
		if(confirm("Are you sure you want to remove this team from the tournament?")) {
			$('.text-success').remove();
			$('.text-danger').remove();
			var id = this.id;
			var formData = {
				'teamID': id,
				'tournamentID': getUrlVars()["tournamentID"],
				'action': 'remove'
			};
			$.ajax({
				type: 'POST',
				url: 'handlers/updateTournamentTeams.php',
				data: formData,
				dataType: 'json',
				encode: true
			})
			.done(function(data) {
				if(!data.success) {
					$('#teams').prepend('<p class="text-danger">' + data.errors.error + '</p>');
				} else {
					// $('[id="' + id + '"]').after('<p class="text-success">Removed ' + data.teamName + '</p>');

					teams.row($('[id="' + id + '"]')).remove().draw();
				}
			});
		}
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
