(function($) {

    jQuery( window ).on( 'elementor/frontend/init', () => {
        const addHandler = ( $element ) => {
            
            var fields = $element.find('input[type="gloo_datepicker_field"]');
            
            if( typeof fields != 'undefined' ) {
                fields.each(function( key, val) {
                    var settings = $(this).data('config');
                    var field_id = $(this).attr('id');
                    
                    if( typeof field_id != 'undefined' && field_id != null ) {
                        
                        settings.field = document.getElementById(field_id);

                        settings.onSelect = function(start, end){
                           $('#'+field_id).trigger('change');
                        }

                        var picker = new Lightpick(settings);
                    }
                });
            }
        };

        elementorFrontend.hooks.addAction( 'frontend/element_ready/form.default', addHandler );
    });
})(jQuery);
