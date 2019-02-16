var repeaters = (function($){
	var row = '<tr><td class="left"><input type="text" class="repeater-head" value="" style="width: 100%;" placeholder="Platform Name"></td><td class="right"><input type="text" class="repeater-value" value="" style="width: 100%;" placeholder="API Key"></td><td style="vertical-align: middle;"><span class="button removal-button"><span class="dashicons dashicons-no-alt"></span><span>Remove</span></span></td></tr>';

	return {
		initialize : function($element){
			$($element).find('.add-option').on('click', function(){
				//CREATE NEW ROW
				$($element).find('.custom-repeater-options tbody').append(row);

				//RESET TYPE ACTIONS
				$($element).find('.custom-repeater-options tbody input').off('keyup');
				$($element).find('.custom-repeater-options tbody input').on('keyup', function(){
					var val = $(this).val();
					var heads = $($element).find('.custom-repeater-options .repeater-head').toArray();
					var values = $($element).find('.custom-repeater-options .repeater-value').toArray();
					var form_object = [];
					for(var gg in heads) {
						if($(heads[gg]).val() != '' && $(values[gg]).val() != '') {
							form_object.push({
								'api': $(heads[gg]).val(),
								'key':$(values[gg]).val()
							});
						}
					}
					$($element).find('.repeater-final').val(JSON.stringify(form_object));
				});

				//RESET REMOVAL ACTIONS
				$($element).find('.custom-repeater-options .removal-button').off('click');

				$($element).find('.custom-repeater-options .removal-button').on('click', function(){
					var for_removal = $(this).parent().parent();
					$(this).off('click');
					$(for_removal).transition({
						'opacity' : 0
					}, 600, function(){
						$(for_removal).remove();
						var heads = $($element).find('.custom-repeater-options .repeater-head').toArray();
						var values = $($element).find('.custom-repeater-options .repeater-value').toArray();
						var form_object = [];
						for(var gg in heads) {
							if($(heads[gg]).val() != '' && $(values[gg]).val() != '') {
								form_object.push({
									'api': $(heads[gg]).val(),
									'key':$(values[gg]).val()
								});
							}
						}
						$($element).find('.repeater-final').val(JSON.stringify(form_object));
					});
				});
			});
			$($element).find('.add-option').click();
		}
	};

})(jQuery);
(function($){
	$(document).ready(function(){
		$('.edit-image').on('click', function(){
			var el = $(this);
			image_field('Insert Logo', JSON.parse($(el).attr('data-file-types')), true, function(img){
				$('#'+$(el).attr('data-target')).find('input').val(img.id);
				$('#'+$(el).attr('data-target')+' .image-container').empty();
				$('#'+$(el).attr('data-target')+' .image-container').html('<img src="'+img.url+'" alt="Logo" />');
				$(el).html('Change Logo');
			});
		});
		var repeater_elements = $('.repeater_option').toArray();
		for(var i in repeater_elements) {
			repeaters.initialize($(repeater_elements[i]));
		}
	});
})(jQuery);