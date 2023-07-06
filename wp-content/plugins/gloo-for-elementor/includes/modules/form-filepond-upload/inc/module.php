<?php

namespace Gloo\Modules\Form_Filepond_Upload;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo_form_filepond_upload';
	public $depended_styles = [ 'gloo-for-elementor','gloo-filepond','gloo-filepond-image-preview' ];
	public $depended_scripts = [ 'gloo-filepond-js','gloo-filepond-image-preview','gloo-form-filepond-image'];

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

		add_action( 'elementor_pro/init', [ $this, 'init_fields' ] );

	}

	public function init_fields() {

		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'add_filepond_option_for_upload'
		], 20 );
 
		add_filter( 'elementor_pro/forms/render/item/upload', [ $this, 'add_filpond_render_attr' ], 10, 3 );
		add_action( 'rest_api_init', [ $this,'gloo_uploaded_files_request']);
		
	}
	
	public function gloo_uploaded_files_request() {
		register_rest_route('gloo-uploads/v1', '/action/', [
			'methods' => 'GET',
			'callback' => [ $this, 'gloo_get_api_uploads'],
			'permission_callback' => function(){
				return true;
			}
		]);
	}
	
	public function gloo_get_api_uploads(\WP_REST_Request $request) {
		
		$data = $request->get_params();

		if(isset($data['media_id']) && !empty($data['media_id'])) {
			return wp_get_attachment_url( $data['media_id'] );
		}
	}

	
	public function get_filepond_settings($item, $item_index, $form) {
		$settings = $form->get_settings_for_display( 'form_fields' );
		$setting_array = array();

		/* global settings */
		$setting_array['storeAsFile'] = true;
		$setting_array['instantUpload'] = false;
		$setting_array['allowProcess'] = false;	
		
		if( !empty($settings[ $item_index ][$this->prefix.'gloo_filepond_label']) ) {
			$setting_array['labelIdle'] = __($settings[ $item_index ][$this->prefix.'gloo_filepond_label'], 'gloo_for_elementor');
		} 
						
		return $setting_array;
	}


	public function add_filpond_render_attr( $item, $item_index, $form ) {
		// echo '<pre>'; print_r($item); echo '</pre>'; 

		if ( (isset( $item['gloo_filepond_upload'] ) && $item['gloo_filepond_upload'] == 'yes')) {
					
			foreach ( $this->depended_scripts as $script ) {
				$form->add_script_depends( $script );
			}

			foreach ( $this->depended_styles as $style ) {
				$form->add_style_depends( $style );
			}

			$field_settings = $this->get_filepond_settings($item, $item_index, $form);

			if(!empty($field_settings)) {
				$form->add_render_attribute( 'field-group' . $item_index, 'data-config', wp_json_encode( $field_settings ) );
			}
			
			$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo-filepond-upload' );
			$form->add_render_attribute( 'field-group' . $item_index, 'data-filepond-url', home_url() );

		}

		return $item;
	}
	
	public function add_filepond_option_for_upload( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		// echo '<pre>';
		// print_r($control_data['fields']);
		// echo '</pre>';
	 	// die();


		if ( is_wp_error( $control_data ) ) {
			return;
		}

		// if ( isset( $control_data['fields']['gloo_upload_files_to_media'] ) ) {
		// 	return;
		// }

		$field_controls =
			[
				'gloo_filepond_upload'       => [
					'name'         => 'gloo_filepond_upload',
					'label'        => __( 'Better Gallery Form Field', 'gloo_for_elementor' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'condition'    => [
						'field_type' => 'upload',
					],
					'default' => 'no',
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
				'gloo_filepond_label'       => [
					'name'         => 'gloo_filepond_label',
					'label'        => __( 'Better Gallery Label', 'gloo_for_elementor' ),
					'label_block'  => true,
					'description' => 'Default will be  "Drag & Drop your files or <span class="filepond--label-action"> Browse </span>"',
					'type'         => \Elementor\Controls_Manager::TEXT,
					'condition'    => [
						'field_type' => 'upload',
						'gloo_filepond_upload' => 'yes'
					],
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
			];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}

	
	
	public function inject_field_controls( $array, $controls_to_inject ) {
		$keys      = array_keys( $array );
		$key_index = array_search( 'required', $keys ) + 1;

		return array_merge( array_slice( $array, 0, $key_index, true ),
			$controls_to_inject,
			array_slice( $array, $key_index, null, true )
		);
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
