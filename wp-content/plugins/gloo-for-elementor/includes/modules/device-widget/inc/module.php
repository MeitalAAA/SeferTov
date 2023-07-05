<?php
namespace Gloo\Modules\Device_Widget;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo-device-widget';

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
		add_action( 'elementor/widgets/widgets_registered', [$this, 'register_device_widget']);
	//	add_filter('upload_mimes', [$this, 'device_svg_support']);

	}


	public function register_device_widget() {

		include_once( gloo()->modules_path( 'device-widget/inc/widget-device.php' ) );
			
		$widget_object = new Widget_Device();		
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type($widget_object);

	}

	public function device_svg_support($mimes) {
		
		$mimes['svg'] = 'image/svg+xml';
		return $mimes;
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
