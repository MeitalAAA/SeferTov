<?php

namespace Gloo\Modules\Form_Fields_For_Users;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo_form_fields_for_users';

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

		require gloo()->modules_path( 'form-fields-for-users/inc/fields/user-field.php' );
		$form_field = new Fields\Users_Field;

		// Register the action with form widget
		\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_field_type( $form_field->get_name(), $form_field );

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
