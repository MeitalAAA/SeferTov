<?php

/**
 * Dashboard Manager
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

if (!class_exists('Gloo_Dashboard')) {

	/**
	 * Define Gloo_Dashboard class
	 */
	class Gloo_Dashboard
	{


		/**
		 * Constructor for the class
		 */
		function __construct()
		{
			//			add_action( 'admin_menu', array( $this, 'register_main_menu_page' ), 10 );
			//			add_action( 'admin_init', array( $this, 'init_components' ), 99 );

			add_action('admin_menu', [$this, 'gloo_setting_page']);
			add_action('admin_enqueue_scripts', [$this, 'gloo_enqueue_admin_script']);
			add_action('wp_ajax_gloo_update_options', [$this, 'update_options']);
			add_action('admin_head', [$this, 'gloo_icon_alignment']);
		}

		public function update_options()
		{

			$status = $_POST['status'] === "true";
			$module = $_POST['module'];

			if (!$module) {
				wp_send_json_error();
			}

			$option_name = gloo()->modules->option_name;
			$modules     = get_option($option_name, array());


			if (!in_array($module, $modules)) {
				if ($status) {
					$modules[] = $module;
				}
			} else {
				$module_key = array_search($module, $modules);
				if (!$status && $module_key !== false) {
					unset($modules[$module_key]);
				}
			}

			update_option($option_name, $modules);

			wp_send_json_success();
		}

		public function gloo_enqueue_admin_script($hook)
		{

			if (!$this->is_dashboard()) {
				return;
			}

			wp_enqueue_style('gloo-fonts', gloo()->plugin_url('assets/fonts/admin/fonts.css'), null, gloo()->get_version());
			wp_enqueue_style('gloo', gloo()->plugin_url('assets/css/admin/gloo.css'), null, gloo()->get_version());
			wp_enqueue_script('gloo', gloo()->plugin_url('assets/js/admin/gloo.js'), ['jquery'], gloo()->get_version());

			wp_localize_script(
				'gloo',
				'glooData',
				array(
					'ajaxUrl' => admin_url('admin-ajax.php'),
				)
			);
		}

		public function gloo_setting_page()
		{

			$active_modules = gloo()->modules->get_active_modules();

			/* Main Settings Page */
			add_menu_page(
				'GLoo',
				'GLoo',
				'manage_options',
				gloo()->admin_page,
				[$this, 'render_admin_page'],
				gloo()->plugin_url('assets/images/admin/gloo-icon.png')
			);


			/* Woo Gloo Settings Page */
			if (in_array('woo_gloo_modules', $active_modules)) {
				add_submenu_page(
					null, // hide from menu
					'Woo Gloo',
					'Woo Gloo',
					'manage_options',
					'woo-gloo-dashboard',
					[$this, 'render_admin_woo_gloo_page']
				);
			}

			/* Woo Gloo Settings Page */
			if (in_array('interactor', $active_modules)) {

				add_action('admin_init', [$this, 'gloo_interactor_settings_init']);
				//				add_action( 'load-tools_page_gloo_interactor_settings', [ $this, 'interactor_save_options' ] );

				add_submenu_page(
					null, // hide from menu
					'Interactor Settings',
					'Interactor Settings',
					'manage_options',
					'interactor-settings',
					[$this, 'render_admin_interactor_page']
				);
			}

			/* BuddyBoss Settings Page */
			if (in_array('buddyboss_gloo_kit', $active_modules) || in_array('gloo_learndash', $active_modules)) {
				add_submenu_page(
					null, // hide from menu
					'BuddyBoss Gloo',
					'BuddyBoss Gloo',
					'manage_options',
					'gloo-buddyboss',
					[$this, 'render_admin_buddyboss_gloo_page']
				);
			}

			/* Zoho Settings Page */
			add_submenu_page(
				null, // hide from menu
				'Zoho Gloo',
				'Zoho Gloo',
				'manage_options',
				'gloo-zoho-setting',
				[$this, 'render_admin_zoho_gloo_page']
			);
		}

		function interactor_save_options()
		{
			if (!isset($_POST) || !$_POST) {
				return;
			}

			if (!isset($_POST['gloo_interactor_settings_debug'])) {
				delete_option('gloo_interactor_settings_debug');
			} else {
				$value = boolval($_POST['gloo_interactor_settings_debug']);
				update_option('gloo_interactor_settings_debug', $value);
			}
		}

		public function gloo_interactor_settings_init()
		{
			$this->interactor_save_options();
			register_setting('gloo_interactor', 'gloo_interactor_settings');
			add_settings_section(
				'gloo_interactor_settings_section',
				__('Interactor Settings', 'wordpress'),
				'',
				'gloo_interactor_settings'
			);

			add_settings_field(
				'gloo_interactor_settings_debug',
				__('Debug Mode', 'wordpress'),
				[$this, 'gloo_interactor_settings_debug_render'],
				'gloo_interactor_settings',
				'gloo_interactor_settings_section'
			);
		}

		public function gloo_interactor_settings_debug_render()
		{
			$options = get_option('gloo_interactor_settings_debug');
?>
			<input type='checkbox' name='gloo_interactor_settings_debug' value='1' <?php echo $options ? "checked='checked'" : "" ?>>
<?php
		}


		public function gloo_icon_alignment()
		{
			echo '<style>
			#adminmenu #toplevel_page_gloo-dashboard img {
				padding: 4px;
				width: 19px;
				height: 22px;
			}
			#adminmenu li#toplevel_page_gloo-dashboard .wp-first-item {
				display: none;
			}
		  </style>';
		}

		public function render_admin_page()
		{
			include gloo()->plugin_path('includes/dashboard/views/admin-gloo-setting.php');
		}

		public function render_admin_woo_gloo_page()
		{
			include gloo()->plugin_path('includes/dashboard/views/admin-woo-gloo-modules-setting.php');
		}

		public function render_admin_interactor_page()
		{
			include gloo()->plugin_path('includes/dashboard/views/admin-interactor-settings.php');
		}

		public function render_admin_buddyboss_gloo_page()
		{
			include gloo()->plugin_path('includes/dashboard/views/admin-buddyboss-setting.php');
		}

		public function render_admin_zoho_gloo_page()
		{
			include gloo()->plugin_path('includes/dashboard/views/admin-zoho-setting.php');
		}

		/**
		 * Check if is dashboard page
		 *
		 * @return boolean [description]
		 */
		public function is_dashboard()
		{

			if (isset($_GET['page'])) {
				return ($_GET['page'] === gloo()->admin_page ||
					$_GET['page'] === 'listing_shortcode' ||
					$_GET['page'] === 'gloo_autocomplete_address' ||
					$_GET['page'] === 'gloo_frontend_post_creation' ||
					$_GET['page'] === 'gloo_frontend_post_editing' ||
					$_GET['page'] === 'gloo_dnm_elementor_addon' ||
					$_GET['page'] === 'woo-gloo-dashboard' ||
					$_GET['page'] === 'interactor-settings' ||
					$_GET['page'] === 'gloo-buddyboss' ||
					$_GET['page'] === 'data-source' ||
					$_GET['page'] === 'gloo_google_client' ||
					$_GET['page'] === 'datalayer_connector' ||
					$_GET['page'] === 'google-adsense' ||
					$_GET['page'] === 'gloo-zoho-setting');
			}
		}
	}
}
