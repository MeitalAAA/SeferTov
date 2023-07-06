<?php

namespace Gloo\Modules\Jet_Relation_Dynamic_Tags;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'jet_relation_dynamic_tag';

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
		foreach ( glob( gloo()->modules_path( 'jet-relation-dynamic-tag/inc/dynamic-tags/*.php' ) ) as $file ) {
			require $file;
		}	

		$classes = [
			'Jet_Relation_Tag',
		];

		// register tags
		foreach ( $classes as $class ) {
			$dynamic_tags->register_tag( "Gloo\Modules\Jet_Relation_Dynamic_Tags\\{$class}" );
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
