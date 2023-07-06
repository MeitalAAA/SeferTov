<?php
/**
 * Taxonomy Terms module
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Module_Taxonomy_Terms_Dynamic_Tags' ) ) {

	/**
	 * Define Gloo_Module_Taxonomy_Terms_Dynamic_Tags class
	 */
	class Gloo_Module_Taxonomy_Terms_Dynamic_Tags extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'taxonomy_terms_dynamic_tags';
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Taxonomy Terms Dynamic Tags', 'gloo_for_elementor' );
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
			require  gloo()->modules_path( 'taxonomy-terms-dynamic-tags/inc/module.php' );
			$this->instance = \Gloo\Modules\Taxonomy_Terms_Dynamic_Tags\Module::instance();
		}

	}

}
