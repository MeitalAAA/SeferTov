<?php

namespace Gloo\Modules\BB_Group_Dynamic_Tags;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'bb_group_dynamic_tags';

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
		foreach ( glob( gloo()->modules_path( 'buddyboss-group-dynamic-tags/inc/dynamic-tags/*.php' ) ) as $file ) {
			require $file;
		}

		$classes = [
			'BuddyBoss_Group_User_Role',
			'BuddyBoss_Group_Name',
			'BuddyBoss_Group_Fields',
			'BuddyBos_Group_Settings',
			'BuddyBoss_Group_Join_URL',
			'BuddyBoss_Group_Leave_URL'
		];

		// register tags
		foreach ( $classes as $class ) {
			$dynamic_tags->register_tag( "Gloo\Modules\BB_Group_Dynamic_Tags\\{$class}" );
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
