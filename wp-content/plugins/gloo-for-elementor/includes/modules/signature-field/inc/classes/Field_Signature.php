<?php

namespace Gloo\Modules\SignatureField;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
 
class Field_Signature extends \ElementorPro\Modules\Forms\Fields\Field_Base {
 
    use \Gloo\Modules\SignatureField\Traits\ElementorButtonControls;

    public $prefix = 'gloo_signature_field';

    public $depended_scripts = [
        'gloo_signature_field',
    ];
 
    public function get_type() {
        return 'gloo_signature_field';
    }
 
    public function get_name() {
        return __( 'Gloo Signature', 'gloo_for_elementor' );
    }
 
    public function __construct()
    {
        add_action('elementor/element/form/section_form_style/after_section_end', [$this, 'add_control_section_to_form'], 10, 2);
        // add_action('elementor/widget/print_template', function ($template, $widget) {
        //     if ('form' === $widget->get_name()) {
        //         $template = \false;
        //     }
        //     return $template;
        // }, 10, 2);
        parent::__construct();
    }

    public function update_controls($widget)
    {
        $elementor = \ElementorPro\Plugin::elementor();
        $control_data = $elementor->controls_manager->get_control_from_stack($widget->get_unique_name(), 'form_fields');
        if (is_wp_error($control_data)) {
            return;
        }
        $field_controls = [
          $this->prefix.'_save_to_file' => [
            'name' => $this->prefix.'_save_to_file', 
            'label' => __('Save as file', 'gloo_for_elementor'), 
            'default' => 'yes', 
            'type' => \Elementor\Controls_Manager::SWITCHER,
            'description' => "Enable this option to use this signature in PDF", 
            'inner_tab' => 'form_fields_content_tab', 
            'tabs_wrapper' => 'form_fields_tabs', 
            'condition' => [
              'field_type' => $this->get_type()
              ]
            ], 
          // $this->prefix.'_jpeg' => [
          //   'name' => $this->prefix.'_jpeg', 
          //   'label' => __('Transmit using JPEG', 'gloo_for_elementor'), 
          //   'description' => __('Use this option if the signature does not appear in the PDF.', 'gloo_for_elementor'), 
          //   'type' => \Elementor\Controls_Manager::SWITCHER, 
          //   'default' => 'no', 
          //   'inner_tab' => 'form_fields_content_tab', 
          //   'tabs_wrapper' => 'form_fields_tabs', 
          //   'condition' => [
          //     'field_type' => $this->get_type()
          //     ]
          //   ]
          ];
        $control_data['fields'] = $this->inject_field_controls($control_data['fields'], $field_controls);
        $widget->update_control('form_fields', $control_data);
    }

    public function render($item, $item_index, $form)
    {
        $settings = $form->get_settings_for_display();
        // We do not use type hidden so the browser will honor required:
        $hidden_css = 'width: 0; height: 0; opacity: 0; position: absolute; pointer-events: none;';
        $form->add_render_attribute('input' . $item_index, 'style', $hidden_css, true);
        $form->add_render_attribute('gloo-signature-canvas' . $item_index, 'class', 'gloo-signature-canvas');
        $form->add_render_attribute('gloo-signature-canvas' . $item_index, 'data-pen-color', $settings['gloo_signature_canvas_pen_color']);
        $form->add_render_attribute('gloo-signature-canvas' . $item_index, 'data-background-color', $settings['gloo_signature_canvas_background_color']);
        $form->add_render_attribute('gloo-signature-canvas' . $item_index, 'data-jpeg', /*$item['signature_jpeg']*/'no');
        $form->add_render_attribute('gloo-signature-canvas' . $item_index, 'style', 'width: 100%; height: --canvas-height; border-style: solid');
        // $form->add_render_attribute('gloo-signature-canvas' . $item_index, 'width', '400');
        // $form->add_render_attribute('gloo-signature-canvas' . $item_index, 'height', '200');
        $form->add_render_attribute('gloo-signature-wrapper' . $item_index, 'class', 'gloo-signature-wrapper');
        $form->add_render_attribute('gloo-signature-wrapper' . $item_index, 'id', 'gloo-signature-wrapper-' . $form->get_attribute_name($item));
        $form->add_render_attribute('gloo-signature-wrapper' . $item_index, 'style', 'width: 100%;');
        echo '<div ' . $form->get_render_attribute_string('gloo-signature-wrapper' . $item_index) . '>';
        echo '<div class="gloo-signature-canvas-inner_wrapper" style="position: relative;/* display: inline-block;*/">';
        echo '<button type="button" class="gloo-signature-button-clear" data-action="clear" style="right: 0; position: absolute;">X</button>';
        // echo '<button type="button" class="gloo-signature-button-clear" data-action="clear" style="right: 0; position: absolute;">❌</button>';
        echo '<input ' . $form->get_render_attribute_string('input' . $item_index) . '>';
        echo '<canvas ' . $form->get_render_attribute_string('gloo-signature-canvas' . $item_index) . '></canvas>';
        echo '</div></div>';
    }


