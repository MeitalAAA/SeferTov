<?php

namespace Gloo\Modules\Form_Fields_For_Datepicker;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo_form_fields_for_datepicker';

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
		add_filter( 'elementor_pro/forms/render/item', [ $this, 'render_field_group_class' ], 10, 3 );


	}

	public function init_fields() {

		require gloo()->modules_path( 'form-fields-for-datepicker/inc/fields/datepicker-field.php' );

		$form_field = new Fields\Datepicker_Field;

		// Register the action with form widget
		\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( $form_field->get_name(), $form_field );

	}

	public function render_field_group_class( $item, $item_index, $element ) {
		$settings = $element->get_settings_for_display( 'form_fields' );

		if($settings[ $item_index ]['gloo_datepicker_inline'] && $settings[ $item_index ]['gloo_datepicker_inline'] == 'yes') {
			$element->add_render_attribute( 'field-group' . $item_index, 'class', 'gloo-datepicker-inline' );
		}

		return $item;
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
