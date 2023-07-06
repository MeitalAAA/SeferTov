<?php

namespace Gloo\Modules\BB_Dynamic_Tags;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'bb_dynamic_tags';

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
		foreach ( glob( gloo()->modules_path( 'buddyboss-dynamic-tags/inc/dynamic-tags/*.php' ) ) as $file ) {
			require $file;
		}

		$classes = [
			'BuddyBoss_Username',
			'BuddyBoss_User_Type',
			'BuddyBoss_User_Fields',
			'BuddyBoss_Is_User_Friend',
			'BuddyBoss_Profile_Completion',
			'BuddyBoss_User_Pictures',
			'BuddyBoss_Block_URL',
			'BuddyBoss_Block_Text',
			'BuddyBoss_Message_URL',
			'BuddyBoss_Friend_Req_URL',
			'BuddyBoss_Visitor_Datetime',
		];

		// register tags
		foreach ( $classes as $class ) {
			$dynamic_tags->register_tag( "Gloo\Modules\BB_Dynamic_Tags\\{$class}" );
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