    /**
     * validate uploaded file field
     *
     * @param array                $field
     * @param Classes\Form_Record  $record
     * @param Classes\Ajax_Handler $ajax_handler
     */
    public function validation($field, $record, $ajax_handler)
    {
        $id = $field['id'];
        if ($field['required'] && $field['raw_value'] === '') {
            $ajax_handler->add_error($id, __('This signature field is required.', 'gloo_for_elementor'));
        }
        if ($field['raw_value'] === '') {
            return;
        }
        if (!\preg_match('&^data:image/(jpeg|png);base64,[\\w\\d/+]+=*$&', $field['raw_value'])) {
            $ajax_handler->add_error($id, __('Invalid signature.', 'gloo_for_elementor'));
        }
    }
 
    public function register_field_type( $fields ) {
        \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( self::get_type(), $this );
        $fields[ self::get_type() ] = self::get_name();
        return $fields;
    }
    
    public function save_to_file($data, $dir_name, $extension, $ajax_handler)
    {
        $dir_abs_path = trailingslashit(wp_upload_dir()['basedir']) . 'gloo/signatures';
        if($dir_name)
          $dir_abs_path = trailingslashit(wp_upload_dir()['basedir']) . 'gloo/signatures/' . $dir_name;
        $this->create_dir($dir_abs_path);
        // Code from Elementor Upload field:
        $filename = \uniqid() . '.' . $extension;
        $filename = wp_unique_filename($dir_abs_path, $filename);
        $new_file = trailingslashit($dir_abs_path) . $filename;
        if (\is_dir($dir_abs_path) && \is_writable($dir_abs_path)) {
            $res = \file_put_contents($new_file, $data);
            if ($res) {
                // Set correct file permissions.
                $perms = 0644;
                @\chmod($new_file, $perms);
                $url = wp_upload_dir()['baseurl'] . '/gloo/signatures/' . $filename;
                if($dir_name)
                  $url = wp_upload_dir()['baseurl'] . '/gloo/signatures/' . trailingslashit($dir_name) . $filename;
                return ['url' => $url, 'loc' => $new_file];
            } else {
                $ajax_handler->add_error_message(esc_html__('There was an error while trying to save your signature.', 'gloo_for_elementor'));
            }
        } else {
            $ajax_handler->add_admin_error_message(esc_html__('Signature save directory is not writable or does not exist.', 'gloo_for_elementor'));
        }
    }

    public function process_field($field, $record, $ajax_handler)
    {
      // db($record);exit();
        $value = $field['value'];
        if ($value === '') {
            return;
        }
        // $settings = $record->get( 'form_settings' );
        // db($settings);exit();
        $settings = $this->get_form_field_settings($field['id'], $record);

        if (($settings[$this->prefix.'_save_to_file'] ?? '') !== 'yes') {
          return;
        }

        \preg_match('&^data:image/(jpeg|png);base64,([\\w\\d/+]+=*)$&', $value, $matches);
        $extension = $matches[1];
        $encoded_image = $matches[2];
        // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
        $decoded_image = \base64_decode($encoded_image);

        $php_img = imagecreatefromstring($decoded_image);
        if($php_img == false)
          $ajax_handler->add_error_message('Provided image format is not supported.');
          
        $dir_name = $settings['_id'];
        if (!\preg_match('/[\\w\\d_]+/', $dir_name)) {
            $ajax_handler->add_admin_error_message(__('Invalid field ID', 'gloo_for_elementor'));
            return;
        }
        $dir_name = '';
        list('url' => $url, 'loc' => $loc) = $this->save_to_file($decoded_image, $dir_name, $extension, $ajax_handler);
        $record->update_field($field['id'], 'value', $url);
        $record->update_field($field['id'], 'raw_value', $loc);
    }


    public static function get_form_field_settings($id, $record)
    {
        $field_settings = $record->get_form_settings('form_fields');
        $field_settings = \array_filter($field_settings, function ($field) use($id) {
            return $field['custom_id'] === $id;
        });
        return \array_values($field_settings)[0];
    }

    /** Make sure the given dir is created and has protection files. */
    public static function create_dir($path)
    {
        if (\file_exists($path . '/index.php')) {
            return $path;
        }
        wp_mkdir_p($path);
        $files = [['file' => 'index.php', 'content' => ['<?php', '// Silence is golden.']], ['file' => '.htaccess', 'content' => ['Options -Indexes', '<ifModule mod_headers.c>', '	<Files *.*>', '       Header set Content-Disposition attachment', '	</Files>', '</IfModule>']]];
        foreach ($files as $file) {
            if (!\file_exists(trailingslashit($path) . $file['file'])) {
                $content = \implode(\PHP_EOL, $file['content']);
                @\file_put_contents(trailingslashit($path) . $file['file'], $content);
            }
        }
    }

