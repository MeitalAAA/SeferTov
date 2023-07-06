<?php
/**
 * JetEngine Dynamic Visibility: BuddyPress Addon module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_JEDV_BP' ) ) {

	/**
	 * Define Gloo_Module_JEDV_BP class
	 */
	class Gloo_Module_JEDV_BP extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'jedv_bp';
		}


		public function module_dependencies() {
			return [
        		'Buddypress'      => function_exists( 'buddypress' ),
						'BuddyBossGlooKit' => $this->is_parent_module_active(),
			];
		}
		
		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'JetEngine Dynamic Visibility: BuddyPress Addon', 'gloo_for_elementor' );
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
			require gloo()->modules_path( 'jedv-bp/inc/module.php' );
			$this->instance = \Gloo\Modules\JEDV_BP\Module::instance();
		}

	}

}
