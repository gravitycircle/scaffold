var image_field = (function($){
	var file_frame = false;
	var locked = false;
	return function(mTitle, mType, mSingle, mCallback) {
		if(!locked) {
			if (file_frame !== false){
				file_frame.close();
			}

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media({
				title: mTitle,
				library: {
					type: mType
				},
				button: {
					text: 'Use this Image',
				},
				multiple: !mSingle // Set to true to allow multiple files to be selected
			});

			// When an image is selected, run a callback.
			file_frame.on('select', function() {
				// We set multiple to false so only get one image from the uploader
				if(mSingle) {
					attachment = file_frame.state().get('selection').first().toJSON();
				}
				else{
					attachment = file_frame.state().get('selection').toJSON();
				}

				mCallback(attachment);
				
				file_frame.close();
				locked = false;
				file_frame = false;
			});

			// Finally, open the modal
			file_frame.open();
		}
	};
})(jQuery);