    public function add_control_section_to_form($element, $args)
    {
        $element->start_controls_section(
          'gloo_section_signature_buttons_style', [
            'label' => __('Signature', 'gloo_for_elementor'), 
            'tab' => \Elementor\Controls_Manager::TAB_STYLE]
          );
        $element->add_responsive_control(
          'gloo_signature_canvas_width', [
            'label' => __('Width of the Signature Pad', 'gloo_for_elementor'), 
            'type' => \Elementor\Controls_Manager::SLIDER, 
            'size_units' 	=> [ 'px', '%','em','rem','vh' ],
            // 'size_units' 	=> [ 'px', '%','vh' ],
            'default' => ['unit' => 'px', 'size' => 400], 
            'range' => [
              'px' => [
                'min' 	=> 0,
                'max' 	=> 1400,
                'step' => 5
              ],
              // '%' => [
              //   'min' 	=> 0,
              //   'max' 	=> 100,
              // ],
              'em' => [
                'min' => 0.1,
						    'max' => 100,
                'step' => 1,
              ],
              'rem' => [
                'min' 	=> 0,
                'max' 	=> 100,
                'step' => 1
              ],
            ],
            // 'range' => ['px' => ['min' => 1, 'max' => 800, 'step' => 5]], 
            'selectors' => [
              '{{WRAPPER}} .gloo-signature-wrapper' => '--canvas-width: {{SIZE}}{{UNIT}};',
              '{{WRAPPER}} .gloo-signature-wrapper > div' => 'width: {{SIZE}}{{UNIT}};',
              // '{{WRAPPER}} .gloo-signature-wrapper .gloo-signature-canvas' => 'width: {{SIZE}}{{UNIT}};',
              ]
          ]
        );
        $element->add_responsive_control(
          'gloo_signature_canvas_height', [
            'label' => __('Height of the Signature Pad', 'gloo_for_elementor'), 
            'type' => \Elementor\Controls_Manager::SLIDER, 
            'size_units' 	=> [ 'px', 'em','rem','vh' ],
            // 'size_units' 	=> [ 'px', '%','vh' ],
            'default' => ['unit' => 'px', 'size' => 200], 
            'range' => [
              'px' => [
                'min' 	=> 0,
                'max' 	=> 1400,
                'step' => 5
              ],
              // '%' => [
              //   'min' 	=> 0,
              //   'max' 	=> 100,
              // ],
              'em' => [
                'min' => 0.1,
						    'max' => 100,
                'step' => 1,
              ],
              'rem' => [
                'min' 	=> 0,
                'max' 	=> 100,
                'step' => 1
              ],
            ],
            // 'range' => ['px' => ['min' => 1, 'max' => 800, 'step' => 5]], 
            'selectors' => [
              '{{WRAPPER}} .gloo-signature-wrapper' => '--canvas-height: {{SIZE}}{{UNIT}};',
              '{{WRAPPER}} .gloo-signature-wrapper > div' => 'height: {{SIZE}}{{UNIT}};',
              '.elementor-editor-active {{WRAPPER}} .gloo-signature-wrapper canvas' => 'height: {{SIZE}}{{UNIT}};',
              // '{{WRAPPER}} .gloo-signature-wrapper .gloo-signature-canvas' => 'width: {{SIZE}}{{UNIT}};',
              ]
          ]
        );
        $element->add_control(
          'gloo_signature_canvas_border_radius', 
          ['label' => __('Pad Border Radius', 'gloo_for_elementor'), 
          'type' => \Elementor\Controls_Manager::DIMENSIONS, 
          'default' => ['top' => '3', 'right' => '3', 'bottom' => '3', 'left' => '3', 'size_units' => 'px'], 
          'size_units' => ['px', '%'], 
          'selectors' => [
            '{{WRAPPER}} .gloo-signature-canvas' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            '{{WRAPPER}} .gloo-signature-button-clear' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
          ], 
          'separator' => 'before'
        ]
      );
      $element->add_control(
        'gloo_signature_canvas_border_width', [
          'label' => __('Pad Border Width', 'gloo_for_elementor'), 
          'type' => \Elementor\Controls_Manager::DIMENSIONS, 
          'default' => ['top' => '1', 'right' => '1', 'bottom' => '1', 'left' => '1', 'size_units' => 'px'], 
          'size_units' => ['px'], 
          'selectors' => ['{{WRAPPER}} .gloo-signature-canvas' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']
        ]
      );
        $element->add_control(
          'gloo_signature_canvas_background_color', [
            'label' => __('Pad Background Color', 'gloo_for_elementor'), 
            'type' => \Elementor\Controls_Manager::COLOR, 
            'default' => '#ffffff', 
            'selectors' => ['{{WRAPPER}} .gloo-signature-canvas' => 'background-color: {{VALUE}};']
          ]
        );
        $element->add_control(
          'gloo_signature_canvas_pen_color', [
            'label' => __('Pen Color', 'gloo_for_elementor'), 
            'type' => \Elementor\Controls_Manager::COLOR, 'default' => '#000000'
          ]
        );

        $this->add_button_style_controls($element, $this->prefix);
        $element->end_controls_section();
    }
}
