<?php

namespace Gloo\Modules\Form_User_Submission;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo_form_user_submission';

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
		add_action( 'elementor_pro/init', [ $this, 'register_form_action' ] );

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

		if(method_exists($this, 'inject_field_controls'))
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

	public function register_form_action() {

		// Include the form actions
		foreach ( glob( gloo()->modules_path( 'frontend-user-submission/inc/form-action/*.php' ) ) as $file ) {
			require $file;
		}

		$classes = [
			'Frontend_User_Registration',
		];

		// register tags
		foreach ( $classes as $class ) {

			$class       = "Gloo\Modules\Form_User_Submission\\{$class}";
			$form_action = new $class;

			// Register the action with form widget
			\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $form_action->get_name(), $form_action );
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