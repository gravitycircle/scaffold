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
		$('.sort-left, .options-avail, .sort-right, .sort-register').sortable({
			connectWith: '.for-sorter',
			handle: '.dashicons',
			receive: function(event, ui) {
				// so if > 10
				if($(this).hasClass('sort-left') || $(this).hasClass('sort-right')){
					if ($(this).children().length > 3) {
						//ui.sender: will cancel the change.
						//Useful in the 'receive' callback.
						$(ui.sender).sortable('cancel');
					}
				}
				else if($(this).hasClass('sort-register')) {
					if ($(this).children().length > 1) {
						//ui.sender: will cancel the change.
						//Useful in the 'receive' callback.
						$(ui.sender).sortable('cancel');
					}
				}
			},
			stop: function() {
				var leftItems = $('.sort-left').find('.page-handle').toArray();
				var rightItems = $('.sort-right').find('.page-handle').toArray();
				var regItems = $('.sort-register').find('.page-handle').toArray();


				var leftValue = [];
				var rightValue = [];
				var regValue = [];

				for(var l in leftItems) {
					leftValue.push($(leftItems[l]).attr('data-value'));
				}

				for(var r in rightItems) {
					rightValue.push($(rightItems[r]).attr('data-value'));
				}

				for(var g in regItems) {
					regValue.push($(regItems[g]).attr('data-value'));
				}

				if(leftValue.length >= 1) {
					$('#nav-left').val(JSON.stringify(leftValue));
				}
				else{
					$('#nav-left').val('');
				}

				if(rightValue.length >= 1) {
					$('#nav-right').val(JSON.stringify(rightValue));
				}
				else{
					$('#nav-right').val('');
				}

				if(regValue.length >= 1) {
					$('#nav-register').val(JSON.stringify(regValue));
				}
				else{
					$('#nav-register').val('');
				}
			}
		});
	});
})(jQuery);