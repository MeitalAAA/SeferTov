<?php

namespace Gloo\Modules\Zapier_Connector;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'zapier_connector';

	/**
	 * @var Conditions\Manager
	 */
	public $conditions = null;

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

		add_filter( 'gloo/modules/interactor/enable_variables', '__return_true' );
		require gloo()->modules_path( 'zapier-connector/inc/settings.php' );
		new Settings();

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
