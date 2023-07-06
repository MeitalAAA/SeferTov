jQuery(document).ready(function($){
	if($(".gloo_required_message_input").length >= 1){
		
		function gloo_required_message_input(input_object){
			
			if($("input[name='"+input_object.attr('name')+"']").length >= 1){
        
				is_checkbox_field_required = true;
				$("input[name='"+input_object.attr('name')+"']").each(function(){
          
					if($(this).is(':checked')){            
						is_checkbox_field_required = false;
						return;
					}
				});
				if(is_checkbox_field_required){          
					input_object.parents('.elementor-field-group').find(".gloo_required_message_input").val('');
				}else{
					input_object.parents('.elementor-field-group').find(".gloo_required_message_input").val('required');
				}				
			}
		}

		$("body").on("change", ".gloo-child-post input", function(){
			gloo_required_message_input($(this));
		});
		$("body").on("change", ".gloo-parent-post input", function(){
			gloo_required_message_input($(this));
		});
		
		$(".gloo-parent-post").each(function(){
			gloo_required_message_input($(this).find('input'));
		});		

	}	
});