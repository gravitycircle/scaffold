var actions = (function($){

})(jQuery);

//main
(function($){
	$(document).ready(function(){
		var deletes = $('.action_del').toArray();

		if(deletes.length >= 1)  {
			$('.action_del').on('click', function(e){
				e.preventDefault();
				var target = $(this).attr('href');
				var idt = $(this).attr('data-id');
				var fader = $(this).closest('tr');
				var reload = $(this).attr('data-reload');
				var x = confirm('Are you sure you want to delete this entry? This action cannot be undone.');
				if(x) {
					$.ajax({
						type: "POST",
						url: target,
						data: {
							del : idt
						},
						success: function(response){
							$(fader).transition({
								'opacity' : 0
							}, 600, function(){
								$(fader).remove();
								if($('.sentry').toArray().length < 1) {
									window.location = reload;
								}
							});
						}
					});
				}
			});
		}

		var views = $('.view-related').toArray();

		if(views.length >= 1){
			$('.view-related').on('click', function(e){
				e.preventDefault();
				var t = $(this).attr('data-url');

				if($('#to-view').val() != '---') {
					window.location = t+'&v='+$('#to-view').val();
				}
				else{
					alert('Select a page / page version on the dropdown above.');
				}
			});
		}

		var single_deletes = $('.single_del').toArray();

		if(single_deletes.length >= 1) {
			$('.single_del').on('click', function(e){
				e.preventDefault();
				var target = $(this).attr('href');
				var idt = $(this).attr('data-id');
				var reload = $(this).attr('data-reload');

				var x = confirm('Are you sure you want to delete this entry? This action cannot be undone.');
				if(x) {
					$.ajax({
						type: "POST",
						url: target,
						data: {
							del : idt
						},
						success: function(response){
							window.location = reload;
						}
					});
				}
			});
		}

		var single_expanded_deletes = $('.single_expanded_del').toArray();

		if(single_expanded_deletes.length >= 1) {
			$('.single_expanded_del').on('click', function(e){
				e.preventDefault();
				var target = $(this).attr('href');
				var idt = $(this).attr('data-id');
				var reload = $(this).attr('data-reload');
				var forremove = $(this).closest('.postbox');

				var x = confirm('Are you sure you want to delete this entry? This action cannot be undone.');
				if(x) {
					$.ajax({
						type: "POST",
						url: target,
						data: {
							del : idt
						},
						success: function(response){
							$(forremove).transition({
								'opacity' : 0
							}, 600, function(){
								$(forremove).remove();
								
								if($('.single-entry').toArray().length < 1) {
									window.location = reload;
								}
							});
						}
					});

				}
			});
		}

		var bulk_actions = $('.bulk-actions').toArray();

		if(bulk_actions.length >= 1) {
			$('.bulk-actions').on('click', function(e) {
				e.preventDefault();
				

				if($('#to-act').val() != '---') {
					var checkboxes = $('.for-action').toArray();
					var get_act = [];
					var target = $(this).attr('data-download');
					var reload = $(this).attr('data-reload');
					for(var i in checkboxes) {
						if($(checkboxes[i]).prop('checked')) {
							get_act.push($(checkboxes[i]).val());
						}
					}
					
					if(get_act.length < 1) {
						alert('There are no entries selected. Please select at least one entry to continue.');
					}
					else {
						if($('#to-act').val() == 'download') {
							window.location = target+'?download='+get_act.join('-');
						}
						else if($('#to-act').val() == 'delete') {
							// data-delete
							var continue_deleting = confirm('Are you sure you want to delete the selected entries? This action cannot be undone.');
							if(continue_deleting) {
								$.ajax({
									type: "POST",
									url: target,
									data: {
										'batch-del' : get_act.join('-')
									},
									success: function(response){
										var batchanimate = [];
										var tr = 0;
										for(var gg in get_act) {
											batchanimate.push('.bulk-'+get_act[gg]);
										}

										$(batchanimate.join(', ')).transition({
											'opacity' : 0
										}, 600, function(){
											$(this).remove();
											if(tr === 0) {
												if($('.sentry').toArray().length < 1) {
													window.location = reload;
												}
											}
											tr = 1;
										});
									}
								});
							}
						}
					}
				}
				else{
					alert('Select an action on the dropdown above.');
				}
				
			});
		}
	});
})(jQuery);