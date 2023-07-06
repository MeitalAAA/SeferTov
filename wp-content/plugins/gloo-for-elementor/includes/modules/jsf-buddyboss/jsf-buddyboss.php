<?php
/**
 * JetSmartFilters: BuddyBoss Addon module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_JSF_Buddyboss' ) ) {

	/**
	 * Define Gloo_Module_JSF_Buddyboss class
	 */
	class Gloo_Module_JSF_Buddyboss extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'jsf_buddyboss';
		}

		public function module_dependencies() {
			// array of label => plugin file path

			// return [
			// 	'WooCommerce' => 'woocommerce/woocommerce.php'
			// ];

			// or return boolean value
			// in this case checking for Buddypress and JetSmartFilters
			return [
				'BuddyPress'      => function_exists( 'bp_is_active' ),
				'JetSmartFilters' => function_exists( 'jet_smart_filters' ),
				'BuddyBossGlooKit' => $this->is_parent_module_active(),
			];

		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'JetSmartFilters: BuddyBoss Addon', 'gloo_for_elementor' );
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
			if (in_array('buddyboss_gloo_kit', $active_modules))
				$output = true;
			return $output;
		}
		
		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance() {
			require gloo()->modules_path( 'jsf-buddyboss/inc/module.php' );
			$this->instance = \Gloo\Modules\JSF_Buddyboss\Module::instance();
		}

	}

}
