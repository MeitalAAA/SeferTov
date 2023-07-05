(function($) {

    jQuery( window ).on( 'elementor/frontend/init', () => {
        const addHandler = ( $element ) => {

            var fields = $element.find('input.gloo-time-span-field');
            console.log(window.lightpick);
            if( typeof fields != 'undefined' ) {
              
                fields.each(function( key, val) {
                    var settings = $(this).data('field-settings');
                    var field_id = $(this).attr('id');
                                        
                    if( typeof field_id != 'undefined' && field_id != null ) {
                        settings.field_id = field_id;

                        if( !settings.start_date_field_id ) {
                            settings.start_value = settings.start_date;
                        }   

                        if( !settings.end_date_field_id ) {
                            settings.end_value = settings.end_date;
                        }   

                        if(settings.is_hidden) {
                            $(this).closest(".elementor-field-group").find('label').hide();
                        }

                        if( settings.start_date_field_id ) {

                            jQuery(document).on('keyup change', '#form-field-'+settings.start_date, function(){
                                settings.start_value = jQuery(this).val();
                                update_calculation_value(settings);
                            });
                        }

                        if( settings.end_date_field_id ) {

                            jQuery(document).on('keyup change', '#form-field-'+settings.end_date, function(){
                                settings.end_value = jQuery(this).val();
                                update_calculation_value(settings);
                            });
                        }
                        console.log(settings);
                        update_calculation_value(settings);
                    }
                });
            }
        };

        elementorFrontend.hooks.addAction( 'frontend/element_ready/form.default', addHandler );

    });
     
})(jQuery);
 
function update_calculation_value(settings) {
    
    if( (typeof settings.start_value == 'undefined' || typeof settings.end_value == 'undefined') ||  ( settings.start_value == '' || settings.end_value == '') ) {
        return false;
    }

    const dateB = moment(settings.start_value, settings.start_format);
    const dateA = moment(settings.end_value, settings.end_format);

    if(dateA == 'invalid' && dateB == 'invalid') {
        return false;
    }
 
    if(settings.output != 'custom' ) {
        input_calculated_value = dateA.diff(dateB, settings.output);
    } else if( settings.output == 'custom' && typeof settings.output_custom != 'undefined' && settings.output_custom != '') {

        var tags = settings.included_output;
        var custom_string = settings.output_custom;
        
        if (typeof tags !== 'undefined' && tags.length > 0) {
            var i;
            for (i = 0; i < tags.length; ++i) {
                console.log(tags[i]);    
                calculated_value = dateA.diff(dateB, tags[i]);
                custom_string = custom_string.replace('%'+tags[i]+'%', calculated_value);
            }

            input_calculated_value =  custom_string;
        }
        console.log(custom_string);
    }
    

    jQuery('#'+settings.field_id).val(input_calculated_value).closest(".elementor-field-group").find('.time_span_field_value').find(".time_span_field_result").html(input_calculated_value);
}
