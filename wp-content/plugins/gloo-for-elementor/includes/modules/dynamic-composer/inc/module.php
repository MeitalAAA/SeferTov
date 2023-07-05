<?php

namespace Gloo\Modules\Dynamic_Composer;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo-dynamic-composer';

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

		// include the generic functions file.
		// include_once plugin_dir_path(OTW_DYNAMICS_COMPOSER_PLUGIN_FILE).'inc/functions.php';
		// include_once plugin_dir_path(OTW_DYNAMICS_COMPOSER_PLUGIN_FILE).'inc/autoload.php';

		require gloo()->modules_path( 'dynamic-composer/inc/functions.php' );
		require gloo()->modules_path( 'dynamic-composer/inc/autoload.php' );
		return \OTW\DynamicComposer\DynamicComposer::instance();

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
