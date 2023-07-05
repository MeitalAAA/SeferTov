<?php

namespace Gloo\Modules\Form_Country_Dial_Code;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $prefix = 'gloo_cdc_';
	public $depended_styles = [ 'gloo-for-elementor','gloo_intlTelInput_css'];
	public $depended_scripts = [ 'gloo_intlTelInput_js','gloo_country_code'];

	/**
	 * Constructor for the class
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array($this, 'wp_admin_style_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

		$this->init();
	}

	/******************************************/
	/***** add javascript and css to wp-admin dashboard. **********/
	/******************************************/
	public function wp_admin_style_scripts() {
		wp_register_script( 'gloo_intlTelInput_js', gloo()->plugin_url( 'includes/modules/form-country-dial-code/assets/js/intlTelInput.js'), array('jquery'), '1.0');
		wp_register_script( 'gloo_country_code_custom', gloo()->plugin_url( 'includes/modules/form-country-dial-code/assets/js/gloo-country-dial-code.js'), array('jquery', 'gloo_intlTelInput_js'), '1.0');

		wp_register_style( 'gloo_intlTelInput_css', gloo()->plugin_url( 'includes/modules/form-country-dial-code/assets/css/intlTelInput.css'));
		
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
			'add_country_dial_code_option_for_phone'
		], 20 );
		add_action( 'elementor/element/form/section_form_style/after_section_end', [$this,'add_control_section_country_code'], 10, 2 );

		add_filter( 'elementor_pro/forms/render/item/tel', [ $this, 'add_country_code_render_attr' ], 10, 3 );
		
	}
	 
	public function get_intlTelInput_settings($item, $item_index, $form) {
		$settings = $form->get_settings_for_display( 'form_fields' );
		$setting_array = array();
 
		/* global settings */
		$setting_array['nationalMode'] = false;
				
		if( !empty($settings[ $item_index ][$this->prefix.'initial_country']) ) {
			$setting_array['initialCountry'] = $settings[ $item_index ][$this->prefix.'initial_country'];
		} 
 
		if( !empty($settings[ $item_index ][$this->prefix.'national_mode']) && $settings[ $item_index ][$this->prefix.'national_mode'] == 'yes' ) {
			$setting_array['nationalMode'] = true;
		} 

		if( !empty($settings[ $item_index ][$this->prefix.'onlycountries']) ) {
			$onlycountries = explode(',', $settings[ $item_index ][$this->prefix.'onlycountries']);
			$setting_array['onlyCountries'] = $onlycountries;
		} 

		if( !empty($settings[ $item_index ][$this->prefix.'preferred_countries']) ) {
			$preferred_countries = explode(',', $settings[ $item_index ][$this->prefix.'preferred_countries']);
			$setting_array['preferredCountries'] = $preferred_countries;
		} 

		if( !empty($settings[ $item_index ][$this->prefix.'exclude_countries']) ) {
			$exclude_countries = explode(',', $settings[ $item_index ][$this->prefix.'exclude_countries']);
			$setting_array['excludeCountries'] = $exclude_countries;
		} 
						
		return $setting_array;
	}


	public function add_country_code_render_attr( $item, $item_index, $form ) {
		// echo '<pre>'; print_r($item); echo '</pre>'; 

		if ( (isset( $item[$this->prefix.'enable'] ) && $item[$this->prefix.'enable'] == 'yes')) {
			
			foreach ( $this->depended_scripts as $script ) {
				$form->add_script_depends( 'gloo_country_code_custom' );
			}

			foreach ( $this->depended_styles as $style ) {
				$form->add_style_depends( $style );
			}

			$field_settings = $this->get_intlTelInput_settings($item, $item_index, $form);

			if(!empty($field_settings)) {
				$form->add_render_attribute( 'field-group' . $item_index, 'data-config', wp_json_encode( $field_settings ) );
			}
			
			$form->add_render_attribute( 'input' . $item_index, 'class', 'gloo-intlTelInput' );
			$form->add_render_attribute( 'field-group' . $item_index, 'data-filepond-url', home_url() );

		}

		return $item;
	}

	
	public function add_control_section_country_code( $element, $args ) {

		$element->start_controls_section(
			$this->prefix.'country_code_field',
			[
				'label' => __( 'Country Code Field', 'gloo' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$element->add_control(
			$this->prefix.'color',
			[
				'label'     => __( 'Drop Arrow Background Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#DDD',
 				'selectors' => [
					'.iti--allow-dropdown .iti__flag-container .iti__selected-flag' => 'background-color: {{VALUE}};',
					'.iti--allow-dropdown .iti__flag-container:hover .iti__selected-flag' => 'background-color: {{VALUE}} !important;',
 				],
			]
		);
		$element->add_control(
			$this->prefix.'arrow_color',
			[
				'label'     => __( 'Drop Arrow Color', 'gloo' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default' => '#000',
 				'selectors' => [
					'.iti--allow-dropdown .iti__arrow' => 'border-top-color: {{VALUE}};',
 				],
			]
		);
 
		$element->end_controls_section();
	}
	
	public function add_country_dial_code_option_for_phone( $widget ) {
		$elementor = \Elementor\Plugin::instance();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );
 
		if ( is_wp_error( $control_data ) ) {
			return;
		}
 
		$field_controls =
			[
				$this->prefix.'enable'       => [
					'name'         => $this->prefix.'enable',
					'label'        => __( 'Country Dial Code Field', 'gloo_for_elementor' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'condition'    => [
						'field_type' => 'tel',
					],
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
				$this->prefix.'initial_country'      => [
					'name'         => $this->prefix.'initial_country',
					'label'        => __( 'Default Country', 'gloo_for_elementor' ),
					'type'         => \Elementor\Controls_Manager::TEXT,
					'condition'    => [
						'field_type' => 'tel',
						$this->prefix.'enable' => 'yes'
					],
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
 				$this->prefix.'national_mode'      => [
					'name'         => $this->prefix.'national_mode',
					'label'        => __( 'National Mode', 'gloo_for_elementor' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'description' => 'don\'t insert international dial codes',
					'condition'    => [
						'field_type' => 'tel',
						$this->prefix.'enable' => 'yes'
					],
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
				$this->prefix.'onlycountries'      => [
					'name'         => $this->prefix.'onlycountries',
					'label'        => __( 'Include Countries', 'gloo_for_elementor' ),
					'type'         => \Elementor\Controls_Manager::TEXT,
					'description' => 'comma separated values e.g us,gb',
					'condition'    => [
						'field_type' => 'tel',
						$this->prefix.'enable' => 'yes'
					],
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
				$this->prefix.'preferred_countries'      => [
					'name'         => $this->prefix.'preferred_countries',
					'label'        => __( 'Preferred Countries', 'gloo_for_elementor' ),
					'type'         => \Elementor\Controls_Manager::TEXT,
					'description' => 'The countries that will appear at the top of the list. <br>comma separated values e.g us,gb',
					'condition'    => [
						'field_type' => 'tel',
						$this->prefix.'enable' => 'yes'
					],
					'tab'          => 'content',
					'inner_tab'    => 'form_fields_content_tab',
					'tabs_wrapper' => 'form_fields_tabs',
				],
				$this->prefix.'exclude_countries'      => [
					'name'         => $this->prefix.'exclude_countries',
					'label'        => __( 'Exclude Countries', 'gloo_for_elementor' ),
					'type'         => \Elementor\Controls_Manager::TEXT,
					'description' => 'comma separated values e.g us,gb',
					'condition'    => [
						'field_type' => 'tel',
						$this->prefix.'enable' => 'yes'
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
