<?php

namespace Gloo\Modules\Form_Comment_Submission;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo_form_comment_submission';

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
	}
 
	public function register_form_action() {

		// Include the form actions
		foreach ( glob( gloo()->modules_path( 'frontend-comment-submission/inc/form-action/*.php' ) ) as $file ) {
			require $file;
		}

		$classes = [
			'Frontend_Comment_Submission',
		];

		// register tags
		foreach ( $classes as $class ) {

			$class       = "Gloo\Modules\Form_Comment_Submission\\{$class}";
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