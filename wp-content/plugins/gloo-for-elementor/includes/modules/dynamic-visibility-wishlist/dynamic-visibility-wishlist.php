<?php
/**
 * Dynamic Visibility Wishlist
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Dynamic_Visibility_Wishlist' ) ) {

	/**
	 * Define Gloo_Module_Dynamic_Visibility_Wishlist class
	 */
	class Gloo_Module_Dynamic_Visibility_Wishlist extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'dynamic_visibility_wishlist';
		}

		public function module_dependencies() {
			return [
        		'WishListMember' => function_exists( 'wishlistmember_instance' ),
				'MembershipGlooKit' => $this->is_parent_module_active(),
			];

		}
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Dynamic Visibility Wishlist', 'gloo_for_elementor' );
		}

		/**
		 * Module init
		 *
		 * @return void
		 */
		public function module_init() {
			add_action( 'gloo/init', array( $this, 'create_instance' ) );
		}

		/**
		 * is_parent_module_active
		 *
		 * @return true/false
		 */
		public function is_parent_module_active() {
			$output = false;
			$active_modules = $this->get_active_modules_from_db();
			if (in_array('gloo_learndash', $active_modules))
				$output = true;
			return $output;
		}

		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'dynamic-visibility-wishlist/inc/module.php' );
			$this->instance = \Gloo\Modules\Dynamic_Visibility_Wishlist\Module::instance();
		}

	}

}
