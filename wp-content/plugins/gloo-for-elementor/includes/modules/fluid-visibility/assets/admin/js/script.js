jQuery(window).on('load', function () {
  if (elementor) {
    
    const fluid_visibility_getEditorControlView = function (id) {
      const editor = elementor.getPanelView().getCurrentPageView();
      
      if(typeof editor.getControlModel(id) != 'undefined' && typeof editor.getControlModel(id).cid != 'undefined')
          return editor.children.findByModelCid(editor.getControlModel(id).cid);
    };

    /* new fluid visibility js start here */
    jQuery('body').on('click', '.elementor-control-gloo_fluid_visibility_ffc_tab', function (e) {
      var currentItem = jQuery(this).closest('.elementor-repeater-row-controls');
      // console.log("currentItem");
      // console.log(currentItem);
      // console.log("currentItem index");
      // console.log(jQuery(currentItem).parent().index());
      // var currentSelect = jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"]');
      // console.log("currentSelect");
      // console.log(currentSelect);
      var conditions = elementor.settings.page.getSettings().settings.gloo_fluid_visibility_conditions;
      if (typeof conditions === "undefined" || conditions.length < 1) {
          return;
      }
      // console.log("conditions");
      // console.log(conditions);
      // console.log("next select");
      // console.log(jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"]'));
      var formFieldsRepeaterControlView = fluid_visibility_getEditorControlView('form_fields'),
          currentItemIndex = jQuery(currentItem).parent().index();
      // console.log("currentItemIndex");
      // console.log(currentItemIndex);
      // console.log("formFieldsRepeaterControlView");
      // console.log(formFieldsRepeaterControlView);
      selectedItem = false;
      if (formFieldsRepeaterControlView.collection.length) {
          // console.log("formFieldsRepeaterControlView.collection.models.currentItemIndex");
          // console.log(formFieldsRepeaterControlView.collection.models[currentItemIndex]);
          var selectedItem = formFieldsRepeaterControlView.collection.models[currentItemIndex].attributes.gloo_fluid_visibility_ffc_chain;
          // console.log("formFieldsRepeaterControlView.collection.models");
          // console.log(formFieldsRepeaterControlView.collection.models);
          // jQuery.each(formFieldsRepeaterControlView.collection.models, function (index, value) {
          //     if (jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"] option[value="' + value.attributes._id + '"]').length < 1) {
          //         var label = value.attributes['gloo_fluid_visibility_condition_name'] ? value.attributes['gloo_fluid_visibility_condition_name'] : 'Item #' + (index + 1)
          //         jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"]').append(new Option(label, value.attributes._id));
          //     }
          // });
      }
      jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"] option').remove();
      jQuery.each(conditions.models, function (index, value) {
          if (jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"] option[value="' + value.attributes._id + '"]').length < 1) {
              var label = value.attributes['gloo_fluid_visibility_condition_name'] ? value.attributes['gloo_fluid_visibility_condition_name'] : 'Item #' + (index + 1)
              jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"]').append(new Option(label, value.attributes._id));
          }
      });
      // console.log("selectedItem down");
      // console.log(selectedItem);
      // console.log(jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"] option[value="' + selectedItem + '"]'));
      if (selectedItem && jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"] option[value="' + selectedItem + '"]').length) {
          // console.log('in there');
          // console.log(jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"]'));
          jQuery(currentItem).find('select[data-setting="gloo_fluid_visibility_ffc_chain"]').val(selectedItem).change();
      }
    });
    function get_existing_conditions_for_elements(target_control = 'gloo_fluid_visibility_elements_condition'){
      var selectEl = jQuery('select[data-setting="'+target_control+'"]');
      var conditions = elementor.settings.page.getSettings().settings.gloo_fluid_visibility_conditions;
      if (typeof conditions === "undefined" || conditions.length < 1) {
          return;
      }
      jQuery('select[data-setting="'+target_control+'"] option').remove();
      jQuery.each(conditions.models, function (index, value) {
          if (jQuery('select[data-setting="'+target_control+'"] option[value="' + value.attributes._id + '"]').length < 1) {
              var label = value.attributes['gloo_fluid_visibility_condition_name'] ? value.attributes['gloo_fluid_visibility_condition_name'] : 'Item #' + (index + 1)
              selectEl.append(new Option(label, value.attributes._id));
          }
      });
      var repeaterControlView = fluid_visibility_getEditorControlView(target_control);
      selectedItem = repeaterControlView.getControlValue();
      if(!(typeof selectedItem != 'undefined' && selectedItem))
          selectedItem = false;
      
      if (selectedItem && jQuery('select[data-setting="'+target_control+'"] option[value="' + selectedItem + '"]').length) {
          jQuery('select[data-setting="'+target_control+'"]').val(selectedItem).change();
      }
    }

    jQuery(document).on('DOMNodeInserted', 'body', function (e) {
      if(jQuery(e.target).hasClass('elementor-repeater-row-item-title')){
          get_existing_conditions_for_next_condition_chain(jQuery(e.target));
      };
      
      // if(jQuery(e.target).hasClass('elementor-control-gloo_fluid_visibility_condition_next')){
          // get_existing_conditions_for_next_condition_chain(e);
      // }
      if(jQuery(e.target).hasClass('elementor-control-gloo_fluid_visibility_elements_condition')){
          get_existing_conditions_for_elements('gloo_fluid_visibility_elements_condition');
      }
      if(jQuery(e.target).hasClass('elementor-control-gloo_fluid_visibility_button_condition')){
        get_existing_conditions_for_elements('gloo_fluid_visibility_button_condition');
      }
      
      
    });

    // jQuery(document).on('click', '.elementor-tab-control-advanced', function () {
    //     jQuery('.elementor-control-gloo_fluid_visibility_elements').click(function(){
    //         get_existing_conditions_for_elements();
    //     });
    // });

    // elementor.hooks.addAction( 'panel/open_editor/widget', function( panel, model, view ) {
    //     jQuery('.elementor-control-gloo_fluid_visibility_elements').click(function(){
    //         get_existing_conditions_for_elements();
    //     });
    // } );
    function get_existing_conditions_for_next_condition_chain(dom_element){
        var selectEl = dom_element.parent().next().find('select[data-setting="gloo_fluid_visibility_condition_next"]');
        if(selectEl.length >= 1){
            var repeaterControlView = fluid_visibility_getEditorControlView('gloo_fluid_visibility_conditions');
            if (repeaterControlView.collection.length) {

                jQuery.each(repeaterControlView.collection.models, function (index, value) {
                    
                    if (jQuery(selectEl).find('option[value="' + value.attributes._id + '"]').length < 1) {
                        var label = value.attributes['gloo_fluid_visibility_condition_name'] ? value.attributes['gloo_fluid_visibility_condition_name'] : 'Item #' + (index + 1)
                        jQuery(selectEl).append(new Option(label, value.attributes._id));
                    }
                });
                jQuery(selectEl).find('option').each(function(){
                    option_exist = false;
                    current_option_value = jQuery(this).attr('value');
                    jQuery.each(repeaterControlView.collection.models, function (index, value) {
                        if(value.attributes._id == current_option_value)
                            option_exist = true;
                            
                    });
                    if(option_exist == false || dom_element.parent().next().find('input[data-setting="_id"]').val() == current_option_value)
                        jQuery(this).remove();
                });
            }
        }
          
    }
    jQuery(document).one('click', '#elementor-panel-page-settings .elementor-tab-control-advanced', function () {
      jQuery('.elementor-control-gloo_fluid_visibility_conditions .elementor-repeater-row-item-title').on('click', function () {
          var isClosing = jQuery(this).parent().next().hasClass('editable');
          if (isClosing) {
              return;
          }
          get_existing_conditions_for_next_condition_chain(jQuery(this));
          
      });
    });

    /* fluid visibility js end here */
    



  }
});