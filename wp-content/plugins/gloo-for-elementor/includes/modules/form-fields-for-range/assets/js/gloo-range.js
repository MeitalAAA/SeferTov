(function($) {

    jQuery( window ).on( 'elementor/frontend/init', () => {
        const addHandler = ( $element ) => {
            
            var fields = $element.find('input.gloo-range-field');

            if( typeof fields != 'undefined' ) {
              
                fields.each(function( key, val) {
                    var settings = $(this).data('field-settings');
                    var field_id = $(this).attr('id');

                    if (window.matchMedia('(max-width: 767px)').matches) {
                        settings.width = settings.width.mobile_width;
                    } else if(window.matchMedia('(max-width: 991px)').matches) {
                        settings.width = settings.width.tablet_width;
                    } else {
                        settings.width = settings.width.desktop_width;
                    }
  
                    if( typeof field_id != 'undefined' && field_id != null ) {
                        settings.target = '#'+field_id;
                        settings.onChange = function (values) {
                            jQuery(settings.target).val(values).trigger('change');
                        }
                        var mySlider = new rSlider(settings);
                    }
                });
            }
        };

        elementorFrontend.hooks.addAction( 'frontend/element_ready/form.default', addHandler );
    });
    
})(jQuery);
