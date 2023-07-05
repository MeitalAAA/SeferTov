<?php

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gloo_Module_Image_Crop' ) ) {

	/**
	 * Define Gloo_Module_Image_Crop class
	 */
	class Gloo_Module_Image_Crop extends Gloo_Module_Base {

		public $instance = null;

		/**
		 * Module ID
		 *
		 * @return string
		 */
		public function module_id() {
			return 'image_crop';
		}

		public function module_dependencies() {
			// array of label => plugin file path
			// or return boolean value
			return [
				'ElementorPro'      => defined( 'ELEMENTOR_PRO_VERSION' ),
				'GlooFormsExtensions'      => $this->is_parent_module_active(),
			];
		}

		/**
		 * Module name
		 *
		 * @return string
		 */
		public function module_name() {
			return __( 'Image Crop', 'gloo' );
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
			if (in_array('gloo_zoho', $active_modules) || in_array('little_engine', $active_modules))
				$output = true;
			return $output;
		}
		
		/**
		 * Create module instance
		 *
		 * @return [type] [description]
		 */
		public function create_instance(  ) {
			require  gloo()->modules_path( 'image-crop/inc/module.php' );
			$this->instance = \Gloo\Modules\ImageCrop\Module::instance();
		}

	}

}






//define('OTW_WOOCOMMERCE_PRICE_WIDGET_FILE', __FILE__);


//include_once plugin_dir_path(OTW_WOOCOMMERCE_PRICE_WIDGET_FILE).'inc/autoload.php';

// add the data sanitization and validation class
//if(!class_exists('BBWPSanitization'))
//  include_once BBWP_FLUID_DYNAMICS_ABS.'inc/classes/BBWPSanitization.php';


