$(document).ready(function() {

	var teams = $('#teams').DataTable({
		'order': [[2, 'dsc']]
	});
	var tournaments = $('#tournaments').DataTable({
		'order': [[3, 'dsc']]
	});

	// create a team
	$('#createTeam').submit(function(event) {
		$('.text-success').remove();
		$('.form-group').removeClass('has-error');
		$('.help-block').remove();
		var formData = {
			'teamName': $('input[name=teamName]').val(),
			'action': 'add',
			'token': token
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateTeam.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.teamName) {
					$('#createTeam-group').addClass('has-error');
					$('#createTeam-group').append('<div class="help-block col-sm-4">' + data.errors.teamName + '</div>');
				}
			} else {
				$('#createTeam-group').addClass('has-success');
				$('#createTeam-group').append('<div class="help-block col-sm-4">Team successfully created!</div>');

				$('select[name=team] option:eq(0)').after('<option value="' + data.teamID + '">' + data.teamName + '</option>');

				var row = teams.row.add([
					"<a href='manageTeam.php?teamID=" + data.teamID + "' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-pencil'></span></a> " +
					"<button class='btn btn-danger btn-sm deleteTeam' id='" + data.teamID + "'><span class='glyphicon glyphicon-trash'></span></button>",
					data.teamName,
					data.date
				]).draw().node();
				$(row).attr('id', data.teamID);
				$(row).first().addClass('text-nowrap');
			}
		});
		event.preventDefault();
	});

	// delete a team
	$('body').on('click', 'button.deleteTeam', function(event) {
		if(confirm("Are you sure you want to delete this team? This action will be permanent.")) {
			$('.text-success').remove();
			$('.text-danger').remove();
			var formData = {
				'teamID': this.id,
				'action': 'remove',
				'token': token
			};
			$.ajax({
				type: 'POST',
				url: 'handlers/updateTeam.php',
				data: formData,
				dataType: 'json',
				encode: true
			})
			.done(function(data) {
				if(!data.success) {
					$('#teams').prepend('<p class="text-danger">' + data.errors.teamName + '</p>');
				} else {
					$('select[name=team] option[value="' + data.teamID + '"]').remove();

					// $('[id="' + data.teamID + '"]').after('<p class="text-success">Deleted ' + data.teamName + '</p>');
					teams.row($('[id="' + data.teamID + '"]')).remove().draw();
					tournaments.row($('[id*="' + data.teamID + '"]')).remove().draw();
				}
			});
		}
		event.preventDefault();
	});

	// join tournament
	$('#joinTournament').submit(function(event) {
		$('.text-success').remove();
		$('.form-group').removeClass('has-error');
		$('.help-block').remove();
		var formData = {
			'tournamentID': $('input[name=tournamentID]').val(),
			'teamID': $('select[name=team]').val(),
			'action': 'add',
			'token': token
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
				if(data.errors.team) {
					$('#team-group').addClass('has-error');
					$('#team-group').append('<div class="help-block col-sm-12">' + data.errors.team + '</div>');
				}
				if(data.errors.tournamentID) {
					$('#tournamentID-group').addClass('has-error');
					$('#tournamentID-group').append('<div class="help-block col-sm-12">' + data.errors.tournamentID + '</div>');
				}
			} else {
				window.location.href = data.url;
			}
		});
		event.preventDefault();
	});

	// leave tournament
	$('body').on("click", "button.leaveTournament", function(event) {
		if(confirm("Are you sure you want to leave this tournament?")) {
			$('.text-success').remove();
			$('.text-danger').remove();
			var id = this.id;
			var arr = id.split(',');
			var formData = {
				'tournamentID': arr[0],
				'teamID': arr[1],
				'action': 'remove',
				'token': token
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
					$('#tournaments').prepend('<p class="text-danger">' + data.errors.error + '</p>');
				} else {
					// $('[id="' + id + '"]').after('<p class="text-success">Left ' + data.tournamentName + '</p>');

					tournaments.row($('[id="' + id + '"]')).remove().draw();
				}
			});
		}
		event.preventDefault();
	});

});
