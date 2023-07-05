const getEditorControlView = function (id) {
    const editor = elementor.getPanelView().getCurrentPageView();
    
    if(typeof editor.getControlModel(id) != 'undefined' && typeof editor.getControlModel(id).cid != 'undefined')
        return editor.children.findByModelCid(editor.getControlModel(id).cid);
};

jQuery(window).on('load', function () {
    if (elementor) {

        // add dark or light theme class to the body tag
        addThemeClass();

        // helper functions
        const addSelectOption = function (id, value, label) {
            jQuery('select[data-setting="' + id + '"]').append(new Option(label, value));
        }

        const updateSelectOption = function (value, label) {
            jQuery('select option[value="' + value + '"]').text(label).closest('select').select2();
            // console.log('updated ' + value);
        }

        const removeSelectOption = function (value) {
            jQuery('select option[value="' + value + '"]').remove();
            // console.log('removed ' + value);
        }

        const correctNamesAfterSort = function (titleName, collection) {
            if (collection.length) {
                jQuery.each(collection.models, function (index, value) {
                    var label = value.attributes[titleName] ? value.attributes[titleName] : 'Item #' + (index + 1)
                    updateSelectOption(value.attributes._id, label);
                });
            }
        }

        const addMissingSelectOptions = function (titleName, selectName, collection) {
            if (collection.length) {
                jQuery.each(collection.models, function (index, value) {
                    if (jQuery('select[data-setting="' + selectName + '"] option[value="' + value.attributes._id + '"]').length < 1) {
                        var label = value.attributes[titleName] ? value.attributes[titleName] : 'Item #' + (index + 1)
                        jQuery('select[data-setting="' + selectName + '"]').append(new Option(label, value.attributes._id));
                    }
                });
            }
        }


        


        var disableAuto = jQuery('.elementor-control-gloo_interactor_disable_auto_update input').is(':checked');

        const linkRepeaterToSelect = function (repeaterName, selectName, titleName) {
            var repeaterControlView = getEditorControlView(repeaterName);
            if(typeof repeaterControlView !='undefined'){
                // console.log(repeaterControlView);
                repeaterControlView.collection
                    .on('change', function repeaterChanged(model, repeaterName) {
                        if (disableAuto) {
                            return;
                        }
    
                        if (repeaterName === 'gloo_interactor_events' && typeof (model.changed.gloo_interactor_event_next_status) !== 'undefined' && model.changed.gloo_interactor_event_next_status === 'yes') {
                            //events
                            if (model.collection.length) {
                                jQuery.each(model.collection.models, function (index, value) {
                                    var parentElement = 'input[value="' + model.attributes._id + '"]';
                                    if (jQuery(parentElement).closest('.elementor-repeater-fields').find('select[data-setting="' + 'gloo_interactor_event_next' + '"] option[value="' + value.attributes._id + '"]').length < 1) {
                                        var label = value.attributes[titleName] ? value.attributes[titleName] : 'Item #' + (index + 1)
                                        jQuery(parentElement).closest('.elementor-repeater-fields').find('select[data-setting="' + 'gloo_interactor_event_next' + '"]').append(new Option(label, value.attributes._id));
                                    }
                                });
                            }
    
                        }
    
                        var optionLabel = model.attributes[titleName],
                            optionValue = model.attributes._id;
    
                        if (!optionLabel) {
                            optionLabel = 'Item #' + model.collection.length;
                        }
    
                        updateSelectOption(optionValue, optionLabel);
                    })
                    .on('update', (collection, update) => {
                        if (disableAuto) {
                            return;
                        }
                        // prevent adding if its just sorting
                        if (update.add && (collection.length == update.at + 1)) {
                            var optionLabel = 'Item #' + (update.at + 1),
                                optionValue = collection.models[update.at].attributes._id;
    
                            if (collection.models[update.at].attributes[titleName]) {
                                optionLabel = collection.models[update.at].attributes[titleName];
                            }
    
                            // console.log(update);
                            if (optionValue) {
    
                                if (repeaterName === 'gloo_interactor_events') {
                                    var select = jQuery('.elementor-control:not(.elementor-hidden-control) select[data-setting="' + 'gloo_interactor_event_next' + '"]');
                                    if (select.find('option[value="' + optionValue + '"]').length < 1) {
                                    }
                                    jQuery('.elementor-control:not(.elementor-hidden-control) select[data-setting="' + 'gloo_interactor_event_next' + '"]').append(new Option(optionLabel, optionValue));
                                }
    
                                addSelectOption(selectName, optionValue, optionLabel);
                            }
                            return false;
                            // return console.log('true add:', collection, update);
                        }
    
                        addMissingSelectOptions(titleName, selectName, collection);
                        correctNamesAfterSort(titleName, collection);
                        // return console.log('add on sort:', collection, update);
                    })
                    .on('remove', (model, collection) => {
                        if (disableAuto) {
                            return;
                        }
    
                        var idToRemove = model.attributes._id;
                        // prevent removing if its just sorting
                        if (collection.models.findIndex(x => x.attributes._id === idToRemove) > 0) {
                            // console.log("not removed");
                            return false;
                        }
                        // not sorting, safe to remove
                        removeSelectOption(idToRemove);
                        // console.log('removed:', model);
    
                    });
            }
            
            return false;
        };


        // update connections button
        jQuery('body').on('change', '.elementor-control-gloo_interactor_disable_auto_update input', function (e) {
            disableAuto = jQuery(this).is(':checked');
        });

        jQuery(document).one('click', '#elementor-panel-page-settings .elementor-tab-control-advanced', function () {
            linkAllRepeaters();

            jQuery('.elementor-control-gloo_interactor_triggers .elementor-repeater-row-item-title').on('click', function () {
                var isClosing = jQuery(this).parent().next().hasClass('editable');
                if (isClosing) {
                    return;
                }
                var selectEl = jQuery(this).parent().next().find('select[data-setting="gloo_interactor_trigger_connect"]');

                // is opening
                var repeaterControlView = getEditorControlView('gloo_interactor_events');
                if (repeaterControlView.collection.length) {
                    jQuery.each(repeaterControlView.collection.models, function (index, value) {

                        // console.log("selectEl");
                        // console.log(selectEl);

                        if (jQuery(selectEl).find('option[value="' + value.attributes._id + '"]').length < 1) {
                            var label = value.attributes['gloo_interactor_event_title'] ? value.attributes['gloo_interactor_event_title'] : 'Item #' + (index + 1)
                            jQuery(selectEl).append(new Option(label, value.attributes._id));
                        }
                    });
                }

                // console.log(isClosing);
            });
        });


        




        function linkAllRepeaters() {
            // repeater, select, title
            linkRepeaterToSelect('gloo_interactor_triggers', 'gloo_interactor_condition_triggers', 'gloo_interactor_trigger_title');
            // linkRepeaterToSelect('gloo_interactor_events', 'gloo_interactor_trigger_connect', 'gloo_interactor_event_title');
            linkRepeaterToSelect('gloo_interactor_variables', 'gloo_dl_connector_interactor_variables', 'gloo_interactor_variable_name');
            linkRepeaterToSelect('gloo_dl_connector_', 'gloo_dl_connector_triggers', 'gloo_dl_connector_title');
        }

        

        // add dark or light theme class to the body tag
        function addThemeClass() {
            var uiTheme = elementor.settings.editorPreferences.model.get('ui_theme'),
                userPrefersDark = matchMedia('(prefers-color-scheme: dark)').matches,
                uiThemeClass = 'dark' === uiTheme || 'auto' === uiTheme && userPrefersDark ? 'dark' : 'light';

            if (uiTheme) {
                jQuery('body').addClass('gloo-editor-theme-' + uiThemeClass);
            }

            jQuery('body').on('change', '.elementor-control-ui_theme :input', function (e) {
                uiTheme = elementor.settings.editorPreferences.model.get('ui_theme');
                if ('dark' === uiTheme || 'auto' === uiTheme && userPrefersDark) {
                    //dark
                    jQuery('body').addClass('gloo-editor-theme-dark').removeClass('gloo-editor-theme-light')
                } else {
                    // light
                    jQuery('body').addClass('gloo-editor-theme-light').removeClass('gloo-editor-theme-dark')
                }
            });
        }
    }
});


