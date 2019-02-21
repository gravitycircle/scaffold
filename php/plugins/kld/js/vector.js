(function($){
	acf.add_action('load', function( $el ){
	
		// $el will be equivalent to $('body')
		
		$($el).find('.kld-vector-raster-preloaded .sizer').on('click', function(){
			var btn = $(this);
			var parent = $(this).closest('tr');
			var input = $(this).closest('.kld-vector-raster-input').find('input');
			$(this).closest('.kld-vector-raster-input').removeClass('kld-vector-raster-preloaded');
			image_field($(this).attr('data-title'), [$(this).attr('data-type')], true, function(attachment){
				$(btn).attr('data-id', attachment.id);
				$(btn).css({
					'background-image' : 'url('+attachment.url+')',
					'background-color' : '#ffffff'
				})

				var dr = $(parent).find('.s-raster').attr('data-id');
				var dv = $(parent).find('.s-vector').attr('data-id');

				if(!(dr == '' || dv == '')){
					input.val(dr+'-'+dv);
				}
				else{
					input.val('');
				}
			});
		});

		$($el).find('.kld-vector-raster-imgclear').on('click', function(){
			var parent = $(this).closest('.kld-vector-raster-input');

			$(parent).find('input').val('');

			$(parent).find('.sizer').attr('data-id', '');

			$(parent).find('.sizer').attr('style', '');
		});

		// find a specific field

		
	});
	acf.add_action('append', function( $el ){
	
		// $el will be equivalent to the new element being appended $('tr.row')

		$($el).find('.kld-vector-raster-input .sizer').on('click', function(){
			var btn = $(this);
			var parent = $(this).closest('tr');
			var input = $(this).closest('.kld-vector-raster-input').find('input');
			image_field($(this).attr('data-title'), [$(this).attr('data-type')], true, function(attachment){
				$(btn).attr('data-id', attachment.id);
				$(btn).css({
					'background-image' : 'url('+attachment.url+')',
					'background-color' : '#ffffff'
				})

				var dr = $(parent).find('.s-raster').attr('data-id');
				var dv = $(parent).find('.s-vector').attr('data-id');

				if(!(dr == '' || dv == '')){
					input.val(dr+'-'+dv);
				}
				else{
					input.val('');
				}
			});
		});

		$($el).find('.kld-vector-raster-imgclear').on('click', function(){
			var parent = $(this).closest('.kld-vector-raster-input');

			$(parent).find('input').val('');

			$(parent).find('.sizer').attr('data-id', '');

			$(parent).find('.sizer').attr('style', '');
		});
	});
})(jQuery);