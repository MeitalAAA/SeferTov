<?php
/**
 * Listing Grid Shortcode
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Listing_Grid_Shortcode_Maker' ) ) {

	/**
	 * Define Gloo_Module_Listing_Grid_Shortcode class
	 */
	class Gloo_Module_Listing_Grid_Shortcode_Maker extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'gloo_listing_grid_shortcode_maker';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Listing Grid Shortcode Maker', 'gloo_for_elementor' );
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
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'listing-grid-shortcode-maker/inc/module.php' );
			$this->instance = \Gloo\Modules\Listing_Grid_Shortcode_Maker\Module::instance();
		}

	}

}
