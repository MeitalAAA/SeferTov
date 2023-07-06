<?php

namespace Gloo\Modules\Form_Post_Editing;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo_form_post_editing';
 
	/**
	 * Constructor for the class
	 */
	public function __construct() {

		$this->init();
	}

	/**
	 * Init module components	
	 *
	 * @return [type] [description]
	 */
	public function init() {

		if(is_admin()){
			$file_path = gloo()->modules_path('frontend-post-editing/inc/PageSettings.php');
			require_once $file_path;
			new \Gloo\Modules\Form_Post_Editing\PageSettings();
		}

		add_action( 'elementor_pro/init', [ $this, 'register_form_action' ] );
		add_filter( 'elementor_pro/forms/render/item', [ $this, 'check_post_editing_action' ], 10, 3 );
		add_action( 'wp_ajax_elementor_pro_forms_send_form', [ $this, 'ensure_get_parameters' ] );
		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'add_upload_to_media_library'
		], 11 );
		add_action( 'elementor_pro/forms/process/upload', [ $this, 'process_upload_to_media_library' ], 11, 3 );

		$is_image_upload_ui_active = gloo()->modules->is_module_active('gloo_form_image_upload_ui');
		if(!$is_image_upload_ui_active){
			add_action( 'elementor/element/form/section_form_style/after_section_end', [
				$this,
				'add_control_section_to_form'
			], 10, 2 );
		}
		

		add_filter( 'elementor_pro/forms/render/item/upload', [ $this, 'field_render_filter' ], 10, 3 );

		add_action( 'elementor_pro/forms/process/date', [ $this, 'maybe_change_to_timestamp' ], 11, 3 );
		add_filter( 'elementor_pro/forms/render/item/date', [ $this, 'maybe_revert_to_date_format' ], 10, 3 );
		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'add_save_as_timestamp_controls'
		], 11 );
	}

	public function add_save_as_timestamp_controls( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		if ( isset( $control_data['fields']['gloo_date_save_as_timestamp'] ) ) {
			return;
		}

		$field_controls =
			[
				'gloo_date_save_as_timestamp' => [
					'name'         => 'gloo_date_save_as_timestamp',
					'label'        => __( 'Save as timestamp', 'gloo' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'condition'    => [
						'field_type' => 'date',
					],
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
			];


		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}

	public function maybe_revert_to_date_format( $item, $item_index, $element ) {

		$value        = $item['field_value'];
		$is_timestamp = ( (string) (int) $value === $value )
		                && ( $value <= PHP_INT_MAX )
		                && ( $value >= ~PHP_INT_MAX );

		if ( $is_timestamp && isset( $item['gloo_date_save_as_timestamp'] ) && $item['gloo_date_save_as_timestamp'] ) {
			$element->set_render_attribute( 'input' . $item_index, 'value', date( 'Y-m-d', $item['field_value'] ) );
		}

		return $item;

	}

	public function maybe_change_to_timestamp( $field, $record, $ajax_handler ) {

		$form_fields    = $record->get_form_settings( 'form_fields' );
		$field_settings = [];
		$field_id       = $field['id'];
		foreach ( $form_fields as $form_field ) {
			if ( $form_field['custom_id'] === $field_id ) {
				$field_settings = $form_field;
			}
		}

		if ( ! isset( $field_settings['gloo_date_save_as_timestamp'] ) || ! $field_settings['gloo_date_save_as_timestamp'] ) {
			return;
		}

		$value = strtotime( $field['value'] );
		$record->update_field( $field['id'], 'value', $value );
		$record->update_field( $field['id'], 'raw_value', $value );
	}

	public function field_render_filter( $item, $item_index, $form ) {
	
		if ( ((isset( $item['gloo_upload_image_ui'] ) && $item['gloo_upload_image_ui'])) && $item['gloo_filepond_upload'] != 'yes') {
			$form->add_style_depends( 'gloo-for-elementor' );
			$form->add_script_depends( 'gloo-form-image-ui' );

			$form->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo-image-upload-ui-wrapper' );
			$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo-image-upload-ui-input' );
		}

		return $item;
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

	public function add_upload_to_media_library( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' )	;

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		if ( isset( $control_data['fields']['gloo_upload_files_to_media'] ) ) {
			return;
		}

		$is_image_upload_ui_active = gloo()->modules->is_module_active('gloo_form_image_upload_ui');
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
		$field_controls['gloo_upload_random_no'] = [
			'name'         => 'gloo_upload_random_no',
			'label'        => __( 'Random Title', 'gloo' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'description'  => 'This will store the uploaded image title as random number',
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

		if(!empty($submit_actions) && in_array('frontend_post_editing', $submit_actions)) {	
		
			$form_fields    = $record->get_form_settings( 'form_fields' );
			$field_settings = [];
			$field_id       = $field['id'];
			foreach ( $form_fields as $form_field ) {
				if ( $form_field['custom_id'] === $field_id ) {
					$field_settings = $form_field;
				}
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

				if ( isset( $field_settings['gloo_upload_random_no'] ) && $field_settings['gloo_upload_random_no'] == 'yes' ) {
					$attachment_title = rand(1000,9999);
			   	} else {
					$attachment_title = sanitize_file_name( $original_filename );
				}

				$attachment = array(
					'post_mime_type' => $_FILES['form_fields'][ $field_id ][ $index ]['type'],
					'post_title'     => $attachment_title,
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				$attach_id   = wp_insert_attachment( $attachment, $path );
				$attach_data = wp_generate_attachment_metadata( $attach_id, $path );
				wp_update_attachment_metadata( $attach_id, $attach_data );

				if ( isset( $field_settings['gloo_upload_random_no'] ) && $field_settings['gloo_upload_random_no'] == 'yes' ) {
					$attachment_media = array(
						'ID'           => $attach_id,
						'post_title'   => $attachment_title,
					);
					wp_update_post( $attachment_media );
				}

				$uploaded_media['uploaded_to_media'][ $field_id ][] = $attach_id;
			}
 
			$record->set( 'uploaded_to_media', $uploaded_media );
		}
	}

	public function inject_field_controls( $array, $controls_to_inject ) {
		$keys      = array_keys( $array );
		$key_index = array_search( 'required', $keys ) + 1;

		return array_merge( array_slice( $array, 0, $key_index, true ),
			$controls_to_inject,
			array_slice( $array, $key_index, null, true )
		);
	}

	public function ensure_get_parameters() {
		$query_params = parse_url( wp_get_referer(), PHP_URL_QUERY );
		if ( $query_params ) {
			global $_GET;
			$new_parameters = [];
			parse_str( $query_params, $new_parameters );
			$_GET = $_GET + $new_parameters;
		}
	}

	public function register_form_action() {

		// Include the form actions
		foreach ( glob( gloo()->modules_path( 'frontend-post-editing/inc/form-action/*.php' ) ) as $file ) {
			require $file;
		}

		$quantity = get_option('gloo_frontend_post_editing_form_actions_quantity', 1);
		if(!(!empty($quantity) && $quantity && is_numeric($quantity) && $quantity >= 1))
			$quantity = 1;
		
		for($i = 1; $i <= $quantity; $i++){
			
			$form_action = new \Gloo\Modules\Form_Post_Editing\Frontend_Post_Editing('gloo_frontend_post_editing', $i);
			// Register the action with form widget
			\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $form_action->get_name(), $form_action );

			// $ZohoCampaignsAfterSubmit = new ZohoCampaignsAfterSubmit('zohoformsubmitaction', $i);
			// \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $ZohoCampaignsAfterSubmit->get_name(), $ZohoCampaignsAfterSubmit );
		}

		/*$classes = [
			'Frontend_Post_Editing'
		];

		// register tags
		foreach ( $classes as $class ) {

			$class       = "Gloo\Modules\Form_Post_Editing\\{$class}";
			$form_action = new $class;

			// Register the action with form widget
			\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $form_action->get_name(), $form_action );
		}*/

	}
 
	public function formate_post_type_value_by_return($post_ids, $field_with_settings, $item_custom_id) {
		//echo '<pre>'; print_r($field_with_settings); echo '</pre>';
		
 		if( empty($field_with_settings) || empty($post_ids)) {
			return $post_ids;
		}

		foreach($field_with_settings as $single_post_type_field) {
			if(isset($single_post_type_field['custom_id']) && $single_post_type_field['custom_id'] == $item_custom_id) {
				$post_type_field_settings = $single_post_type_field;
				break;
			}
		}
 
		$return_type = $post_type_field_settings['gloo_cpt_fields_input_return_value'];
		$post_type = $post_type_field_settings['gloo_cpt_fields_post_type'];
 
		if( $return_type == 'title' || $return_type == 'slug' ) {
 			$post_data = array();
			$arr_values = $post_ids;

			if(!empty($arr_values)) {
				foreach( $arr_values as $arr_value ) {
					
					if( $return_type == 'title' ) {

						$post_data[] = get_the_title( $arr_value );
						
					} elseif($return_type == 'slug') {
						$post_data[] = get_permalink( $arr_value );
					}
				}
			}
			
			return $post_data;
		} else {
			return $post_ids;
		}

	}
 
	public function formate_terms_type_value_by_return($terms_ids, $field_with_settings, $item_custom_id) {
		//echo '<pre>'; print_r($field_with_settings); echo '</pre>';
		
 		if( empty($field_with_settings) || empty($terms_ids)) {
			return $terms_ids;
		}

		foreach($field_with_settings as $single_term_field) {
			if(isset($single_term_field['custom_id']) && $single_term_field['custom_id'] == $item_custom_id) {
				$term_field_settings = $single_term_field;
				break;
			}
		}
 
		$return_type = $term_field_settings['gloo_term_fields_output_value_type'];
 
		if( $return_type == 'title' || $return_type == 'slug' ) {

 			$query = $term_field_settings['gloo_term_fields_query'];
			$taxonomy = $term_field_settings['gloo_term_fields_by_tax'];
 			$terms_data = array();
			$arr_values = $terms_ids;
 
			if(!empty($arr_values)) {
				foreach( $arr_values as $arr_value ) {
					$term_value = get_term( $arr_value ); 

					if( $return_type == 'title' ) {
						$terms_data[] = $term_value->name;
					} elseif($return_type == 'slug') {
						$terms_data[] = $term_value->slug;
					}
				}
			}


			return $terms_data;
		} else {
			return $terms_ids;
		}

	}


	public function check_post_editing_action( $item, $item_index, $element ) {

		$submit_actions = $element->get_settings_for_display( 'submit_actions' );

		if ( ! $submit_actions || ! in_array( "frontend_post_editing", $submit_actions ) ) {
			return $item;
		}

		$settings = $element->get_settings_for_display();

		$form_meta_fields = $settings['gloo_frontend_post_editing_meta_fields'];
		$editing_post_id  = $settings['gloo_frontend_post_editing_post_id'];
		$field_with_settings = $settings['form_fields'];


		$item_custom_id = $item['custom_id'];

		$input_type = 'input';

		if ( $item['field_type'] === 'select' ) {
			$input_type = 'select';
		}

		// check default fields
		$title_field   = $settings['gloo_frontend_post_editing_post_title'] ?: 'title';
		$content_field = $settings['gloo_frontend_post_editing_post_content'] ?: 'content';

		if ( $item_custom_id === $title_field || $item_custom_id === $content_field ) {

			if ( $item_custom_id === $title_field ) {
				$value = get_post_field( 'post_title', $editing_post_id );
			} else {
				$value = get_post_field( 'post_content', $editing_post_id );
			}

			$item['field_value'] = $value;
			$element->add_render_attribute( $input_type . $item_index, 'value', $value );
		}

		// featured image
		$post_image_id = $settings['gloo_frontend_post_editing_post_image'] ?: '';
		if ( $post_image_id && $item_custom_id === $post_image_id ) {
			$attachment_url = $item['gloo_upload_image_ui_placeholder']['url'];
			if ( $value = get_the_post_thumbnail_url( $editing_post_id ) ) {
				$attachment_url = $value;
			}
			if(empty($item['gloo_filepond_upload']) || $item['gloo_filepond_upload'] == 'no') {
				$element->add_render_attribute( 'field-group' . $item_index, 'style', '--gloo-upload-image-ui-url: url(' . $attachment_url . ');');
				//$element->add_render_attribute( $input_type . $item_index, 'style', '--gloo-upload-image-ui-url: url(' . $attachment_url . ')' );
				$element->set_render_attribute( $input_type . $item_index, 'data-background-image', $attachment_url );
				$element->set_render_attribute( 'field-group' . $item_index, 'data-background-image', $attachment_url);
			}
		}

		$term_repeater = $settings['gloo_frontend_post_editing_post_terms_repeater'] ?: '';
		if ( ! empty( $term_repeater ) ) {
			$field_key = array_search( $item_custom_id, array_column( $term_repeater, 'field_id' ) );
			if ( $field_key !== false ) {

				$taxonomies                 = get_taxonomies( [ 'public' => true ] );
				$terms                      = wp_get_post_terms( $editing_post_id, $taxonomies, array( 'fields' => 'ids' ) );

				$terms = $this->formate_terms_type_value_by_return( $terms, $field_with_settings, $item_custom_id );
				$item['gloo_checked_terms'] = $terms;
			}
		}
		
		$post_type_repeater = $settings['gloo_frontend_post_editing_post_type_fields'] ?: '';

		if ( ! empty( $post_type_repeater ) ) {
			$field_key = array_search( $item_custom_id, array_column( $post_type_repeater, 'field_id' ) );
			if ( $field_key !== false ) {
				
				/* condition for new jet relation field */
				if( $post_type_repeater[ $field_key ]['is_relation_field'] == 'new_jet_engine_relation' ) {
					
					$jet_relation = $post_type_repeater[ $field_key ]['jet_relation'];
					$jet_rel_context = $post_type_repeater[ $field_key ]['jet_rel_context'];

					if(!empty($jet_relation) && !empty($jet_rel_context)) {

						$relation_instance = jet_engine()->relations->get_active_relations( $jet_relation );
						$object_id = $editing_post_id;

						if(!empty($relation_instance)) {
							switch ( $jet_rel_context ) {
								case 'parent_object':
									$related_ids = $relation_instance->get_parents( $object_id, 'ids' );
									break;
					
								default:
									$related_ids = $relation_instance->get_children( $object_id, 'ids' );
									break;
							}
						}
						
						$related_ids = ! empty( $related_ids ) ? $related_ids : array();
						$related_ids = $this->formate_post_type_value_by_return($related_ids, $field_with_settings, $item_custom_id);

						$item['gloo_checked_post_types'] = $related_ids;
						$item['field_options'] = $related_ids;
					}
					
				} else {
					$post_type_meta_id = $post_type_repeater[ $field_key ]['meta_field'];
					$value = $this->formate_post_type_value_by_return($value, $field_with_settings);

					$value = get_post_meta( $editing_post_id, $post_type_meta_id );
					$item['gloo_checked_post_types'] = $value;
				}
			}
		}	

		/* user types field */
		$user_type_repeater = $settings['gloo_frontend_post_editing_user_type_fields'] ?: '';
		if ( ! empty( $user_type_repeater ) ) {
			$field_key = array_search( $item_custom_id, array_column( $user_type_repeater, 'field_id' ) );
			
			if(isset($user_type_repeater[ $field_key ]['meta_field_type'])) {
				if($user_type_repeater[ $field_key ]['meta_field_type'] == 'jet_engine_rel') {
					
					$jet_relation = $user_type_repeater[ $field_key ]['jet_relation'];
					$jet_rel_context = $user_type_repeater[ $field_key ]['jet_rel_context'];

					if(!empty($jet_relation) && !empty($jet_rel_context)) {

						$relation_instance = jet_engine()->relations->get_active_relations( $jet_relation );
						$object_id = $editing_post_id;

						if(!empty($relation_instance)) {
							switch ( $jet_rel_context ) {
								case 'parent_object':
									$related_ids = $relation_instance->get_parents( $object_id, 'ids' );
									break;
					
								default:
									$related_ids = $relation_instance->get_children( $object_id, 'ids' );
									break;
							}
						}
						
						$related_ids = ! empty( $related_ids ) ? $related_ids : array();
						$item['gloo_checked_user_types'] = $related_ids;
					}
					
				} else {
					if ( $field_key !== false ) {
						$post_type_meta_id = $post_type_repeater[ $field_key ]['meta_field'];
						$value = get_post_meta( $editing_post_id, $post_type_meta_id );
						$item['gloo_checked_user_types'] = $value;
					}
				}
			}
		}	

		$field_key = array_search( $item_custom_id, array_column( $form_meta_fields, 'field_id' ) );
		if ( $field_key !== false ) {

			$meta_field_id = $form_meta_fields[ $field_key ]['meta_field'];
			$is_relation_field = $form_meta_fields[ $field_key ]['is_relation_field'];

			$is_relation_field = $form_meta_fields[ $field_key ]['is_relation_field'];
			$store = $form_meta_fields[ $field_key ]['store_image'];
			if(isset($form_meta_fields[ $field_key ]['store_image_meta_type']) && $store == 'url' && $form_meta_fields[ $field_key ]['store_image_meta_type'] == 'acf')
				$store = 'id';
			$value = get_post_meta( $editing_post_id, $meta_field_id, true );
		
			if(!empty($value)) {
				$formated_ids = $this->get_image_attribute_render_value($store, $value);
			}
			
			
			if ( $form_meta_fields[ $field_key ]['is_image'] && !empty($value)) {

				if(isset($item['gloo_filepond_upload']) && $item['gloo_filepond_upload'] == 'yes') {
 					
					$attachments = array();

					if (!empty($formated_ids)) {
						if(is_array($formated_ids)) {
							foreach( $formated_ids as $val ) {
								$attachments[] = [
									'id' => $val
								];
							}
						}  else {
							$attachments[] = [
								'id' => $formated_ids
							];
						}

						$element->add_render_attribute( 'field-group' . $item_index, 'data-gloo-uploads', wp_json_encode($attachments) );
					}
				} else {
					 /* only for image meta values */ 
					if($item_custom_id != $post_image_id) {
						$attachment_url = $item['gloo_upload_image_ui_placeholder']['url'];
						$attachment_url = wp_get_attachment_url($formated_ids);
						
						$element->add_render_attribute( 'field-group' . $item_index, 'style', '--gloo-upload-image-ui-url: url(' . $attachment_url . ');');
						$element->set_render_attribute( 'field-group' . $item_index, 'data-background-image', $attachment_url);
						$element->set_render_attribute( $input_type . $item_index, 'data-background-image', $attachment_url );
// 						$element->add_render_attribute( $input_type . $item_index, 'style', '--gloo-upload-image-ui-url: url(' . $attachment_url . ')' );
					}
				}
					
			} else {
				
				$item['field_value'] = $value;
				if($item['field_type'] == 'checkbox') {
					
					$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );
					$html = '';
					if ( $options ) {
						foreach ( $options as $key => $option ) {
							$element_id = $item['custom_id'] . $key;
							$option_value = $option;
							$option_label = $option;
							if ( false !== strpos( $option, '|' ) ) {
								list( $option_label, $option_value ) = explode( '|', $option );
							}
							$option_value = trim($option_value);
							$option_label = trim($option_label);
							
							if(!$option_value)
								$option_value = $option_label;
							
							// $option_value = (bolean) $option_value;
							if ($value && is_array($value) && count($value) >= 1 && isset($value[$option_value]) && ($value[$option_value] == 'true' || $value[$option_value] === true)) {
								
								$element->add_render_attribute( $element_id, 'checked', 'checked' );
							}
							else if ( ! empty( $item['field_value'] ) && $option_value === $item['field_value'] ) {
								$element->add_render_attribute( $element_id, 'checked', 'checked' );
							}else{
								$element->add_render_attribute( $input_type . $item_index, 'value', $value );
							}
						}
					}

					
				}elseif($item['field_type'] == 'acceptance') {
					if($value == 'yes' || $value == 'on')
						$element->add_render_attribute( $input_type . $item_index, 'checked', 'checked');
				}elseif($item['field_type'] == 'gloo_datepicker_field') {
					
					/* handle date picker field */
					$is_datepicker_field = $form_meta_fields[ $field_key ]['is_gloo_datepicker'];
					$is_timestamp = $form_meta_fields[ $field_key ]['is_timestamp'];
					$value         = get_post_meta( $editing_post_id, $meta_field_id, true );
					
					if (isset($is_datepicker_field) && $value && isset($is_timestamp) && $is_timestamp == 'yes') {
						$value = date('d-m-Y', $value);
						$element->add_render_attribute( $input_type . $item_index, 'value', $value );
					}
				} else {
					$element->add_render_attribute( $input_type . $item_index, 'value', $value );
				}
			}
		}

		if($item['field_type'] == 'gloo_repeater_start_field'){
			$this->add_repeater_field_data($editing_post_id, $item, $item_index, $element);
		}

		return $item;

	}

	public function get_image_attribute_render_value( $store, $saved_value ) {
 		$values = array();
 
		switch ( $store ) {
			case 'url' :
				$value = attachment_url_to_postid($saved_value);
				break;
			case 'array_multi_url' :
				foreach( $saved_value as $val ) {
					$value[] = attachment_url_to_postid($val);
				}
				break;
			case 'array' :
				foreach( $saved_value as $val ) {
					$value[] = $val['id'];
				}
				break;
			case 'array_multi_id' :
				$value = $saved_value;
				break;
			case 'comma_separated_string' :
				$value = explode( ",", $saved_value );
				break;
			case 'comma_separated_string_url' :
				$urls = explode( ",", $saved_value );
				foreach( $urls as $val ) {
					$value[] = attachment_url_to_postid($val);
				}
				break;
			case 'id' :
				$value = $saved_value;
				break;
			default:
				$value[] = $saved_value;

		}

		return $value;
	}
	
	public function add_repeater_field_data($editing_post_id, $item, $item_index, $element){
		
		$prefix = 'gloo_frontend_post_editing';
		
		$settings = $element->get_settings_for_display();
		$is_repeater_field   = $settings[ $prefix . '_is_repeater_field' ] ?: '';
		// $allowed_repeater_fields   = $settings[ $prefix . '_allowed_repeater_fields' ] ?: '';

		$sub_prefix = '';
		for($i = 1; $i <= 10; $i++){
			if($i >= 2)
				$sub_prefix = '_'.$i;
			if(isset($settings[$prefix.$sub_prefix.'_form_repeater_id']) && $item['custom_id'] == $settings[$prefix.$sub_prefix.'_form_repeater_id']){
				break;
			}
		}

		
		if( $is_repeater_field == 'yes' ){
			
			// $field_with_settings = $element->get_form_settings('form_fields');
			$field_with_settings = $settings['form_fields'];
			$repeater_source = $settings[$prefix.$sub_prefix.'_repeater_source'];
			$form_repeater_id = $settings[$prefix.$sub_prefix.'_form_repeater_id'];
			$source_repeater_id = $settings[$prefix.$sub_prefix.'_source_repeater_id'];
			$new_repeater_with_values = array();
			$repeater_subfields   = $settings[ $prefix .$sub_prefix. '_repeater_subfields' ];

			if(!(is_array($repeater_subfields) && count($repeater_subfields) >= 1))
				$repeater_subfields = array();
		
			if(!empty($repeater_source) && !empty($form_repeater_id) && !empty($source_repeater_id) && !empty($field_with_settings)){
				
				$start_repeater = false;
				foreach($field_with_settings as $single_field){
					if($single_field['field_type'] == 'gloo_repeater_start_field' && $single_field['custom_id'] == $form_repeater_id){
						$start_repeater = true;
						break;
					}
				}

				if($start_repeater){
					
					if($repeater_source == 'jet_engine'){
						$jet_engine_repeater_meta = get_post_meta($editing_post_id, $source_repeater_id, true);
						if(!empty($jet_engine_repeater_meta) && is_array($jet_engine_repeater_meta) && count($jet_engine_repeater_meta) >= 1)
							$new_repeater_with_values = $jet_engine_repeater_meta;							
					}	
					else if($repeater_source == 'acf')
						$new_repeater_with_values = $this->get_acf_repeater_data($editing_post_id, $source_repeater_id, $repeater_subfields);
					
					$element->add_render_attribute( 'field-group' . $item_index, 'data-repeater-sub-prefix', $sub_prefix);
					$element->add_render_attribute( 'field-group' . $item_index, 'data-repeater-post-data', wp_json_encode($new_repeater_with_values));
					$element->add_render_attribute( 'field-group' . $item_index, 'data-repeater-sub-fields-maping', wp_json_encode($repeater_subfields));
					
				}
			}
		}

	}


	public function get_acf_repeater_data($editing_post_id, $source_repeater_id, $repeater_subfields){

		$output = array();
		$prefix = 'gloo_frontend_post_editing';
		$acf_repeater_meta = get_post_meta($editing_post_id, $source_repeater_id, true);
		if($acf_repeater_meta && $acf_repeater_meta >= 1 && !empty($repeater_subfields)){
			$acf_repeater_meta = (int) $acf_repeater_meta;
			for($i = 0; $i < $acf_repeater_meta; $i++){
				foreach($repeater_subfields as $key=>$single_sub_field){					
					$source_sub_field = $single_sub_field[$prefix .'_source_sub_field'];
					if(!empty($source_sub_field)){
						$subfield_key = $source_repeater_id.'_'.$i.'_'.$source_sub_field;
						$sub_field_meta_value = get_post_meta($editing_post_id, $subfield_key, true);
						$output['item-'.$i][$source_sub_field] = $sub_field_meta_value;
					}
					
				}
			}
			
		}

		return $output;

	}

	/**
	 * Returns the instance.
	 *
	 * @return Module
	 * @since  1.0.0
	 * @access public
	 */
	public static function instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}