var gloo_form_action_pro_count = 0;
jQuery(window).on('load', function () {
  if (elementor) {
    
    const form_actions_pro_getEditorControlView = function (id) {
      const editor = elementor.getPanelView().getCurrentPageView();
      
      if(typeof editor.getControlModel(id) != 'undefined' && typeof editor.getControlModel(id).cid != 'undefined')
          return editor.children.findByModelCid(editor.getControlModel(id).cid);
    };

     /* Form actions Pro JS */

     const update_default_form_submit_actions = function(ele, selected_values = [], index_ele = false){
        jQuery(".elementor-control-section_integration").show();
        jQuery(".elementor-control-section_integration").trigger('click');
        jQuery(".elementor-control-submit_actions select").val('');
        jQuery.each(selected_values, function(i,e){
          jQuery(".elementor-control-submit_actions select option[value='" + e + "']").prop("selected", true);
        });
  
        jQuery(".elementor-control-submit_actions select")/*.val('email')*/.trigger('change');
        jQuery(".elementor-control-gloo_section_pro_form_actions").trigger('click');
        
        if(index_ele){
          jQuery(".elementor-control-form_actions_pro_form_actions_list .elementor-repeater-fields").each(function(i,v){
            if(i == index_ele){
              jQuery(this).find(".elementor-repeater-row-item-title").trigger("click");
            }
          });
        }
        jQuery(".elementor-control-section_integration").hide();
      }
  
      jQuery('body').on('change', '.elementor-control-form_actions_pro_form_action select', function(e){
        selected_values = [];
        // index_ele = jQuery(e.target).closest('.elementor-repeater-fields').find(".elementor-repeater-row-item-title");
        jQuery(".elementor-control-form_actions_pro_form_action select").each(function(){
          selected_values.push(jQuery(this).val());
        });
        var currentItem = jQuery(e.target).closest('.elementor-repeater-row-controls');      
        index_ele = jQuery(currentItem).parent().index();
        update_default_form_submit_actions(jQuery(e.target), selected_values, index_ele);
        // console.log(jQuery(".elementor-control-submit_actions").length);
      });

      jQuery(document).on('DOMNodeInserted', 'body', function (e) {
        
        if(jQuery(e.target).hasClass('elementor-repeater-row-item-title') && jQuery(".elementor-control-form_actions_pro_form_action select").length >= 1){
          gloo_form_action_pro_count++;
          if(jQuery(".elementor-control-form_actions_pro_form_action select").length === gloo_form_action_pro_count){
            
            selected_values = [];
            jQuery(".elementor-control-form_actions_pro_form_action select").each(function(){
              if(jQuery(this).val() && jQuery.inArray( jQuery(this).val(), selected_values) == -1)
                selected_values.push(jQuery(this).val());
            });
            if(jQuery(".elementor-control-section_integration").length >= 1){
              jQuery(".elementor-control-section_integration").show();
              jQuery(".elementor-control-section_integration").trigger('click');
              old_values = jQuery(".elementor-control-submit_actions select").val();
                jQuery(".elementor-control-submit_actions select").val('');
                jQuery.each(selected_values, function(i,e){
                  jQuery(".elementor-control-submit_actions select option[value='" + e + "']").prop("selected", true);
                });
                jQuery(".elementor-control-submit_actions select")/*.val('email')*/.trigger('change');
              // }
              jQuery(".elementor-control-gloo_section_pro_form_actions").trigger('click');
              jQuery(".elementor-control-form_actions_pro_form_actions_list .elementor-repeater-fields").each(function(i,v){
                if(i == gloo_form_action_pro_count-1){
                  jQuery(this).find(".elementor-repeater-row-item-title").trigger("click");
                }
              });
              jQuery(".elementor-control-section_integration").hide();
            }
            gloo_form_action_pro_count = 0;
          }
        };
      });
      
      jQuery(document).on('DOMNodeInserted', 'body', function (e) {
        if(jQuery(".elementor-control-section_integration").length >= 1){
          jQuery(".elementor-control-section_integration").hide();
        }
        // if(jQuery(e.target).hasClass('elementor-control-section_integration')){
        //   jQuery(e.target).hide();
        //   console.log(jQuery(e.target).attr('class'));
        //   jQuery(e.target).css('height', '1px!important;');
        // }
      });





      const update_fluid_dynamic_conditions_chain = function(ele){
        var conditions = elementor.settings.page.getSettings().settings.gloo_form_actions_pro_conditions;
        if (typeof conditions === "undefined" || conditions.length < 1) {
          return;
        }
  
  
        var currentItem = ele.closest('.elementor-repeater-row-controls');
        jQuery.each(conditions.models, function (index, value) {
          if (jQuery(currentItem).find('select[data-setting="form_actions_pro_condition_chain"] option[value="' + value.attributes._id + '"]').length < 1) {
              var label = value.attributes['gloo_form_actions_pro_condition_name'] ? value.attributes['gloo_form_actions_pro_condition_name'] : 'Item #' + (index + 1)
              jQuery(currentItem).find('select[data-setting="form_actions_pro_condition_chain"]').append(new Option(label, value.attributes._id));
          }
        });
  
  
        selectedItem = false;
        formFieldsRepeaterControlView = form_actions_pro_getEditorControlView('form_actions_pro_form_actions_list');
        currentItemIndex = jQuery(currentItem).parent().index();
        if (formFieldsRepeaterControlView.collection.length) {
          var selectedItem = formFieldsRepeaterControlView.collection.models[currentItemIndex].attributes.form_actions_pro_condition_chain;
        }
        if (selectedItem && jQuery(currentItem).find('select[data-setting="form_actions_pro_condition_chain"] option[value="' + selectedItem + '"]').length) {
          jQuery(currentItem).find('select[data-setting="form_actions_pro_condition_chain"]').val(selectedItem);
          //.change();
        }
  
      }

      elementor.hooks.addAction("panel/open_editor/widget/form", function(){

        jQuery(document).on('DOMNodeInserted', 'body', function (e) {
          if(jQuery(e.target).hasClass('elementor-control-form_actions_pro_form_actions_list')){
            jQuery('.elementor-control-form_actions_pro_form_actions_list .elementor-repeater-row-item-title').on('click', function () {
              var isClosing = jQuery(this).parent().next().hasClass('editable');
              if (isClosing) {
                  return;
              }
              // update_fluid_dynamic_conditions_chain(jQuery(this).parent().next().find(".elementor-control-form_actions_pro_enable_conditions input"));
            });
          }
        });
      });

      jQuery('body').on('change', '.elementor-control-form_actions_pro_enable_conditions input', function(){
        // update_fluid_dynamic_conditions_chain(jQuery(this));
      });
  
  
    



  }
});