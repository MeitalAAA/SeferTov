(function($) {

    jQuery( window ).on( 'elementor/frontend/init', () => {
        const addHandler = ( $element ) => {
            
            var fields = $element.find('input[type="tel"].gloo-intlTelInput');

            if( typeof fields != 'undefined' ) {
                fields.each(function( key, val) {
                    var settings = $(this).parents('.elementor-field-group').data('config');
                    var field_id = $(this).attr('id');
                    console.log(settings);
                    
                    if( typeof field_id != 'undefined' && field_id != null ) {
                        var input = document.getElementById(field_id);
                        window.intlTelInput(input,settings);
                    }
                });
            }
        };

        elementorFrontend.hooks.addAction( 'frontend/element_ready/form.default', addHandler );
    });
})(jQuery);
