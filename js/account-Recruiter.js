$(document).ready(function() {

	var tournaments = $('#tournaments').DataTable({
		'order': [[3, 'dsc']]
	});

	// add recruiter
	$('#getRosters').submit(function(event) {
		$('.text-success').remove();
		$('.form-group').removeClass('has-error');
		$('.help-block').remove();
		var formData = {
			'tournamentID': $('input[name=tournamentID]').val(),
			'action': 'add',
			'token': token
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateRecruiters.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.tournamentID) {
					$('#getRosters-group').addClass('has-error');
					$('#getRosters-group').append('<div class="help-block col-sm-4">' + data.errors.tournamentID + '</div>');
				}
			} else {
				$('#getRosters-group').addClass('has-success');
				$('#getRosters-group').append('<div class="help-block col-sm-4">Successfully joined ' + data.tournamentName + '</div>');

				var row = tournaments.row.add([
					"<a href='" + data.viewURL + "' class='btn btn-primary btn-sm'><span class='glyphicon glyphicon-search'></span></a> " +
					"<button class='btn btn-danger btn-sm removeRecruiter' id='" + data.tournamentID + "'><span class='glyphicon glyphicon-trash'></span></button>",
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

	// remove recruiter
	$('body').on("click", "button.removeRecruiter", function(event) {
		if(confirm("Are you sure you want to leave this tournament?")) {
			$('.text-success').remove();
			$('.text-danger').remove();
			var formData = {
				'tournamentID': this.id,
				'action': 'remove'
			};
			$.ajax({
				type: 'POST',
				url: 'handlers/updateRecruiters.php',
				data: formData,
				dataType: 'json',
				encode: true
			})
			.done(function(data) {
				if(!data.success) {
					$('#tournaments').before('<p class="text-danger">Error leaving tournament.</p>');
				} else {
					// $('div[id="' + data.tournamentID + '"]').after('<p class="text-success">Successfully left ' + data.tournamentName + '</p>');

					tournaments.row($('[id="' + data.tournamentID + '"]')).remove().draw();
				}
			});
		}
		event.preventDefault();
	});

});
