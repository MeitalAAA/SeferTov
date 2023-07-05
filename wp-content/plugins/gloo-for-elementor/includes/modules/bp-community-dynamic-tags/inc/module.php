<?php
namespace Gloo\Modules\Community_Dynamic_Tags;

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
		foreach ( glob( gloo()->modules_path( 'bp-community-dynamic-tags/inc/dynamic-tags/*.php' ) ) as $file ) {
			require $file;
		}	

		$classes = [
			'Friends_Tag',
			'Newest_Memebers_Tag',
			'Online_Users_Tag'
		];

		// register tags
		foreach ( $classes as $class ) {
			$dynamic_tags->register_tag( "Gloo\Modules\Community_Dynamic_Tags\\{$class}" );
		}

	}

    public function add_macros( $macros_list ){
		$macros_list['my_friends']              = [ $this, 'macro_my_friends' ];
		$macros_list['now_online']              = [ $this, 'macro_now_online' ];
		$macros_list['newest_members']          = [ $this, 'macro_newest_members' ];
		
        return $macros_list;
    }

	public function macro_my_friends() {
		if ( ! function_exists( 'friends_get_friend_user_ids' ) ) {
			return PHP_INT_MAX;
		}
		$user_id = get_current_user_id();
		if ( ! $user_id ) {
			return PHP_INT_MAX;
		}
		$ids = friends_get_friend_user_ids( $user_id );

		if ( ! $ids ) {
			return PHP_INT_MAX;
		}

		return implode( ",", $ids );
	}

	public function macro_now_online() {
		$args = [
			'per_page' => 0, // get all
			'type'     => 'online',
			'exclude'  => get_current_user_id(),
		];

		$user_query = new \BP_User_Query( $args );
		if ( ! $user_query->results ) {
			// no results
			return PHP_INT_MAX;
		}
		$user_ids = $user_query->user_ids;

		if ( ! $user_ids ) {
			// no user ids
			return PHP_INT_MAX;
		}

		// double check
		$key = array_search( get_current_user_id(), $user_ids );
		if ( $key !== false ) {
			unset( $user_ids[ $key ] );
			if ( ! $user_ids ) {
				// no user ids
				return PHP_INT_MAX;
			}
		}

		return implode( ",", $user_ids );
	}

	public function macro_newest_members() {
		$args = [
			'per_page' => 0, // get all
			'type'     => 'newest',
			'exclude'  => get_current_user_id(),
		];

		$user_query = new \BP_User_Query( $args );
		if ( ! $user_query->results ) {
			// no results
			return PHP_INT_MAX;
		}
		$user_ids = $user_query->user_ids;
		if ( ! $user_ids ) {
			// no user ids
			return PHP_INT_MAX;
		}
		// double check
		$key = array_search( get_current_user_id(), $user_ids );
		if ( $key !== false ) {
			unset( $user_ids[ $key ] );
			if ( ! $user_ids ) {
				// no user ids
				return PHP_INT_MAX;
			}
		}

		return implode( ",", $user_ids );
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
