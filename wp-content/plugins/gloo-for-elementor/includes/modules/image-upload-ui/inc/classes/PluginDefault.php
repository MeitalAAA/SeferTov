<?php
namespace Gloo\Modules\ImageUploadUI;

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PluginDefault extends Plugin{

	private static $instance = null;
	
	/******************************************/
	/***** Single Ton base intialization of our class **********/
	/******************************************/
  public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){
    // add javascript and css to wp-admin dashboard.
		if(is_admin()){
			add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}else{
			add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		}
		// if(is_admin()){
		// 	new Admin\PageSettings();
		// }

		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'add_upload_to_media_library'
		], 11 );
		add_filter( 'elementor_pro/forms/render/item', [ $this, 'field_render_filter' ], 9, 3 );


		add_action( 'elementor/element/form/section_form_style/after_section_end', [
			$this,
			'add_control_section_to_form'
		], 10, 2 );


		add_action( 'elementor_pro/forms/process/upload', [ $this, 'process_upload_to_media_library' ], 11, 3 );
		
  }// construct function end here



	public function field_render_filter( $item, $item_index, $form ) {
	
		if (isset($item['field_type']) && $item['field_type'] == 'upload' && isset( $item['gloo_upload_image_ui'] ) && $item['gloo_upload_image_ui'] == 'yes' && $item['gloo_filepond_upload'] != 'yes') {
			$form->add_style_depends( 'gloo-for-elementor' );
			$form->add_script_depends( 'gloo-form-image-ui' );

			$form->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo-image-upload-ui-wrapper' );
			$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo-image-upload-ui-input' );

			$is_post_edit_active = gloo()->modules->is_module_active('gloo_frontend_post_editing');

			if(/*!$is_post_edit_active && */isset($item['gloo_upload_image_ui_placeholder']) && is_array($item['gloo_upload_image_ui_placeholder']) && isset($item['gloo_upload_image_ui_placeholder']['url']) && !empty($item['gloo_upload_image_ui_placeholder']['url']) && empty($item['gloo_filepond_upload'])){
				$attachment_url = $item['gloo_upload_image_ui_placeholder']['url'];
				
					$form->add_render_attribute( 'field-group' . $item_index, 'style', '--gloo-upload-image-ui-url: url(' . $attachment_url . ');');
					//$form->add_render_attribute( $input_type . $item_index, 'style', '--gloo-upload-image-ui-url: url(' . $attachment_url . ')' );
					$form->set_render_attribute( 'upload' . $item_index, 'data-background-image', $attachment_url );
					$form->set_render_attribute( 'field-group' . $item_index, 'data-background-image', $attachment_url);
				
			}
			
		}

		return $item;
	}
	

	public function add_upload_to_media_library( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' )	;

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		if ( isset( $control_data['fields']['gloo_upload_files_to_media'] ) ) {
			return;
		}


		$is_filepond_active = gloo()->modules->is_module_active('gloo_form_filepond_upload');
		
		$conditions = [
			'field_type' => 'upload',
		];
		$field_controls = [];
		$field_controls['gloo_upload_files_to_media'] = [
			'name'         => 'gloo_upload_files_to_media',
			'label'        => __( 'Upload To Media Library', 'gloo' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'condition'    => $conditions,
			'tab'          => 'content',
			'inner_tab'    => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];
		if($is_filepond_active){
			$conditions['gloo_filepond_upload!'] = 'yes';
		}
		$field_controls['gloo_upload_image_ui'] = [
			'name'         => 'gloo_upload_image_ui',
			'label'        => __( 'Image Upload UI', 'gloo' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'description'  => 'Make sure input is not required by elementor.',
			'condition'    => $conditions,
			'tab'          => 'content',
			'inner_tab'    => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];
		$conditions['gloo_upload_image_ui'] = 'yes';
		$field_controls['gloo_upload_image_ui_note'] = [
			'name'         => 'gloo_upload_image_ui_note',
			'type'         => \Elementor\Controls_Manager::RAW_HTML,
			'raw' => __( 'Note: It works only for single image.', 'gloo_for_elementor' ),
			'content_classes' => 'elementor-control-field-description',
			'condition'    => $conditions,
			'tab'          => 'content',
			'inner_tab'    => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];
		$field_controls['gloo_upload_image_ui_placeholder'] = [
			'name'         => 'gloo_upload_image_ui_placeholder',
			'label'        => __( 'Image Upload UI Placeholder', 'gloo' ),
			'type'         => \Elementor\Controls_Manager::MEDIA,
			'default'      => [
				'url' => \Elementor\Utils::get_placeholder_image_src(),
			],
			'condition'    => $conditions,
			'tab'          => 'content',
			'inner_tab'    => 'form_fields_content_tab',
			'tabs_wrapper' => 'form_fields_tabs',
		];


		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}


	public function process_upload_to_media_library( $field, $record, $ajax_handler ) {
			$submit_actions = $record->get_form_settings( 'submit_actions' );

			/* exclude process upload for form actions to restrict double image upload */
			if(!empty($submit_actions) ) {	

				$actions = array(
					'frontend_post_editing',
					'frontend_post_creation'
				);
 				foreach( $actions as $form_action ) {
					if(in_array($form_action, $submit_actions)) {
						return;
					}
				}
			}

			$form_fields    = $record->get_form_settings( 'form_fields' );
			$field_settings = [];
			$field_id       = $field['id'];
			foreach ( $form_fields as $form_field ) {
				if ( $form_field['custom_id'] === $field_id ) {
					$field_settings = $form_field;
				}
			}
			
			/* exit if frontend post actions enabled otherwise image uploaded twice */
			if ( ! isset( $field_settings['gloo_upload_image_ui'] ) || $field_settings['gloo_upload_image_ui'] != 'yes' ) {
				return;
			}

			if ( ! isset( $field_settings['gloo_upload_files_to_media'] ) || ! $field_settings['gloo_upload_files_to_media'] ) {
				return;
			}

			/* store already uploaded file for further process */
			$uploaded_ids = $record->get( 'uploaded_ids' );

			if(isset($_REQUEST['form_fields'][ $field_id ]) && !empty($_REQUEST['form_fields'][ $field_id ])) {
				if(is_array($_REQUEST['form_fields'][ $field_id ])) {
					foreach( $_REQUEST['form_fields'][ $field_id ] as $uploaded_id ) {
						$uploaded_ids[$field_id][] = $uploaded_id;
					}
				} else {
					$uploaded_ids[$field_id][] = $_REQUEST['form_fields'][ $field_id ];
				}
			}

			if(!empty($uploaded_ids)) {
				$record->set( 'uploaded_ids', $uploaded_ids );
			}

			$files = $record->get( 'files' );
			$uploaded_media = $record->get( 'uploaded_to_media' );
			 
			if ( ! isset( $files[ $field_id ] ) ) {
				return; // no images
			}

			foreach ( $files[ $field_id ]['path'] as $index => $path ) {

				if(isset($files['uploaded_to_media']) && isset($files['uploaded_to_media'][ $field_id ]) && is_array($files['uploaded_to_media'][ $field_id ]) && count($files['uploaded_to_media'][ $field_id ]) >= 1)
						continue;

				if ( isset( $_FILES['form_fields'] ) ) {
					$original_filename = pathinfo( $_FILES['form_fields'][ $field_id ][ $index ]['name'], PATHINFO_FILENAME );
				} else {
					$original_filename = pathinfo( $path, PATHINFO_FILENAME );
				}

				$attachment = array(
					'post_mime_type' => $_FILES['form_fields'][ $field_id ][ $index ]['type'],
					'post_title'     => sanitize_file_name( $original_filename ),
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				$attach_id   = wp_insert_attachment( $attachment, $path );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $path );
				wp_update_attachment_metadata( $attach_id, $attach_data );
				$uploaded_media['uploaded_to_media'][ $field_id ][] = $attach_id;
			}
 
			$record->set( 'uploaded_to_media', $uploaded_media );
		// }
	}



	public function add_control_section_to_form( $element, $args ) {

		$element->start_controls_section(
			'gloo_image_upload_ui_style',
			[
				'label' => __( 'Image Upload UI', 'gloo' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_responsive_control(
			'gloo_image_upload_ui_width',
			[
				'label'      => __( 'Width', 'gloo' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 180,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					//'{{WRAPPER}} .gloo-image-upload-ui-wrapper input:before' => 'width: {{SIZE}}{{UNIT}};',
					//'{{WRAPPER}} .gloo-image-upload-ui-wrapper input:after'  => 'width: {{SIZE}}{{UNIT}};',
					
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:before' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:after'  => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper' => '--img-ui-width:{{SIZE}}',
				],
			]
		);

		$element->add_responsive_control(
			'gloo_image_upload_ui_height',
			[
				'label'      => __( 'Height', 'gloo' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 135,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper input'        => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:before' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:after'  => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper' => '--img-ui-height:{{SIZE}}',
					
				],
			]
		);

		$element->add_responsive_control(
			'gloo_image_upload_ui_top',
			[
				'label'      => __( 'Top', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:before' => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:after'  => 'top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper' => '--img-ui-top:{{SIZE}}',
				],
			]
		);

		$element->add_responsive_control(
			'gloo_image_upload_ui_left',
			[
				'label'      => __( 'Left', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:before' => 'left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:after'  => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'gloo_image_upload_ui_bottom',
			[
				'label'      => __( 'Bottom', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:before' => 'bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:after'  => 'bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'gloo_image_upload_ui_right',
			[
				'label'      => __( 'Right', 'gloo_for_elementor' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'default'    => [
					'unit' => 'px',
					'size' => 0,
				],
				'range'      => [
					'px' => [
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:before' => 'right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:after'  => 'right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$element->add_responsive_control(
			'gloo_upload_image_ui_overlay',
			[
				'label'     => __( 'Image Upload UI Overlay', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::MEDIA,
				'default'   => [
					'url' => gloo()->plugin_url( 'assets/images/upload-image-ui-overlay.png' ),
				],
				'selectors' => [
					'{{WRAPPER}} .gloo-image-upload-ui-wrapper label:after' => 'background-image: url({{URL}});',
				],
			]
		);

		$element->end_controls_section();
	}

	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    if(isset($_GET['page']) && $_GET['page'] === $this->prefix){

      // wp_register_style( $this->prefix.'_wp_admin_css', bbwp_engine()->plugin_url().'modules/db-backup/css/style.css', array(), '1.0.0' );
      // wp_enqueue_style($this->prefix.'_wp_admin_css');

      // wp_register_script( $this->prefix.'_wp_admin_script', bbwp_engine()->plugin_url().'modules/db-backup/js/script.js', array('jquery'), '1.0.0' );
      //wp_enqueue_script( $this->prefix.'_wp_admin_script' );


      //$js_variables = array('prefix' => $this->prefix."_");
      //wp_localize_script( $this->prefix.'_wp_admin_script', $this->prefix, $js_variables );

		}

		if(is_admin()){
			//admin js
		}else{
			$script_abs_path = gloo()->plugin_path( 'includes/modules/image-upload-ui/assets/frontend/style.css');
			wp_enqueue_style( $this->prefix.'_style',  gloo()->plugin_url( 'includes/modules/image-upload-ui/assets/frontend/style.css'), array(), get_file_time($script_abs_path));
		}

  }// wp_admin_style_scripts


	
	public function inject_field_controls( $array, $controls_to_inject ) {
		$keys      = array_keys( $array );
		$key_index = array_search( 'required', $keys ) + 1;

		return array_merge( array_slice( $array, 0, $key_index, true ),
			$controls_to_inject,
			array_slice( $array, $key_index, null, true )
		);
	}
	
} // BBWP_CustomFields class

