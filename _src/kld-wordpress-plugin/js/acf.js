var dashicons = ['menu', 'admin-site', 'dashboard', 'admin-post', 'admin-media', 'admin-links', 'admin-page', 'admin-comments', 'admin-appearance', 'admin-plugins', 'admin-users', 'admin-tools', 'admin-settings', 'admin-network', 'admin-home', 'admin-generic', 'admin-collapse', 'welcome-write-blog', 'welcome-add-page', 'welcome-view-site', 'welcome-widgets-menus', 'welcome-comments', 'welcome-learn-more', 'format-aside', 'format-image', 'format-gallery', 'format-video', 'format-status', 'format-quote', 'format-chat', 'format-audio', 'camera', 'images-alt', 'images-alt2', 'video-alt', 'video-alt2', 'video-alt3', 'image-crop', 'image-rotate-left', 'image-rotate-right', 'image-flip-vertical', 'image-flip-horizontal', 'undo', 'redo', 'editor-bold', 'editor-italic', 'editor-ul', 'editor-ol', 'editor-quote', 'editor-alignleft', 'editor-aligncenter', 'editor-alignright', 'editor-insertmore', 'editor-spellcheck', 'editor-distractionfree', 'editor-kitchensink', 'editor-underline', 'editor-justify', 'editor-textcolor', 'editor-paste-word', 'editor-paste-text', 'editor-removeformatting', 'editor-video', 'editor-customchar', 'editor-outdent', 'editor-indent', 'editor-help', 'editor-strikethrough', 'editor-unlink', 'editor-rtl', 'align-left', 'align-right', 'align-center', 'align-none', 'lock', 'calendar', 'visibility', 'post-status', 'edit', 'trash', 'arrow-up', 'arrow-down', 'arrow-right', 'arrow-left', 'arrow-up-alt', 'arrow-down-alt', 'arrow-right-alt', 'arrow-left-alt', 'arrow-up-alt2', 'arrow-down-alt2', 'arrow-right-alt2', 'arrow-left-alt2', 'sort', 'leftright', 'list-view', 'exerpt-view', 'share', 'share-alt', 'share-alt2', 'twitter', 'rss', 'facebook', 'facebook-alt', 'googleplus', 'networking', 'hammer', 'art', 'migrate', 'performance', 'wordpress', 'wordpress-alt', 'pressthis', 'update', 'screenoptions', 'info', 'cart', 'feedback', 'cloud', 'translation', 'tag', 'category', 'yes', 'no', 'no-alt', 'plus', 'minus', 'dismiss', 'marker', 'star-filled', 'star-half', 'star-empty', 'flag', 'location', 'location-alt', 'vault', 'shield', 'shield-alt', 'search', 'slides', 'analytics', 'chart-pie', 'chart-bar', 'chart-line', 'chart-area', 'groups', 'businessman', 'id', 'id-alt', 'products', 'awards', 'forms', 'portfolio', 'book', 'book-alt', 'download', 'upload', 'backup', 'lightbulb', 'smiley'];
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