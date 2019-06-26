grecaptcha.render( 'g-recaptcha', {
	'sitekey' : '<Your "Site Key" from the recaptcha console>',
	'size' : 'invisible',
	'callback': function(gresponse){
		//do what you want here, but gresponse is a string response from Google
		//you need to include this to your form before submission. Here's one way
		//to do it:
		$('form').append('<input type="hidden" name="g-response" value="'+gresponse+'" />');
	}
});



$('.submit-button').on('click', function(ev){
	ev.preventDefault();
	grecaptcha.execute();
	$('form').submit();
});

