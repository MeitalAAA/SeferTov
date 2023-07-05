<?php

namespace Gloo\Modules\Repeater_Dynamic_Tag;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'repeater_dynamic_tag';

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
		include_once( gloo()->modules_path( 'repeater-dynamic-tag/inc/dynamic-tag.php' ) );
		include_once( gloo()->modules_path( 'repeater-dynamic-tag/inc/dynamic-tag-image.php' ) );
		include_once( gloo()->modules_path( 'repeater-dynamic-tag/inc/dynamic-tag-gallery.php' ) );

		// Finally register the tag
		$dynamic_tags->register_tag( 'Gloo\Modules\Repeater_Dynamic_Tag\Repeater_Dynamic_Tag' );
		$dynamic_tags->register_tag( 'Gloo\Modules\Repeater_Dynamic_Tag\Repeater_Dynamic_Tag_Image' );
		$dynamic_tags->register_tag( 'Gloo\Modules\Repeater_Dynamic_Tag\Repeater_Dynamic_Tag_Gallery' );

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
