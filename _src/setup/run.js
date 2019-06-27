var comm = (function($){
	return {
		call_home : function(url, callbk){
			$.ajax({
				url: url,
			}).done(function(data) {
				callbk(JSON.parse(data));
			});
		}
	};
})(jQuery);

(function($){
	$(document).ready(function(){
		$('#begin').on('click', function(){
			//console.log($(this).attr('d-target')+'download.php');
			var el = $(this);
			$(el).css({
				'pointer-events' : 'none',
				'opacity' : 0.8
			});

			$(el).html('Downloading Wordpress...');

			comm.call_home($(el).attr('d-target')+'_src/setup/download.php', function(resp) {
				if(resp.status == 'ok') {
					$(el).html('Success. Setting up directories...');

					comm.call_home($(el).attr('d-target')+'_src/setup/unzip.php', function(resp) {
						if(resp.status == 'ok') {
							$(el).html('Complete. Please wait.');

							setTimeout(function(){
								$('.content-rendered').animate({
									'opacity' : 0
								}, 1000, function(){
									window.location = $(el).attr('d-target')+'introduction.php';
								});
							}, 3000);
						}
						else {
							$(el).html('Directory Error - '+resp.debug);
						}
					});
				}
				else {
					$(el).html('Download Error - '+resp.debug);
				}
			});
		});
	});
})(jQuery);