$(document).ready(function() {

	// update account info
	$('body').on('click', 'button.saveChanges', function(event) {
		$('.form-group').removeClass('has-error').removeClass('has-success');
		$('.help-block').remove();
		$('.text-danger').remove();
		$('.text-success').remove();
		var formData = {
			'firstName': $('input[name=firstName]').val(),
			'lastName': $('input[name=lastName]').val(),
			'oldPassword': $('input[name=oldPassword]').val(),
			'newPassword': $('input[name=newPassword]').val(),
			'newPasswordConfirm': $('input[name=newPasswordConfirm]').val()
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/updateAccount.php',
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
				if(data.errors.oldPassword) {
					$('#oldPassword-group').addClass('has-error');
					$('#oldPassword-group').append('<div class="help-block col-sm-4">' + data.errors.oldPassword + '</div>');
				}
				if(data.errors.newPassword) {
					$('#newPassword-group').addClass('has-error');
					$('#newPassword-group').append('<div class="help-block col-sm-4">' + data.errors.newPassword + '</div>');
				}
				if(data.errors.newPasswordConfirm) {
					$('#newPasswordConfirm-group').addClass('has-error');
					$('#newPasswordConfirm-group').append('<div class="help-block col-sm-4">' + data.errors.newPasswordConfirm + '</div>');
				}
			} else {
				$('#firstName-group').addClass('has-success');
				$('#lastName-group').addClass('has-success');
				$('.saveChanges').before('<p class="text-success">Account successfully updated!</p>');
				
				$('input[name=oldPassword]').val('');
				$('input[name=newPassword]').val('');
				$('input[name=newPasswordConfirm]').val('');
			}
		});
		event.preventDefault();
	});
});
