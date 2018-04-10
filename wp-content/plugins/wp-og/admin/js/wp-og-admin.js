(function( $ ) {
	'use strict';
	 $(function() {
	 	wp_og_media_init('#og-image', '#select_og_image');
	 });
    var wp_og_media_init = function(selector, button_selector)  {
        var clicked_button = false;
        $(selector).each(function (i, input) {
            var button = $(input).next(button_selector);
            button.click(function (event) {
                event.preventDefault();
                var selected_img;
                clicked_button = $(this);
     
                // check for media manager instance
                if(wp.media.frames.wp_og_frame) {
                    wp.media.frames.wp_og_frame.open();
                    return;
                }
                // configuration of the media manager new instance
                wp.media.frames.wp_og_frame = wp.media({
                    title: 'Select image',
                    multiple: false,
                    library: {
                        type: 'image'
                    },
                    button: {
                        text: 'Use selected image'
                    }
                });
     
                // Function used for the image selection and media manager closing
                var wp_og_media_set_image = function() {
                    var selection = wp.media.frames.wp_og_frame.state().get('selection');
     
                    // no selection
                    if (!selection) {
                        return;
                    }
     
                    // iterate through selected elements
                    selection.each(function(attachment) {
                        var url = attachment.attributes.url;
                        clicked_button.prev(selector).val(url);
                    });
                };
     
                // closing event for media manger
                wp.media.frames.wp_og_frame.on('close', wp_og_media_set_image);
                // image selection event
                wp.media.frames.wp_og_frame.on('select', wp_og_media_set_image);
                // showing media manager
                wp.media.frames.wp_og_frame.open();
            });
       });
    };
})( jQuery );
