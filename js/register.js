$(document).ready(function() {

	var enabled = true;

	// create an account
	$('form').submit(function(event) {
		if(enabled) {
			enabled = false;
			$('.form-group').removeClass('has-error');
			$('.help-block').remove();
			var formData = {
				'role': $('select[name=role]').val(),
				'firstName': $('input[name=firstName]').val(),
				'lastName': $('input[name=lastName]').val(),
				'email': $('input[name=email]').val(),
				'password': $('input[name=password]').val(),
				'passwordConfirm': $('input[name=passwordConfirm]').val()
			};
			$.ajax({
				type: 'POST',
				url: 'handlers/register.php',
				data: formData,
				dataType: 'json',
				encode: true
			})
			.done(function(data) {
				enabled = true;
				if(!data.success) {
					if(data.errors.role) {
						$('#role-group').addClass('has-error');
						$('#role-group').append('<div class="help-block col-sm-4">' + data.errors.role + '</div>');
					}
					if(data.errors.firstName) {
						$('#firstName-group').addClass('has-error');
						$('#firstName-group').append('<div class="help-block col-sm-4">' + data.errors.firstName + '</div>');
					}
					if(data.errors.lastName) {
						$('#lastName-group').addClass('has-error');
						$('#lastName-group').append('<div class="help-block col-sm-4">' + data.errors.lastName + '</div>');
					}
					if(data.errors.email) {
						$('#email-group').addClass('has-error');
						$('#email-group').append('<div class="help-block col-sm-4">' + data.errors.email + '</div>');
					}
					if(data.errors.password) {
						$('#password-group').addClass('has-error');
						$('#password-group').append('<div class="help-block col-sm-4">' + data.errors.password + '</div>');
					}
					if(data.errors.passwordConfirm) {
						$('#password-confirm-group').addClass('has-error');
						$('#password-confirm-group').append('<div class="help-block col-sm-4">' + data.errors.passwordConfirm + '</div>');
					}
				} else {
					$('form').after('<p>An email has been sent to ' + data.email + '. If you did not recieve it, please check your spam folder.</p>');
					$('form').remove();
					// window.location.href = "account.php?created=1";
				}
			});
		}
		event.preventDefault();
	});

});
