<?php
namespace Gloo\Modules\Activities_Dynamic_Tags;

class Module {

	/**
	 * A reference to an instance of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Module
	 */
	private static $instance = null;

	public $slug = 'community-dynamic-tag';

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
		add_filter( 'jet-engine/listings/macros-list', [$this, 'add_macros'], 10 , 1);
	}


	public function register_dynamic_tags( $dynamic_tags ) {

			// Include the Dynamic tags
			foreach ( glob( gloo()->modules_path( 'bp-activities-dynamic-tags/inc/dynamic-tags/*.php' ) ) as $file ) {
				require $file;
			}	
	
			$classes = [
				'Activities_Tag',
			];
	
			// register tags
			foreach ( $classes as $class ) {
				$dynamic_tags->register_tag( "Gloo\Modules\Activities_Dynamic_Tags\\{$class}" );
			}

	}

	public function add_macros( $macros_list ){
		$macros_list['liked_activities']        = [ $this, 'macro_liked_activities' ];

        return $macros_list;
    }

	public function macro_liked_activities() {
		if ( ! function_exists( 'bp_get_user_meta' ) ) {
			return PHP_INT_MAX;
		}
		$user_id                   = get_current_user_id();
		$favorite_activity_entries = bp_get_user_meta( $user_id, 'bp_favorite_activities', true );
		if ( ! $favorite_activity_entries ) {
			return PHP_INT_MAX;
		}

		return implode( ",", $favorite_activity_entries );
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
