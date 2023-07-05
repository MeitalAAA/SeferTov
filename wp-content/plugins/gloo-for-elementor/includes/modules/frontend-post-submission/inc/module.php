<?php

namespace Gloo\Modules\Form_Post_Submission;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

use Elementor\Utils;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;
	public $slug = 'gloo_form_post_submission';
	
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
			$file_path = gloo()->modules_path('frontend-post-submission/inc/PageSettings.php');
			require_once $file_path;
			new \Gloo\Modules\Form_Post_Submission\PageSettings();
		}

		add_action( 'elementor_pro/init', [ $this, 'register_form_action' ] );

		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'add_upload_to_media_library'
		], 11 );

		add_action( 'elementor_pro/forms/process/upload', [ $this, 'process_upload_to_media_library' ], 11, 3 );
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

	public function add_upload_to_media_library( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

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

		if(!empty($submit_actions) && in_array('frontend_post_creation', $submit_actions)) {	
 
			$form_fields    = $record->get_form_settings( 'form_fields' );
			$field_settings = [];
			$field_id       = $field['id'];
			foreach ( $form_fields as $form_field ) {
				if ( $form_field['custom_id'] === $field_id ) {
					$field_settings = $form_field;
				}
			}
 
			// if ( isset( $field_settings['gloo_upload_image_ui'] ) || $field_settings['gloo_upload_image_ui'] == 'yes' ) {
			// 	return;
			// }
			
			if ( ! isset( $field_settings['gloo_upload_files_to_media'] ) || ! $field_settings['gloo_upload_files_to_media'] ) {
				return;
			}

			$files = $record->get( 'files' );
 			$uploaded_media = $record->get( 'uploaded_to_media' );
 
			if ( ! isset( $files[ $field_id ] ) ) {
				return; // no images
			}
			
			foreach ( $files[ $field_id ]['path'] as $index => $path ) {
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

	public function register_form_action() {

		// Include the form actions
		foreach ( glob( gloo()->modules_path( 'frontend-post-submission/inc/form-action/*.php' ) ) as $file ) {
			require $file;
		}

		$quantity = get_option('gloo_frontend_post_creation_form_actions_quantity', 1);
		if(!(!empty($quantity) && $quantity && is_numeric($quantity) && $quantity >= 1))
			$quantity = 1;
		
		for($i = 1; $i <= $quantity; $i++){
			
			$form_action = new \Gloo\Modules\Form_Post_Submission\Frontend_Post_Submission('gloo_frontend_post_creation', $i);
			// Register the action with form widget
			\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $form_action->get_name(), $form_action );

			// $ZohoCampaignsAfterSubmit = new ZohoCampaignsAfterSubmit('zohoformsubmitaction', $i);
			// \ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $ZohoCampaignsAfterSubmit->get_name(), $ZohoCampaignsAfterSubmit );
		}

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