jQuery(window).on('load', function () {
    if (elementor) {

        function gloo_update_event_to_fire(target_control = 'gloo_interactor_trigger_connect', repeater_control = 'gloo_interactor_events', repeater_title = 'gloo_interactor_event_title'){

            var selectEl = jQuery('select[data-setting="'+target_control+'"]');
            if(selectEl.length >= 1){
                var repeaterControlView = getEditorControlView(repeater_control);
                if (typeof repeaterControlView != 'undefined' && repeaterControlView.collection.length) {
                    // console.log('tests');
                    selectEl.each(function(){
                        current_dropdown_list = jQuery(this);
                        current_selected_events = current_dropdown_list.val();
                        current_dropdown_list.find('option').remove();
                        jQuery.each(repeaterControlView.collection.models, function (index, value) {
                            if (current_dropdown_list.find('option[value="' + value.attributes._id + '"]').length < 1) {
                                var label = value.attributes[repeater_title] ? value.attributes[repeater_title] : 'Item #' + (index + 1)
                                current_dropdown_list.append(new Option(label, value.attributes._id));
                            }
                        });
                        if(current_selected_events.length >= 1)
                            current_dropdown_list.val(current_selected_events).change();
                    });
                }
                
                
            }
                
        }

        jQuery(document).on('DOMNodeInserted', 'body', function (e) {
			disableAutoUpdate = jQuery('.elementor-control-gloo_interactor_disable_auto_update input').is(':checked');
			if (disableAutoUpdate) {
                            return;
                        }
            if(jQuery(e.target).hasClass('elementor-repeater-row-item-title')){
                gloo_update_event_to_fire('gloo_interactor_trigger_connect', 'gloo_interactor_events', 'gloo_interactor_event_title');
                gloo_update_event_to_fire('gloo_interactor_gsap_triggers', 'gloo_interactor_gsap_', 'gloo_interactor_gsap_title');
                
            }
          });
    }
});
