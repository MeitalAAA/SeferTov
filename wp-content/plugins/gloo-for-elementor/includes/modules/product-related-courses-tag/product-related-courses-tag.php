<?php
/**
 * Product related courses module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Product_Related_Courses' ) ) {

	/**
	 * Define Gloo_Module_Product_Related_Courses class
	 */
	class Gloo_Module_Product_Related_Courses extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'product_related_courses';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Product Related Courses Tag', 'gloo_for_elementor' );
		}

		public function module_dependencies() {
			// array of label => plugin file path

			// return [
			// 	'WooCommerce' => 'woocommerce/woocommerce.php'
			// ];

			// or return boolean value
			// in this case checking for Buddypress and JetSmartFilters
			return [
        // 'Buddypress'      => function_exists( 'buddypress' ),
				'BuddyBossGlooKit' => $this->is_parent_module_active(),
			];

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
			if (in_array('buddyboss_gloo_kit', $active_modules) || in_array('gloo_learndash', $active_modules))
				$output = true;
			return $output;
		}

		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'product-related-courses-tag/inc/module.php' );
			$this->instance = \Gloo\Modules\Product_Related_Courses_Tag\Module::instance();
		}

	}

}
