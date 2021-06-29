var dashicons = ['admin-site', 'admin-site-alt', 'admin-site-alt2', 'admin-site-alt3', 'dashboard', 'admin-post', 'admin-media', 'admin-links', 'admin-page', 'admin-comments', 'admin-appearance', 'admin-plugins', 'plugins-checked', 'admin-users', 'admin-tools', 'admin-settings', 'admin-network', 'admin-home', 'admin-generic', 'admin-collapse', 'filter', 'admin-customizer', 'admin-multisite', 'welcome-write-blog', 'welcome-add-page', 'welcome-view-site', 'welcome-widgets-menus', 'welcome-comments', 'welcome-learn-more', 'format-aside', 'format-image', 'format-gallery', 'format-video', 'format-status', 'format-quote', 'format-chat', 'format-audio', 'camera', 'camera-alt', 'images-alt', 'images-alt2', 'video-alt', 'video-alt2', 'video-alt3', 'media-archive', 'media-audio', 'media-code', 'media-default', 'media-document', 'media-interactive', 'media-spreadsheet', 'media-text', 'media-video', 'playlist-audio', 'playlist-video', 'controls-play', 'controls-pause', 'controls-forward', 'controls-skipforward', 'controls-back', 'controls-skipback', 'controls-repeat', 'controls-volumeon', 'controls-volumeoff', 'image-crop', 'image-rotate', 'image-rotate-left', 'image-rotate-right', 'image-flip-vertical', 'image-flip-horizontal', 'image-filter', 'undo', 'redo', 'database-add', 'database', 'database-export', 'database-import', 'database-remove', 'database-view', 'align-full-width', 'align-pull-left', 'align-pull-right', 'align-wide', 'block-default', 'button', 'cloud-saved', 'cloud-upload', 'columns', 'cover-image', 'ellipsis', 'embed-audio', 'embed-generic', 'embed-photo', 'embed-post', 'embed-video', 'exit', 'heading', 'html', 'info-outline', 'insert', 'insert-after', 'insert-before', 'remove', 'saved', 'shortcode', 'table-col-after', 'table-col-before', 'table-col-delete', 'table-row-after', 'table-row-before', 'table-row-delete', 'editor-italic', 'editor-quote', 'editor-kitchensink', 'editor-removeformatting', 'editor-video', 'editor-help', 'editor-code', 'editor-table', 'align-left', 'align-right', 'align-center', 'lock', 'calendar-alt', 'post-status', 'edit', 'trash', 'sticky', 'external', 'arrow-up', 'arrow-down', 'arrow-right', 'arrow-left', 'arrow-up-alt', 'arrow-down-alt', 'arrow-right-alt', 'arrow-left-alt', 'arrow-up-alt2', 'arrow-down-alt2', 'arrow-right-alt2', 'arrow-left-alt2', 'sort', 'leftright', 'randomize', 'list-view', 'excerpt-view', 'grid-view', 'move', 'share', 'share-alt', 'share-alt2', 'rss', 'email', 'email-alt', 'email-alt2', 'networking', 'amazon', 'facebook', 'facebook-alt', 'google', 'instagram', 'linkedin', 'pinterest', 'podio', 'reddit', 'spotify', 'twitch', 'twitter', 'twitter-alt', 'whatsapp', 'xing', 'youtube', 'hammer', 'art', 'migrate', 'performance', 'universal-access', 'universal-access-alt', 'tickets', 'nametag', 'clipboard', 'heart', 'megaphone', 'schedule', 'tide', 'rest-api', 'code-standards', 'tag', 'category', 'archive', 'tagcloud', 'text', 'bell', 'yes', 'yes-alt', 'no', 'no-alt', 'plus', 'plus-alt', 'plus-alt2', 'minus', 'dismiss', 'marker', 'star-filled', 'star-half', 'star-empty', 'flag', 'warning', 'location', 'location-alt', 'vault', 'shield', 'shield-alt', 'sos', 'search', 'slides', 'text-page', 'analytics', 'chart-pie', 'chart-bar', 'chart-line', 'chart-area', 'groups', 'businessman', 'businesswoman', 'businessperson', 'id', 'id-alt', 'products', 'awards', 'forms', 'testimonial', 'portfolio', 'book', 'book-alt', 'download', 'upload', 'backup', 'clock', 'lightbulb', 'microphone', 'desktop', 'laptop', 'tablet', 'smartphone', 'phone', 'index-card', 'carrot', 'building', 'store', 'album', 'palmtree', 'tickets-alt', 'money', 'money-alt', 'smiley', 'thumbs-up', 'thumbs-down', 'layout', 'paperclip', 'color-picker', 'edit-large', 'edit-page', 'airplane', 'bank', 'beer', 'calculator', 'car', 'coffee', 'drumstick', 'food', 'fullscreen-alt', 'fullscreen-exit-alt', 'games', 'hourglass', 'open-folder', 'pdf', 'pets', 'printer', 'privacy', 'superhero', 'superhero-alt'];
(function($){
	acf.add_action('load', function( $el ){
	
		// $el will be equivalent to $('body')
		
		var dashicon_fields = $($el).find('.dashicons-preloaded').toArray();
		$($el).removeClass('.dashicons-preloaded');
		if($(dashicon_fields).length) {
			for(var i in dashicon_fields) {
				$(dashicon_fields[i]).removeClass('dashicon-pris');

				var thiselement = $(dashicon_fields[i]);

				for(var ci in dashicons) {
					$(thiselement).find('.dashicon-box').append('<div class="choice dashicons-acf-choice choice-'+dashicons[ci]+' dashicons dashicons-'+dashicons[ci]+'" data-choice="'+dashicons[ci]+'"></div>');
				}

				$(thiselement).find('.choice').on('click', function(){
					var el = $(this);
					var parent_el = $(this).closest('.kld-dash-input');
					$(parent_el).find('.choice').removeClass('selected');
					$(parent_el).find('.value-added').val($(el).attr('data-choice'));
					$(el).addClass('selected');

					$(parent_el).find('.dashicon-preview').attr('class', 'dashicon-preview dashicons dashicons-'+$(this).attr('data-choice'));
				});

				if($(thiselement).find('.value-added').val() != '' && $(thiselement).find('.value-added').val() != null) {
					console.log('click happens regardless', $(thiselement).find('.value-added').val());
					$(thiselement).find('.choice-'+$(thiselement).find('.value-added').val()).click();
				}
			}
		}
		// find a specific field

		
	});
	acf.add_action('append', function( $el ){
	
		// $el will be equivalent to the new element being appended $('tr.row')
		
		
		// find a specific field
		var dashicon_fields = $($el).find('.dashicon-pris').toArray();
		$($el).removeClass('.dashicons-model');
		if($(dashicon_fields).length) {
			for(var i in dashicon_fields) {
				$(dashicon_fields[i]).removeClass('dashicon-pris');

				var thiselement = $(dashicon_fields[i]);

				for(var i in dashicons) {
					$(thiselement).find('.dashicon-box').append('<div class="choice dashicons-acf-choice choice-'+dashicons[i]+' dashicons dashicons-'+dashicons[i]+'" data-choice="'+dashicons[i]+'"></div>');
				}

				$(thiselement).find('.choice').on('click', function(){
					var el = $(this);
					$(thiselement).find('.choice').removeClass('selected');

					$(thiselement).find('.value-added').val($(el).attr('data-choice'));
					$(el).addClass('selected');

					$(thiselement).find('.dashicon-preview').attr('class', 'dashicon-preview dashicons dashicons-'+$(this).attr('data-choice'));
				});

				if($(thiselement).find('.value-added').val() != '' && $(thiselement).find('.value-added').val() != null) {
					console.log('click happens regardless', $(thiselement).find('.value-added').val());
					$(thiselement).find('.choice-'+$(thiselement).find('.value-added').val()).click();
				}
			}
		}
		
		// do something to $field
		
	});
})(jQuery);