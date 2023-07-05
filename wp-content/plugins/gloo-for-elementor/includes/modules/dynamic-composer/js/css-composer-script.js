//String.prototype.cleanup = function() {
//    return this.toLowerCase().replace(/[^a-zA-Z0-9]+/g, "");
// }
if (typeof gloo_string_clean != 'function') {
	function gloo_string_clean(raw_string){
		return raw_string.toLowerCase().replace(/[^a-zA-Z0-9]+/g, "");
	}
}


 jQuery( document ).ready(function($) {

    $(document).on('DOMNodeInserted', 'body', function (e) {
        
        $('.gloo_list_variable_name').find("input[type='text']").each(function(){            
            $(this).on("keypress, keydown, keyup", function(){
                newValue = gloo_string_clean($(this).val());
				//$(this).val().cleanup();
                $(this).val(newValue);
                //$(this).val('$'+newValue);
            });
            $(this).on("blur", function(){
                newValue = gloo_string_clean($(this).val());
				//newValue = $(this).val().cleanup();
                $(this).val(newValue);
                //$(this).val('$'+newValue);
            });
        });

        /*$('.otw_variable_name').find("input[type='text']").on("keypress", function(){
            newValue = "["+$(this).val().cleanup()+"]";
            $(this).val(newValue);
            $(this).parents(".elementor-repeater-fields").find(".elementor-repeater-row-item-title").text(newValue);
        });*/

        /*$('.otw_variable_name').find("input[type='text']").each(function(){
            newValue = "["+$(this).val().cleanup()+"]";
            $(this).val(newValue);
            //$(this).parents(".elementor-repeater-fields").find(".elementor-repeater-row-item-title").text(newValue);
        });*/
    });


    
    //console.log($('.otw_variable_name'));
    /**/

    /*$('.otw_variable_name').find("input[type='text']").on('keypress', function(){
        alert("sdf");
    });*/

    function gloo_addCustomCss(css, context) {
        if (!context) {
            return;
        }
        
        var model = context.model,
            customCSS = model.get('settings').get('gloo_custom_css');
        var selector = '.elementor-element.elementor-element-' + model.get('id');

        if ('document' === model.get('elType')) {
            selector = elementor.config.document.settings.cssWrapperSelector;
        }

        if (customCSS) {
            var gloo_css_variables = model.get('settings').get('gloo_css_variables'); //.get('gloo_custom_css_description')
            //var gloo_css_variables = model.get('settings').get('gloo_css_variables');
            if(typeof gloo_css_variables === 'object' && gloo_css_variables !== null){
                gloo_css_variables.each( function( items, indexs ) {
                    customCSS = customCSS.replace(items.attributes.list_variable_name, items.attributes.list_variable_value);
                });
            }
            
            css += customCSS.replace(/selector/g, selector);
        }

        return css;
	}

	function gloo_addPageCustomCss() {
		var customCSS = elementor.settings.page.model.get('gloo_custom_css');
		if (customCSS) {
			customCSS = customCSS.replace(/selector/g, elementor.config.settings.page.cssWrapperSelector);
			elementor.settings.page.getControlsCSS().elements.$stylesheetElement.append(customCSS);
		}
    }
    
    elementor.hooks.addFilter('editor/style/styleText', gloo_addCustomCss);
	//elementor.settings.page.model.on('change', gloo_addPageCustomCss);
	//elementor.on('preview:loaded', gloo_addPageCustomCss);
});
 