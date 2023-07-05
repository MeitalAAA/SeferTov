jQuery.fn.outerHTML = function() {
    return jQuery('<div />').append(this.eq(0).clone()).html();
};

var gloo_repeater_html = Array();
// var gloo_repeater_html = '';
jQuery(document).ready(function($){

    function fix_field_names_ids(){
        if($('.gloo_repeater_field_wrapper').length >= 1){
            $('.gloo_repeater_field_wrapper').each(function(){
                wrapper_item = $(this);
                $(this).find('li').each(function(index, item){
                    $(this).find(".gloo_repeater_field").each(function(){
                        
                        field_type = $(this).attr('data-field-type');
                        field_id = $(this).attr('data-field-id');
                        field_input = $(this).attr('data-field-input');
                        field_input_type = $(this).attr('data-field-input-type');

                        if(field_input_type == 'radio'){
                            field_name = 'form_fields['+field_id+']['+index+']';
                            $(this).find(field_input).attr('name', field_name);
                        }else if(field_input_type == 'checkbox'){
                            field_name = 'form_fields['+field_id+']['+index+'][]';
                            $(this).find(field_input).attr('name', field_name);
                        }
                    });
                });
                
            });
        }
    }

    function display_gloo_repeater_existing_values(gloo_start_repeater_item){
        repeater_post_data = gloo_start_repeater_item.attr('data-repeater-post-data');
        repeater_data_index = gloo_start_repeater_item.attr('data-index');
        // .gloo_repeater_end_field button

        if(repeater_post_data){
            repeater_post_data_object = JSON.parse(repeater_post_data);
            if(typeof repeater_post_data_object == 'object' && typeof repeater_post_data_object['item-0'] == 'object'){
                // var size = Object.keys(repeater_post_data_object).length;
                var inner_loop = 0;
                for (const key in repeater_post_data_object) {
                    $(".gloo_repeater_end_field[data-index='"+repeater_data_index+"']").find('button').trigger('click');

                    
                    for (const sub_key in repeater_post_data_object[key]) {

                        $($("ul[data-index='"+repeater_data_index+"'] li")[inner_loop]).find(".gloo_repeater_field_"+repeater_data_index).each(function(){
                            data_field_id = $(this).attr('data-source-field-id');
                            data_field_input_type = $(this).attr('data-field-input');
                            if(data_field_input_type && data_field_id == sub_key){
                                
                                field_type = $(this).attr('data-field-type');
                                if(field_type == 'textarea'){
									$(this).find(data_field_input_type).text(repeater_post_data_object[key][sub_key]);
								 }
                                else if(!(field_type == 'checkbox' || field_type == 'radio')){
                                    $(this).find(data_field_input_type).attr('value', repeater_post_data_object[key][sub_key]);
                                }
                            }
                        });
                        
                    }
                    inner_loop++;
                }
            }
            
            
        }
    }

    function add_repeater_mapping_data(gloo_start_repeater_item, gloo_repeater_item){

        repeater_mapping_data = gloo_start_repeater_item.attr('data-repeater-sub-fields-maping');
        repeater_data_index = gloo_start_repeater_item.attr('data-index');
        data_field_id = gloo_repeater_item.attr('data-field-id');
		repeater_sub_prefix = gloo_start_repeater_item.attr('data-repeater-sub-prefix');
		if(!repeater_sub_prefix)
			repeater_sub_prefix = '';

        if(repeater_mapping_data){
            repeater_mapping_data_object = JSON.parse(repeater_mapping_data);
            if(typeof repeater_mapping_data_object == 'object'){
                var inner_loop = 0;
                for (const key in repeater_mapping_data_object) {
                    if(typeof repeater_mapping_data_object[key]['gloo_frontend_post_editing'+repeater_sub_prefix+'_source_sub_field'] != 'undefined'){
                        data_source_field_id = repeater_mapping_data_object[key]['gloo_frontend_post_editing'+repeater_sub_prefix+'_source_sub_field'];
                        if(data_field_id == data_source_field_id)
                            gloo_repeater_item.attr('data-source-field-id', data_source_field_id);
                        inner_loop++;
                    }
                    
                }
            }
        }

    }
    

    if($(".gloo_repeater_field").length >= 1 && $(".gloo_repeater_start_field").length >= 1){

        if($(".gloo_repeater_end_field").length >= 1){
            $('body').on('click', '.gloo_repeater_end_field button', function(e){
                
                previous_ul = $(this).parent(".gloo_repeater_end_field").prev('ul');
                html_index = parseInt(previous_ul.attr('data-index'));
                previous_ul.append(gloo_repeater_html[html_index]);
                //$('.gloo_repeater_field_wrapper').append(gloo_repeater_html);
                e.preventDefault();
                e.stopPropagation();
                fix_field_names_ids();
    
                if(jQuery('textarea[type="gloo_wysiwyg"]').length >= 1){
                    create_tiny_mce('textarea[type="gloo_wysiwyg"]');
                }
                
            });
        }


        $(".gloo_repeater_start_field").each(function(index, item){
            
            gloo_start_repeater_item = $(this);
            gloo_start_repeater_item.attr('data-index', index);
            

            gloo_repeater_html[index] = '<li class="gloo_repeater_li_item"> <a href="#" class="remove">×</a>';
            if($(".gloo_repeater_field_"+index).length >= 1){
                gloo_repeater_html[index] += '<input type="hidden" name='+gloo_start_repeater_item.attr('data-field-id')+'[] value="gloo_repeater">';
                $(".gloo_repeater_field_"+index).each(function(){
                    
                    add_repeater_mapping_data(gloo_start_repeater_item, $(this));

                    if($(this).attr('data-field-input')){
                        $(this).find($(this).attr('data-field-input')).removeAttr('id');
                        field_name = $(this).find($(this).attr('data-field-input')).attr('name')+'[]';
                        field_type = $(this).attr('data-field-type');
                        if(!(field_type == 'checkbox' || field_type == 'radio')){
                            $(this).find($(this).attr('data-field-input')).attr('name', field_name);
                        }                
                    }
                    gloo_repeater_html[index] += $(this).outerHTML();
                    // gloo_repeater_html.push($(this).clone());
                    /*if(field_type == 'gloo_wysiwyg'){
                        $(this).remove();
                    }
                    else*/
                        $(this).remove();
                });
                gloo_repeater_html[index] += '</li>';
            }
            $(this).next(".gloo_repeater_end_field").attr('data-index', index);
            $(this).after('<ul class="gloo_repeater_field_wrapper" data-index="'+index+'"></ul>');
            // $('.gloo_repeater_field_wrapper').after('<ul class="gloo_repeater_field_wrapper" data-index="'+index+'"></ul>');
            $('.gloo_repeater_field_wrapper').css('width', '100%');

            display_gloo_repeater_existing_values(gloo_start_repeater_item);


            $("body").on("click", ".gloo_repeater_li_item a.remove", function(e){


                

                parent_ui = $(this).closest(".gloo_repeater_field_wrapper");
                $(this).parent('li').remove();
                // if(parent_ui.find("li").length >= 1)
                //     parent_ui.find("li input").trigger('change').trigger('keyup');

                const myEvent = new CustomEvent("gloo_repeater_field_item_deleted", {
                    detail: {parent_ui: parent_ui},
                    bubbles: true,
                    cancelable: true,
                    composed: false,
                  });
                document.querySelector("body").dispatchEvent(myEvent);


                e.preventDefault();
                e.stopPropagation();
            });
        });

        
        
        
    }
    
    
    

    // gloo_repeater_html += '</ul>';
    // console.log(gloo_repeater_html);
});