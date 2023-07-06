<?php
namespace Gloo\Modules\Multi_Currency_Tag;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'multi_currency_dynamic_tags';

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

		// Include the Dynamic tag class file
		include_once( gloo()->modules_path( 'multi-currency-dynamic-tag/inc/multi-currecny-tag.php' ) );

		// Finally register the tag
		$dynamic_tags->register_tag( 'Gloo\Modules\Multi_Currency_Tag\Currency_Tag' );
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
