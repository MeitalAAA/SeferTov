<?php
namespace Gloo\Modules\Dynamic_Visibility_Wishlist;
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'dynamic-visibility-wishlist';

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
		// add_action( 'jet-engine/modules/dynamic-visibility/conditions/register', array( $this, 'register_conditions' ) );
		add_filter('wishlistmember_process_protection', array( $this, 'gloo_wishlistmember_process_protection' ));
	}

	public function register_dynamic_tags( $dynamic_tags ) {

		// Include the Dynamic tags
		foreach ( glob( gloo()->modules_path( 'dynamic-visibility-wishlist/inc/dynamic-tags/*.php' ) ) as $file ) {
			require $file;
		}

		$classes = [
			'User_level_Tag',
			'Post_Level_Tag',
			'Common_Level_Tag'
		];

		// register tags
		foreach ( $classes as $class ) {
			$dynamic_tags->register_tag( "Gloo\Modules\Dynamic_Visibility_Wishlist\\{$class}" );
		}


		// // Include the Dynamic tag class file
		// include_once( gloo()->modules_path( 'dynamic-visibility-wishlist/inc/wishlist-tag.php' ) );

		// // Finally register the tag
		// $dynamic_tags->register_tag( 'Gloo\Modules\Dynamic_Visibility_Wishlist\Wishlist_Tag' );

	}

	// public function register_conditions( $conditions_manager ) {

	// 	include_once( gloo()->modules_path( 'dynamic-visibility-wishlist/inc/wishlist-condition.php' ) );
		
	// 	$class =  'User_Wishlist_Course';
	// 	$class = "Gloo\Modules\Dynamic_Visibility_Wishlist\\{$class}";
	// 	$conditions_manager->register_condition( new $class );
	// }

	public function gloo_wishlistmember_process_protection($redirect) {
		return 'STOP';
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
