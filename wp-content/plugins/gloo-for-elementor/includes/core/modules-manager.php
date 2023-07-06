<?php
/**
 * Modules manager
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'Gloo_Modules' ) ) {

	/**
	 * Define Gloo_Modules class
	 */
	class Gloo_Modules {

		public $option_name = 'gloo_modules';
		private $modules = array();
		private $active_modules = array();
		private $unsatisfied_dependencies = array();

		/**
		 * Constructor for the class
		 */
		function __construct() {

			$this->preload_modules();
			$this->init_active_modules();
		}


		/**
		 * Activate module
		 *
		 * @return [type] [description]
		 */
		public function activate_module( $module ) {

			$modules = get_option( $this->option_name, array() );

			if ( ! in_array( $module, $modules ) ) {
				$modules[] = $module;
			}

			update_option( $this->option_name, $modules );

		}

		/**
		 * Returns path to file inside modules dir
		 *
		 * @param  [type] $path [description]
		 *
		 * @return [type]       [description]
		 */
		public function modules_path( $path ) {
			return gloo()->modules_path() . $path;
		}

		/**
		 * Preload modules
		 *
		 * @return void
		 */
		public function preload_modules() {

			$base_path   = gloo()->modules_path();
			$all_modules = apply_filters( 'gloo/available-modules', array(
				'Gloo_Module_Interactor'                         => $base_path . 'interactor/interactor.php',
				'Gloo_Module_Dynamic_Attributes'                 => $base_path . 'dynamic-attributes/dynamic-attributes.php',
				'Gloo_Module_Dynamic_Tags_Everywhere'            => $base_path . 'dynamic-tags-everywhere/dynamic-tags-everywhere.php',
				'Gloo_Module_PHP_Responsive'                     => $base_path . 'php-responsive/php-responsive.php',
				'Gloo_Module_BB_Dynamic_Tags'                    => $base_path . 'buddyboss-dynamic-tags/buddyboss-dynamic-tags.php',
				'Gloo_Module_Taxonomy_Terms_Dynamic_Tags'        => $base_path . 'taxonomy-terms-dynamic-tags/taxonomy-terms-dynamic-tags.php',
				'Gloo_Module_Relationship_Dynamic_Tags'          => $base_path . 'relationship-dynamic-tags/relationship-dynamic-tags.php',
				'Gloo_Module_Acf_Dynamic_Tags'                   => $base_path . 'acf-dynamic-tags/acf-dynamic-tags.php',
				//'Gloo_Module_Content_Trimmer'                => $base_path . 'content-trimmer/content-trimmer.php',
				'Gloo_Module_Time_Span_Dynamic_Tags'             => $base_path . 'time-span-dynamic-tags/time-span-dynamic-tags.php',
				'Gloo_Module_Listing_Grid_Shortcode_Maker'       => $base_path . 'listing-grid-shortcode-maker/listing-grid-shortcode-maker.php',
				'Gloo_Module_Acf_Relation_Field_Macro'           => $base_path . 'acf-relation-field-macro/acf-relation-field-macro.php',
				'Gloo_Module_WooCommerce_Dynamic_Tags_Kit'       => $base_path . 'woocommerce-dynamic-tags-kit/woocommerce-dynamic-tags-kit.php',
				'Gloo_Module_Dynamic_Composer'                   => $base_path . 'dynamic-composer/dynamic-composer.php',
				'Gloo_Module_Autocomplete_Address_Fields'        => $base_path . 'autocomplete-address-fields/autocomplete-address-fields.php',
				'Gloo_Module_Woocommerce_Price_Widget'           => $base_path . 'woocommerce-price-widget/woocommerce-price-widget.php',
				'Gloo_Module_Woocommerce_Products'               => $base_path . 'woocommerce-products/woocommerce-products.php',
				'Gloo_Module_Woocommerce_Swatches'               => $base_path . 'woocommerce-swatches/woocommerce-swatches.php',
				'Gloo_Module_Zoho_Crm_Dynamic_Form_Action'       => $base_path . 'zoho-crm-dynamic-form-action/zoho-crm-dynamic-form-action.php',
				'Gloo_Module_Woocommerce_Product_Discount'       => $base_path . 'woocommerce-discount/woocommerce-discount.php',
				'Gloo_Module_Global_Elementor_Tags'              => $base_path . 'global-elementor-tags/global-elementor-tags.php',
				'Gloo_Module_Data_Source'                        => $base_path . 'data-source/data-source.php',
				'Gloo_Module_WC_Macro_Set'                       => $base_path . 'wc-macro-set/wc-macro-set.php',
				'Gloo_Module_JSF_Buddyboss'                      => $base_path . 'jsf-buddyboss/jsf-buddyboss.php',
				'Gloo_Module_JEDV_LD'                            => $base_path . 'jedv-ld/jedv-ld.php',
				'Gloo_Module_JEDV_BP'                            => $base_path . 'jedv-bp/jedv-bp.php',
				'Gloo_Module_Typography_Plus'                    => $base_path . 'typography-plus/typography-plus.php',
				'Gloo_Module_Clickable'                          => $base_path . 'elementor-clickable/elementor-clickable.php',
				'Gloo_Module_Cart_Values_Dynamic_Tags'           => $base_path . 'cart-values-tag/cart-values-tag.php',
				'Gloo_Module_Keyframes'                          => $base_path . 'elementor-keyframes/elementor-keyframes.php',
				'Gloo_Module_DataLayer_Connector'                => $base_path . 'datalayer-connector/datalayer-connector.php',
				'Gloo_Module_Product_Related_Courses'            => $base_path . 'product-related-courses-tag/product-related-courses-tag.php',
				'Gloo_Module_Zapier_Connector'                   => $base_path . 'zapier-connector/zapier-connector.php',
				'Gloo_Module_Repeater_Dynamic_Tag'               => $base_path . 'repeater-dynamic-tag/repeater-dynamic-tag.php',
				'Gloo_Module_Dynamic_Visibility_Wishlist'        => $base_path . 'dynamic-visibility-wishlist/dynamic-visibility-wishlist.php',
				'Gloo_Module_Device_Widget'                      => $base_path . 'device-widget/device-widget.php',
				'Gloo_Dynamify_Repeaters'                        => $base_path . 'dynamify-repeaters/dynamify-repeaters.php',
				'Gloo_Module_Salesforce_Crm_Dynamic_Form_Action' => $base_path . 'salesforce-crm-dynamic-form-action/salesforce-crm-dynamic-form-action.php',
				'Gloo_Module_Draggable'                          => $base_path . 'elementor-draggable/elementor-draggable.php',
				'JSF_Pagination_Widget'                          => $base_path . 'jsf-pagination-widget/jsf-pagination-widget.php',
				'Gloo_Module_User_Agents_Extension'              => $base_path . 'user-agents-extension/user-agents-extension.php',
				'Gloo_Module_ActiveTrail_Form_Submit_Action'     => $base_path . 'activetrail-form-submit-action/activetrail-form-submit-action.php',
				'Gloo_Module_Woocommerce_Variation_Table'        => $base_path . 'woocommerce-variaton-table/woocommerce-variaton-table.php',
				'Gloo_Module_Custom_Webhook'                     => $base_path . 'custom-webhook-connector/custom-webhook-connector.php',
				// 'Gloo_Module_Gmb_Review'   => $base_path . 'gmb-reviews/gmb-reviews.php',
				// 'Gloo_Module_Facebook_Reviews'   => $base_path . 'facebook-reviews/facebook-reviews.php',
				'Gloo_Module_Native_Dynamic_Tags_Kit'            => $base_path . 'native-dynamic-tags/native-dynamic-tags.php',
				'Gloo_Module_BP_Community_Dynamic_Tags'          => $base_path . 'bp-community-dynamic-tags/bp-community-dynamic-tags.php',
				'Gloo_Module_BP_Activities_Dynamic_Tags'         => $base_path . 'bp-activities-dynamic-tags/bp-activities-dynamic-tags.php',
				'Gloo_Module_BB_Group_Dynamic_Tags'              => $base_path . 'buddyboss-group-dynamic-tags/buddyboss-group-dynamic-tags.php',
				'Gloo_Module_Dokan_Dynamic_Tags'                 => $base_path . 'dokan-dynamic-tags/dokan-dynamic-tags.php',
				'Gloo_Module_Schema_Control'                     => $base_path . 'schema-control/schema-control.php',
				'Gloo_Module_Grain_Control'                       => $base_path . 'grain-control/grain-control.php',
				'Gloo_Module_Google_Adsense'                     => $base_path . 'google-adsense/google-adsense.php',
				'Gloo_Module_Powerlink_Form_Action'              => $base_path . 'powerlink-form-action/powerlink-form-action.php',
				'Gloo_Module_Query_Control'                      => $base_path . 'query-control/query-control.php',
				'Gloo_Module_Dynamic_Nav'                        => $base_path . 'dynamic-nav/dynamic-nav.php',
				'Gloo_Module_Form_Post_Editing'                  => $base_path . 'frontend-post-editing/frontend-post-editing.php',
				'Gloo_Module_Form_User_Editing'                  => $base_path . 'frontend-user-editing/frontend-user-editing.php',
				'Gloo_Module_Form_Post_Submission'               => $base_path . 'frontend-post-submission/frontend-post-submission.php',
				'Gloo_Module_Form_User_Submission'               => $base_path . 'frontend-user-submission/frontend-user-submission.php',
				'Gloo_Module_Form_Fields_For_Terms'              => $base_path . 'form-fields-for-terms/form-fields-for-terms.php',
				'Gloo_Module_Elementor_Select2_Fields'        	 => $base_path . 'elementor-select2-fields/elementor-select2-fields.php',
				'Gloo_Module_Bundle_Maker'        				 => $base_path . 'bundle-maker/bundle-maker.php',
				'Gloo_Module_Form_Fields_For_CPT'        		 => $base_path . 'form-fields-for-cpt/form-fields-for-cpt.php',
				'Gloo_Module_Checkout_Anything'        			 => $base_path . 'checkout-anything/checkout-anything.php',
				'Gloo_Module_Wysiwyg_Field'        	 		     => $base_path . 'wysiwyg-field/wysiwyg-field.php',
				'Gloo_Module_Repeater_Field'        	 		 => $base_path . 'repeater-field/repeater-field.php',
				'Gloo_Module_Form_Comment_Submission'        	 => $base_path . 'frontend-comment-submission/frontend-comment-submission.php',
				'Gloo_Module_Affiliate_Dynamic_Tags'        	 => $base_path . 'wp-affiliate-dynamic-tag/wp-affiliate-dynamic-tag.php',
				'Gloo_Module_Form_Fields_For_Users'        	 	 => $base_path . 'form-fields-for-users/form-fields-for-users.php',
				'Gloo_Module_Form_Fields_Color_Picker'        	 => $base_path . 'form-fields-color-picker/form-fields-color-picker.php',
				'Gloo_Module_Form_OTP'        			 		 => $base_path . 'form-otp/form-otp.php',
				'Gloo_Module_Image_Crop'        			 	 => $base_path . 'image-crop/image-crop.php',
				'Gloo_Form_Field_Validation'        			 => $base_path . 'form-field-validation/form-field-validation.php',
				'Gloo_Module_Form_Fields_For_Datepicker'         => $base_path . 'form-fields-for-datepicker/form-fields-for-datepicker.php',
				'Gloo_Random_String_Dynamic_Tag'         		 => $base_path . 'random-string-dynamic-tag/random-string-dynamic-tag.php',
				'Gloo_Checkbox_Radio_Field_Control'         	 => $base_path . 'checkbox-radio-field-control/checkbox-radio-field-control.php',
				'Gloo_Module_Composer_Field'         	 => $base_path . 'composer-field/composer-field.php',
				'Gloo_Module_Form_Fields_For_Range'         	 => $base_path . 'form-fields-for-range/form-fields-for-range.php',
				'Gloo_Module_Column_Responsive_Order'         	 => $base_path . 'column-responsive-order/column-responsive-order.php',
				'Gloo_Module_Jet_Relation_Dynamic_Tags'         	 => $base_path . 'jet-relation-dynamic-tag/jet-relation-dynamic-tag.php',
				'Gloo_Module_Fluid_Visibility'                   => $base_path . 'fluid-visibility/fluid-visibility.php',
				'Gloo_Module_Form_Filepond_Upload'               => $base_path . 'form-filepond-upload/form-filepond-upload.php',
				'Gloo_Module_Multi_Currency_Tag'                 => $base_path . 'multi-currency-dynamic-tag/multi-currency-dynamic-tag.php',
				'Gloo_Module_Ajax_Reload_Prevention'             => $base_path . 'ajax-reload-prevention/ajax-reload-prevention.php',
				'Gloo_Module_Form_Country_Dial_Code'             => $base_path . 'form-country-dial-code/form-country-dial-code.php',
				'Gloo_Module_Form_Fields_For_Time_Span'          => $base_path . 'form-fields-time-span/form-fields-time-span.php',
				'Gloo_Module_Cookies_Form_Action'                 => $base_path . 'cookies_form_action/cookies_form_action.php',
				'Gloo_Module_Cookies_Dynamic_Tag'                 => $base_path . 'cookies_dynamic_tag/cookies_dynamic_tag.php',
				'Gloo_Module_Interactor_Cookies'                   => $base_path . 'interactor_cookies/interactor_cookies.php',
				'Gloo_Module_Image_Upload_UI'                   => $base_path . 'image-upload-ui/image-upload-ui.php',
				'Gloo_Module_Signature_Field'                   => $base_path . 'signature-field/signature-field.php',
				'Gloo_Module_Form_Actions_Pro'                   => $base_path . 'form-actions-pro/form-actions-pro.php',
				'Gloo_Module_Login_Form_Action'                 => $base_path . 'login_form_action/login_form_action.php',
				'Gloo_External_Module_PDF_Generator'                 => $base_path . 'gloo-pdf-generator/gloo-pdf-generator.php',
				'Gloo_Module_Interactor_Gsap'                   => $base_path . 'interactor_gsap/interactor_gsap.php',
				'Gloo_Module_Learndash_Dynamic_Tags'            => $base_path . 'learndash-dynamic-tags/learndash-dynamic-tags.php',
			) );

			require_once gloo()->plugin_path( 'includes/base/base-module.php' );

			foreach ( $all_modules as $module => $file ) {
				require $file;

				$instance     = new $module;
				$module_id    = $instance->module_id();
				$dependencies = $instance->module_dependencies();

				if ( $dependencies && is_array( $dependencies ) ) {
					foreach ( $dependencies as $label => $value ) {

						if ( is_bool( $value ) ) { // value passed
							if ( ! $value ) { // dependency not satisfied
								$this->unsatisfied_dependencies[ $module_id ][] = $label;
							}
							continue;
						}

						// plugin path passed
						if ( ! function_exists( 'is_plugin_active' ) ) {
							include_once ABSPATH . 'wp-admin/includes/plugin.php';
						}

						if ( ! is_plugin_active( $value ) ) {
							$this->unsatisfied_dependencies[ $module_id ][] = $label;
						}
					}
				}

				if ( $this->get_unsatisfied_dependencies( $module_id ) || ! $dependencies ) {
					// contains unsatisfied dependencies - don't init
					continue;
				}

				$this->modules[ $module_id ] = $instance;
			}

		}

		public function get_unsatisfied_dependencies( $module_id = '' ) {

			if ( $module_id ) {
				return isset( $this->unsatisfied_dependencies[ $module_id ] ) ? $this->unsatisfied_dependencies[ $module_id ] : false;
			}

			return $this->unsatisfied_dependencies;

		}

		/**
		 * Initialize active modulles
		 *
		 * @return void
		 */
		public function init_active_modules() {


			$modules = $this->get_active_modules();

			if ( empty( $modules ) ) {
				return;
			}

			/**
			 * Check if is new modules format or old
			 */
			if ( ! isset( $modules['gallery-grid'] ) ) {

				$fixed = array();

				foreach ( $modules as $module ) {
					$fixed[ $module ] = 'true';
				}

				$modules = $fixed;

			}


			foreach ( $modules as $module => $is_active ) {

				if ( 'true' === $is_active ) {

					$module_instance = isset( $this->modules[ $module ] ) ? $this->modules[ $module ] : false;
					if ( $module_instance ) {

						call_user_func( array( $module_instance, 'module_init' ) );
						$this->active_modules[] = $module;
					}
				}
			}


		}

		/**
		 * Get all modules list in format required for JS
		 *
		 * @return [type] [description]
		 */
		public function get_all_modules_for_js() {

			$result = array();

			foreach ( $this->modules as $module ) {

				$result[] = array(
					'value' => $module->module_id(),
					'label' => $module->module_name(),
				);

			}

			return $result;

		}

		/**
		 * Get all modules list
		 *
		 * @return [type] [description]
		 */
		public function get_all_modules() {
			$result = array();
			foreach ( $this->modules as $module ) {
				$result[ $module->module_id() ] = $module->module_name();
			}

			return $result;
		}

		/**
		 * Get active modules list
		 *
		 * @return [type] [description]
		 */
		public function get_active_modules() {

			$active_modules = get_option( $this->option_name, array() );

			return $active_modules;
		}

		/**
		 * Check if pased module is currently active
		 *
		 * @param  [type]  $module_id [description]
		 *
		 * @return boolean            [description]
		 */
		public function is_module_active( $module_id = null ) {
			return in_array( $module_id, $this->active_modules );
		}

		/**
		 * Get module instance by module ID
		 *
		 * @param  [type] $module_id [description]
		 *
		 * @return [type]            [description]
		 */
		public function get_module( $module_id = null ) {
			return isset( $this->modules[ $module_id ] ) ? $this->modules[ $module_id ] : false;
		}

	}

}