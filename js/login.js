$(document).ready(function() {

	// login to the site
	$('body').on('click', 'button.login', function(event) {
		$('.form-group').removeClass('has-error');
		$('.help-block').remove();
		var formData = {
			'email': $('input[name=email]').val(),
			'password': $('input[name=password]').val()
		};
		$.ajax({
			type: 'POST',
			url: 'handlers/login.php',
			data: formData,
			dataType: 'json',
			encode: true
		})
		.done(function(data) {
			if(!data.success) {
				if(data.errors.error) {
					$('#email-group').addClass('has-error');
					$('#email-group').append('<div class="help-block col-sm-4">' + data.errors.error + '</div>');
				}
			} else {
				window.location.href = "account.php";
			}
		});
		event.preventDefault();
	});

});
