<?php

namespace Gloo\Modules\Form_Field_Validation;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'form_field_validation';

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
 
		add_action( 'elementor_pro/init', [ $this, 'elementor_pro_init' ] );

		add_action( 'elementor/element/form/section_form_fields/before_section_end', [
			$this,
			'add_regex_validation_controls'
		], 11 );
 	}
  

	public function add_regex_validation_controls( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

//		echo '<pre>'; print_r($control_data); echo '</pre>';

		if ( is_wp_error( $control_data ) ) {
			return;
		}
 
		$field_controls = [
			'gloo_regex_expression'       => [
				'name'         => 'gloo_regex_expression',
				'label'        => __( 'RegEx ', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_advanced_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'gloo_field_error_label'             => [
				'name'         => 'gloo_field_error_label',
				'label'        => __( 'Error Label', 'gloo' ),
				'type'         => \Elementor\Controls_Manager::TEXT,
				'tab'          => 'content',
				'inner_tab'    => 'form_fields_advanced_tab',
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
     
	public function elementor_pro_init() {
		add_action('elementor_pro/forms/validation', [$this, 'elementor_pro_forms_validation'], 999, 2);
	}
	
	public function elementor_pro_forms_validation($record, $ajax_handler) {
		
		$form_settings = $record->get( 'form_settings' );
		$raw_fields = $record->get( 'fields' );
		
		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		if(isset($form_settings['form_fields'])) {
			foreach( $form_settings['form_fields'] as $field_setting ) {
				
				if(isset($field_setting['gloo_regex_expression']) && !empty($field_setting['gloo_regex_expression'])) {

					$field_custom_id = $field_setting['custom_id'];
					$field_value = $fields[$field_custom_id];

					$regex_expression = $field_setting['gloo_regex_expression'];
					$field_error_label = $field_setting['gloo_field_error_label'];

					if ( preg_match( $regex_expression, $field_value ) !== 1 ) {
						$ajax_handler->add_error( $field['id'], $field_error_label );
					}
				}	
			}
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