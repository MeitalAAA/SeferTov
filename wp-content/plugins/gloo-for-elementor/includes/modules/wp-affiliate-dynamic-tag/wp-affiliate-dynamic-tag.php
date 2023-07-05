<?php
/**
 * Wp Affiliate Dynamic Tag module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Affiliate_Dynamic_Tags' ) ) {

	/**
	 * Define Gloo_Module_Affiliate_Dynamic_Tags class
	 */
	class Gloo_Module_Affiliate_Dynamic_Tags extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_affiliate_dynamic_tags';
		}

		public function module_dependencies() {
		
			// return [
			// 	'WooCommerce' => 'woocommerce/woocommerce.php'
			// ];

			// or return boolean value
			// in this case checking for Buddypress and JetSmartFilters
			return [
       			'affiliate_wp' => function_exists( 'affiliate_wp' ),
				'MembershipGlooKit' => $this->is_parent_module_active(),
			];

		}
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'WP Affiliate Dynamic Tag', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'wp-affiliate-dynamic-tag/inc/module.php' );
			$this->instance = \Gloo\Modules\Affiliate_Dynamic_Tags\Module::instance();
		}

	}

}
