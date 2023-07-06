<?php
namespace Gloo\Modules\Query_Control;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'query-control';

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
		
		require gloo()->modules_path( 'query-control/inc/query-module.php' );
 
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
