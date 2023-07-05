<?php
namespace Gloo\Modules\Dokan_Dynamic_Tags;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'dokan-dynamic-tag';

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
		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_dynamic_tags' ) );
	}


	public function register_dynamic_tags( $dynamic_tags ) {

			// Include the Dynamic tags
			foreach ( glob( gloo()->modules_path( 'dokan-dynamic-tags/inc/dynamic-tags/*.php' ) ) as $file ) {
				require $file;
			}	
	
			$classes = [
				'Dokan_Store_Tag',
				'Dokan_User_Url_Tag',
				'Dokan_Store_Vendor_ID'
			];
	
			// register tags
			foreach ( $classes as $class ) {
				$dynamic_tags->register_tag( "Gloo\Modules\Dokan_Dynamic_Tags\\{$class}" );
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
