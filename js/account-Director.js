$(document).ready(function() {

	var tournaments = $('#tournaments').DataTable({
		'order': [[3, 'dsc']]
	});

	// create tournament
	$('#createTournament').submit(function(event) {
		$('.text-success').remove();
		$('.form-group').removeClass('has-error');
		$('.help-block').remove();
		var formData = {
			'tournamentName': $('input[name=tournamentName]').val(),
			'action': 'add',
			'token': token
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateTournament.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.tournamentName) {
					$('#tournamentName-group').addClass('has-error');
					$('#tournamentName-group').append('<div class="help-block col-sm-4">' + data.errors.tournamentName + '</div>');
				}
			} else {
				$('#tournamentName-group').addClass('has-success');
				$('#tournamentName-group').append('<div class="help-block col-sm-4">Tournament code is ' + data.tournamentID + '</div>');

				var row = tournaments.row.add([
					"<a href='manageTournament.php?tournamentID=" + data.tournamentID + "' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-pencil'></span></a> " +
					"<button class='btn btn-danger btn-sm deleteTournament' id='" + data.tournamentID + "'><span class='glyphicon glyphicon-trash'></span></button>",
					data.tournamentName,
					data.tournamentID,
					data.date
				]).draw().node();
				$(row).attr('id', data.tournamentID);
				$(row).first().addClass('text-nowrap');
			}
		});
		event.preventDefault();
	});

	// delete tournament
	$('body').on("click", "button.deleteTournament", function(event) {
		if(confirm("Are you sure you want to delete this tournament? This action will be permanent.")) {
			$('.text-success').remove();
			$('.text-danger').remove();
			var formData = {
				'tournamentID': this.id,
				'action': 'remove',
				'token': token
			};
			$.ajax({
				type: 'POST',
				url: 'handlers/updateTournament.php',
				data: formData,
				dataType: 'json',
				encode: true
			})
			.done(function(data) {
				if(!data.success) {
					$('#tournaments').prepend('<p class="text-danger">' + data.errors.error + '</p>');
				} else {
					// $('[id="' + data.tournamentID + '"]').after('<p class="text-success">Tournament ' + data.tournamentName + ' deleted</p>');

					tournaments.row($('[id="' + data.tournamentID + '"]')).remove().draw();
				}
			});
		}
		event.preventDefault();
	});

});
