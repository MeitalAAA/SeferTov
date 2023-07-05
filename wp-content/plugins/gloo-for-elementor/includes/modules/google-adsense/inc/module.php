<?php

namespace Gloo\Modules\Google_Adsense_Widget;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo-google-adsense';

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
		add_action( 'elementor/widgets/widgets_registered', [$this, 'register_google_adsense_widget']);

		include_once( gloo()->modules_path( 'google-adsense/inc/settings.php' ) );
		new Settings();
	}

	public function register_google_adsense_widget() {

		include_once( gloo()->modules_path( 'google-adsense/inc/widget-google-adsense.php' ) );
			
		$widget_object = new Google_Adsense();
		
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type($widget_object);

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
