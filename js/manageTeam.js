$(document).ready(function() {

	// data tables
	var players = $('#players').DataTable({
		'columnDefs': [{ 'targets': 0, 'checkboxes': true }],
		'order': [[2, 'asc']]
	});

	$('body').on('click', 'button.drawAddPlayer', function(event) {
		$("#addPlayerForm").slideToggle();
	});

	// add a player to the team
	$('body').on('click', 'button.addPlayer', function(event) {
		$('.form-group').removeClass('has-error');
		$('.help-block').remove();
		$('.text-danger').remove();
		var formData = {
			'firstName': $('input[name=firstName]').val(),
			'lastName': $('input[name=lastName]').val(),
			'parentEmail': $('input[name=parentEmail]').val(),
			'playerEmail': $('input[name=playerEmail]').val(),
			'dobM': $('select[name=dobM]').val(),
			'dobD': $('input[name=dobD]').val(),
			'dobY': $('input[name=dobY]').val(),
			'phone': $('input[name=phone]').val(),
			'jersey': $('input[name=jersey]').val(),
			'position': $('input[name=position]').val(),
			'graduating': $('input[name=graduating]').val(),
			'address': $('input[name=address]').val(),
			'city': $('input[name=city]').val(),
			'state': $('input[name=state]').val(),
			'zip': $('input[name=zip]').val(),
			'teamID': getUrlVars()["teamID"],
			'action': 'add'
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updatePlayer.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.firstName) {
					$('#firstName-group').addClass('has-error');
					$('#firstName-group').append('<div class="help-block col-sm-4">' + data.errors.firstName + '</div>');
				}
				if(data.errors.lastName) {
					$('#lastName-group').addClass('has-error');
					$('#lastName-group').append('<div class="help-block col-sm-4">' + data.errors.lastName + '</div>');
				}
				if(data.errors.parentEmail) {
					$('#parentEmail-group').addClass('has-error');
					$('#parentEmail-group').append('<div class="help-block col-sm-4">' + data.errors.parentEmail + '</div>');
				}
				if(data.errors.playerEmail) {
					$('#playerEmail-group').addClass('has-error');
					$('#playerEmail-group').append('<div class="help-block col-sm-4">' + data.errors.playerEmail + '</div>');
				}
				if(data.errors.phone) {
					$('#phone-group').addClass('has-error');
					$('#phone-group').append('<div class="help-block col-sm-4">' + data.errors.phone + '</div>');
				}
				if(data.errors.jerseyPositionGraduating) {
					$('#jerseyPositionGraduating-group').addClass('has-error');
					$('#jerseyPositionGraduating-group').append('<div class="help-block col-sm-4">' + data.errors.jerseyPositionGraduating + '</div>');
				}
				if(data.errors.address) {
					$('#address-group').addClass('has-error');
					$('#address-group').append('<div class="help-block col-sm-4">' + data.errors.address + '</div>');
				}
				if(data.errors.cityStateZip) {
					$('#cityStateZip-group').addClass('has-error');
					$('#cityStateZip-group').append('<div class="help-block col-sm-4">' + data.errors.cityStateZip + '</div>');
				}
				if(data.errors.dob) {
					$('#dob-group').addClass('has-error');
					$('#dob-group').append('<div class="help-block col-sm-4">' + data.errors.dob + '</div>');
				}
				if(data.errors.error) {
					$("#addPlayerForm").slideToggle(function() {
						$('button.btn-default.drawAddPlayer').after('<p class="text-danger"><br>' + data.errors.error + '</p>');
					});
				}
			} else {
				$('#addPlayerForm').find("input[type=text]").val("");
				$('#addPlayerForm').find("input[type=number]").val("");
				$('select[name=dobM]').prop('selectedIndex', 0);

				var row = players.row.add([
					null,
					"<a href='" + data.playerInfo.url + "' class='btn btn-sm btn-primary'><span class='glyphicon glyphicon-search'></span></a>",
					data.playerInfo.lastName,
					data.playerInfo.firstName,
					data.playerInfo.jersey,
					data.playerInfo.position,
					data.playerInfo.graduating,
					data.playerInfo.dob
				]).draw().node();
				$(row).attr('id', data.playerInfo.playerID);
			}
		});
		event.preventDefault();
	});

	// remove a player from the team
	$('body').on('click', 'button.removePlayer', function(event) {
		var playerIDs = $('.dt-checkboxes:checked').map(function() {
			return $(this).closest('tr').attr('id');
		}).get();
		$('.text-danger').remove();
		if(playerIDs.length == 0) {
			$('table[id="players"]').after('<p class="text-danger">No player(s) selected.</p>');
		} else {
			if(playerIDs.length == 1) {
				var message = "Are you sure you want to remove this player from the team?"
			} else {
				var message = "Are you sure you want to remove these players from the team?";
			}
			if(confirm(message)) {
				var formData = {
					'playerIDs': playerIDs,
					'teamID': getUrlVars()["teamID"],
					'action': 'remove'
				};
				$.ajax({
					type: 'POST',
					url: 'handlers/updateTeamPlayers.php',
					data: formData,
					dataType: 'json',
					encode: true
				})
				.done(function(data) {
					if(!data.success) {
						$('table[id="players"]').after('<p class="text-danger">' + data.errors.error + '</p>');
					} else {
						for(var i = 0; i < playerIDs.length; i++) {
							players.row($('[id="' + playerIDs[i] + '"]')).remove().draw();
						}
						$("input:checkbox").prop('checked', false);
					}
				});
			}
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
