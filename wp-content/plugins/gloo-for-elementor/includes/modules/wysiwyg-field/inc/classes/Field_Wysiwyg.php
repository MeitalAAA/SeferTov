<?php

namespace Gloo\WysiwygField;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
 
class Field_Wysiwyg extends \ElementorPro\Modules\Forms\Fields\Field_Base {
 
    public $depended_scripts = [
        'tinymce-cdn',
    ];
 
    public function get_type() {
        return 'gloo_wysiwyg';
    }
 
    public function get_name() {
        return __( 'Wysiwyg', 'otw-unmanned-directory-td' );
    }
 

    /**
	 * @param Widget_Base $widget
	 */
	public function update_controls( $widget ) {

		$elementor = \Elementor\Plugin::instance();
        $control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

        if ( is_wp_error( $control_data ) ) {
			return;
		}

        
        
        $field_controls = array();

        $advanced_toggles = array(
            'menubar' => 'Menubar',
            'paste_as_text' => 'Paste as text',
            'directionality' => 'Right to Left',
        );

        foreach($advanced_toggles as $key=>$value){
            $field_name = $this->get_type().'_'.$key;
            if($key == 'directionality')
                $field_controls[$field_name] = $this->add_form_field_switcher($field_name, $value, 'advanced', 'no');
            else
                $field_controls[$field_name] = $this->add_form_field_switcher($field_name, $value, 'advanced');
        }

        $tinymce_plugins = $this->tinymce_plugins();
        $field_name = $this->get_type().'_plugins_separator';
        $field_controls[$field_name] = [
            'name' => $field_name,
            // 'raw' => __( '<h3> -- Plugins -- </h3>', 'elementor-pro' ),
            'type' => \Elementor\Controls_Manager::DIVIDER,
            'condition'    => [
                'field_type' => $this->get_type(),
            ],
            'tab'          => 'advanced',
            'inner_tab'    => 'form_fields_advanced_tab',
            // 'tab'          => 'content',
            // 'inner_tab'    => 'form_fields_content_tab',
            'tabs_wrapper' => 'form_fields_tabs',
        ];
        $field_controls [$this->get_type().'_plugins_heading'] = [
            'name' => $this->get_type().'_plugins_heading',
            'raw' => __( '<h2>Plugins</h2>', 'elementor-pro' ),
            'type' => \Elementor\Controls_Manager::RAW_HTML,
            'condition'    => [
                'field_type' => $this->get_type(),
            ],
            'tab'          => 'advanced',
            'inner_tab'    => 'form_fields_advanced_tab',
            // 'tab'          => 'content',
            // 'inner_tab'    => 'form_fields_content_tab',
            'tabs_wrapper' => 'form_fields_tabs',
        ];
        foreach($tinymce_plugins as $toolbar_key=>$toolbar_value){
            foreach($toolbar_value as $key=>$value){
                $field_name = $this->get_type().'_plugins_'.$key;
                $field_controls[$field_name] = $this->add_form_field_switcher($field_name, $value, 'advanced');
            }
        }



        $tmp = new \Elementor\Repeater();
        $tmp->add_control(
			$this->get_type().'_editor_height',
			[
                'name' => $this->get_type().'_editor_height',
                'label' => __( 'Height', 'gloo_for_elementor' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'condition'    => [
                    'field_type' => $this->get_type(),
                ],
                'range' 		=> [
                    'px' 		=> [
                        'min' 	=> 0,
                        'max' 	=> 1000,
                        'step' 	=> 1,
                    ],
                    // '%' => [
                    //     'min' 	=> 0,
                    //     'max' 	=> 100,
                    // ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => '200',
                ],
                'size_units' 	=> [ 'px'/*, '%','em','rem','vh' */],
            ]
		);
        $control_data['fields'] = $this->inject_controls($tmp->get_controls(), $control_data['fields']);

        $tinymce_toolbar = $this->tinymce_toolbar();
        $dividers = array('hr', 'sticky_divider', 'separator_panel_style', 'sitemap_layout_divider', 'separator_tabs_style');
        
        foreach($tinymce_toolbar as $toolbar_key=>$toolbar_value){
            if(isset($dividers[$toolbar_key]))
                $field_controls[$dividers[$toolbar_key]] = $this->add_form_field_divider('content', $dividers[$toolbar_key]);

            foreach($toolbar_value as $key=>$value){
                $field_name = $this->get_type().'_toolbar_'.$key;
                $field_controls[$field_name] = $this->add_form_field_switcher($field_name, $value, 'content');
            }
        }


        $tinymce_toolbar = array(
            'paste' => 'Paste',
            'autolink' => 'Auto Link',
            'colorpicker' => 'Color Picker',
            'contextmenu' => 'Context Menu',
            'help' => 'Help',  
        );

        $control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}

    /**
     * @param      $item
     * @param      $item_index
     * @param Form $form
     */
    public function render( $item, $item_index, $form ) {
 
        // $settings = $form->get_settings_for_display( 'form_fields' );
        // $depth       = $settings[ $item_index ]['gloo_test_dropdown'];

        $form->add_render_attribute( 'textarea' . $item_index, 'class', 'elementor-tinymce' );
        
        $menubar = 'false';
        if(isset($item[$this->get_type().'_menubar']) && $item[$this->get_type().'_menubar'] == 'yes')
            $menubar = 'true';
        $form->add_render_attribute( 'textarea' . $item_index, 'data-menubar', $menubar );

        $paste_as_text = 'false';
        if(isset($item[$this->get_type().'_paste_as_text']) && $item[$this->get_type().'_paste_as_text'] == 'yes')
            $paste_as_text = 'true';
        $form->add_render_attribute( 'textarea' . $item_index, 'data-paste_as_text', $paste_as_text );


        $editor_height = '200';
        if(isset($item[$this->get_type().'_editor_height']) && isset($item[$this->get_type().'_editor_height']['size']) && $item[$this->get_type().'_editor_height']['size'])
            $editor_height = $item[$this->get_type().'_editor_height']['size'];
        $form->add_render_attribute( 'textarea' . $item_index, 'data-editor-height', $editor_height );
 		
        $directionality = 'ltr';
        if(isset($item[$this->get_type().'_directionality']) && $item[$this->get_type().'_directionality'] == 'yes')
            $directionality = 'rtl';
        $form->add_render_attribute( 'textarea' . $item_index, 'data-directionality', $directionality );

        $toolbar = '';
        $tinymce_toolbar = $this->tinymce_toolbar();
        foreach($tinymce_toolbar as $toolbar_key=>$toolbar_value){
            foreach($toolbar_value as $key=>$value){
                if(isset($item[$this->get_type().'_toolbar_'.$key]) && $item[$this->get_type().'_toolbar_'.$key] == 'yes')
                $toolbar .= $key.' ';
            }
            $toolbar .= " | ";
        }
        $form->add_render_attribute( 'textarea' . $item_index, 'data-toolbar', $toolbar );


        $plugins = 'paste charmap hr link media nonbreaking wordcount directionality ';
        $tinymce_plugins = $this->tinymce_plugins();
        foreach($tinymce_plugins as $plugins_key=>$plugins_value){
            foreach($plugins_value as $key=>$value){
                if(isset($item[$this->get_type().'_plugins_'.$key]) && $item[$this->get_type().'_plugins_'.$key] == 'yes'){
                    $plugins .= $key.' ';
                }
            }
        }
        $form->add_render_attribute( 'textarea' . $item_index, 'data-toolbar', $toolbar );
        
        $html_output = '<textarea 
        ' . $form->get_render_attribute_string( 'input' . $item_index ) . ' 
        data-menubar="'.$menubar.'" 
        data-paste_as_text="'.$paste_as_text.'" 
        data-directionality="'.$directionality.'" 
        data-editor-height="'.$editor_height.'" 
        data-toolbar="'.$toolbar.'" 
        data-plugins="'.$plugins.'" 
        style="height: 1px; padding:0px;background:none; border:none; background-color:transparent;
        ">';
        //display: block!important;
        $html_output = apply_filters( 'gloo_tinymce_html_output', $html_output, get_the_ID(), $item);
        if($html_output){
            echo $html_output;
            if(isset($item['field_value'])){ 
                echo $item['field_value']; 
            }
            echo '</textarea> <style>textarea[type="gloo_wysiwyg"]{display:block!important;}</style>';
        }
        
		
    }
 
    public function register_field_type( $fields ) {
        \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( self::get_type(), $this );
        $fields[ self::get_type() ] = self::get_name();
        return $fields;
    }
 
    public function front_end_inline_JS() {
        ?>
        <script>
            function convert_string_into_bolean(string_text){
                if(string_text == 'true')
                    return true;
                else
                    return false;
            }
			function create_tiny_mce(selector_item){
                tinymce.init({
                    selector: selector_item,
                    // selector: 'textarea[type="gloo_wysiwyg"]',
                    setup: function (editor) {
                        
                        editor.on('change', function () {
                            editor.save();
                        });
                    },
                    menubar: convert_string_into_bolean(jQuery(selector_item).attr('data-menubar')),
                    paste_as_text: convert_string_into_bolean(jQuery(selector_item).attr('data-paste_as_text')),
                    directionality : jQuery(selector_item).attr('data-directionality'),
                    // plugins: 'preview searchreplace autolink directionality visualblocks visualchars image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help',
                    plugins: jQuery(selector_item).attr('data-plugins'),
                    toolbar: jQuery(selector_item).attr('data-toolbar'),
                    fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 22px 24px 30px 36px 50px 64px 72px",
                    height : jQuery(selector_item).attr('data-editor-height'),
                    //'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent | removeformat | image table media'
                    init_instance_callback : function(editor) {
                        console.log('Editor: ' + editor.id + ' is now initialized.');
                    }
                });
            }
            var ElementorFormWysiwyg = ElementorFormWysiwyg || {};
            
            jQuery(window).on('load', function () {
				
				if(jQuery('textarea[type="gloo_wysiwyg"]').length >= 1){
                    
			   ElementorFormWysiwyg = {
                    onReady: function( callback ) {
                        if ( window.tinymce ) {
                            callback();
                        } else {
                            // If not ready check again by timeout..
                            setTimeout( function() {
                                ElementorFormWysiwyg.onReady( callback );
                            }, 350 );
                        }
                    },
                    init: function() {
                        /*self = this;*/
                        this.onReady( function() {
                            jQuery('textarea[type="gloo_wysiwyg"]').each(function(index, item){
                                create_tiny_mce('textarea[type="gloo_wysiwyg"]');
                            });
                        });
                    }
                };
                ElementorFormWysiwyg.init();
			   }
				
                
				
            } );
        </script>
        <?php
    }
 
    public function editor_inline_JS() {
        add_action( 'wp_footer', function() {
        ?>
        <script>
			function convert_string_into_bolean(string_text){
                if(string_text == 'true')
                    return true;
                else
                    return false;
            }
            var ElementorFormWysiwygField = ElementorFormWysiwygField || {};
            jQuery( document ).ready( function( $ ) {
				
                ElementorFormWysiwygField = {
                    onReady: function( callback ) {
                        if ( window.tinymce ) {
                            callback();
                        } else {
                            // If not ready check again by timeout..
                            setTimeout( function() {
                                ElementorFormWysiwygField.onReady( callback );
                            }, 350 );
                        }
                    },
                    renderField: function( inputField, item, i, settings ) {
                        var itemClasses = item.css_classes,
                            required = '',
                            fieldName = 'form_field_';
 
                        if ( item.required ) {
                            required = 'required';
                        }
                        return '<textarea type="gloo_wysiwyg" class="elementor-wysiwyg elementor-field ' + itemClasses + '" name="' + fieldName + '" id="form_field_' + i + '" ' + required + '></textarea>';
                    },
                    initTinyMce: function() {
                        tinymce.remove();
                        tinymce.init({
                            selector: 'textarea[type="gloo_wysiwyg"]',
                            setup: function (editor) {
                                editor.on('change', function () {
                                    editor.save();
                                });
                            },
                            menubar: convert_string_into_bolean(jQuery(selector_item).attr('data-menubar')),
                            paste_as_text: convert_string_into_bolean(jQuery(selector_item).attr('data-paste_as_text')),
                            directionality : jQuery(selector_item).attr('data-directionality'),
                            // plugins: 'preview searchreplace autolink directionality visualblocks visualchars image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help',
                            plugins: jQuery(selector_item).attr('data-plugins'),
                            toolbar: jQuery(selector_item).attr('data-toolbar'),
                            fontsize_formats: "8px 10px 12px 14px 16px 18px 20px 22px 24px 30px 36px 50px 64px 72px",
                        });
                    },
                    init: function() {
                        //self = this;
                        this.onReady( function() {
                            elementorFrontend.hooks.addAction( 'frontend/element_ready/form.default', ElementorFormWysiwygField.initTinyMce );
                            elementor.hooks.addFilter( 'elementor_pro/forms/content_template/field/wysiwyg', ElementorFormWysiwygField.renderField, 10, 4 );
                        } );
                    }
                };
				
                ElementorFormWysiwygField.init();
            } );
        </script>
        <?php
        } );
    }
 
    public function sanitize_field( $value, $field ) {
        return wp_kses_post( $field['raw_value'] );
    }
 
    public function __construct() {
        parent::__construct();
        add_filter( 'elementor_pro/forms/field_types', [ $this, 'register_field_type' ] );
        add_action( 'wp_footer', [ $this, 'front_end_inline_JS' ] );
        // add_action( 'elementor/preview/init', [ $this, 'editor_inline_JS' ] );
        //tinymce 4.8.5
        // $tincymce = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.8.5/tinymce.min.js';
        $tincymce = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.10.7/tinymce.min.js';

        //https://www.tiny.cloud/docs/tinymce/6/migration-from-5x/
        wp_register_script( 'tinymce-cdn', $tincymce );
    }

    public function add_form_field_switcher($name, $label, $context = 'content', $default = 'yes'){
        $output = [
            'name'         => $name,
            'label'        => $label,
            'type'         => \Elementor\Controls_Manager::SWITCHER,
            'label_on' => __( 'Yes', 'gloo' ),
            'label_off' => __( 'No', 'gloo' ),
            'return_value' => 'yes',
            'default' => $default,
            'condition'    => [
                'field_type' => $this->get_type(),
            ],
            'tab'          => $context,
            'inner_tab'    => 'form_fields_'.$context.'_tab',
            'tabs_wrapper' => 'form_fields_tabs',
        ];
        
        return $output;
    }

    public function add_form_field_divider($context = 'content', $name = ''){
        $output = [
            // 'name'         => $this->get_type().'_separator_'.$random_value,
            'type'         => \Elementor\Controls_Manager::DIVIDER,
            'condition'    => [
                'field_type' => $this->get_type(),
            ],
            'tab'          => $context,
            'inner_tab'    => 'form_fields_'.$context.'_tab',
            // 'tab'          => 'content',
            // 'inner_tab'    => 'form_fields_content_tab',
            'tabs_wrapper' => 'form_fields_tabs',
        ];
        if($name)
            $output['name'] = $name;
        
        return $output;
    }

    public function tinymce_toolbar(){
        return array(
            array(
                'bold' => 'Bold',
                'italic' => 'Italic',
                'strikethrough' => 'Strike Through',
                'forecolor' => 'Forground Color',
                'backcolor' => 'Background Color',
                'fontsizeselect' => 'Font Size',           
            ),            
            array(
                'alignleft' => 'Align Left',
                'aligncenter' => 'Align Center',
                'alignright' => 'Align Right',
                'alignjustify' => 'Align Justify',
            ),
            array(
                'numlist' => 'Numlist',
                'bullist' => 'Bullet List',
                'outdent' => 'Decrease Indent',
                'indent' => 'Increase Indent',
            ),
            array(
                'formatselect' => 'Format Select',
                'removeformat' => 'Remove Format',
            ),
            array(
                'image' => 'Image',
                'table' => 'Table',
                'media' => 'Media',
            ),
            array(
                'link' => 'Link',
            ),
        );
    }

    public function tinymce_plugins(){
        //// plugins: 'preview searchreplace autolink directionality visualblocks visualchars image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists textcolor wordcount imagetools contextmenu colorpicker textpattern help',
        return array(
            array(
                'help' => 'Help',
                'imagetools' => 'Image Tools',
                'preview' => 'Preview',
                'table' => 'Table',
                'contextmenu' => 'Context Menu',
                'colorpicker' => 'Color Picker',
                'searchreplace' => 'Search and Replace',
                'lists' => 'Lists',
                'image' => 'Image',
                'textcolor' => 'Text Color',
                'autolink' => 'Auto Link',
            ),
        );
    }

    public function inject_controls($repeater_controls, $existing_controls = array()){
		
		if($repeater_controls && is_array($repeater_controls) && count($repeater_controls) >= 1){
			foreach($repeater_controls as $key=>$control){
				$existing_controls[$key] = $control;
			}
		}
		return $existing_controls;
	}
}
