<?php
namespace Gloo\Modules\Data_Source;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'gloo-dynamic-tag-maker';

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
		include( gloo()->modules_path( 'data-source/inc/tag-maker.php' ) );
		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'register_dynamic_tags' ) );

	}
	
	public function register_dynamic_tags( $dynamic_tags ) {

		include_once( gloo()->modules_path( 'data-source/inc/dynamic-tag/google-spreadsheet.php' ) );
		// Finally register the tag
		$dynamic_tags->register_tag( "Gloo\Modules\Data_Source\Tag_Maker" );
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
