<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Plugin
 *
 * Main Plugin class
 */
if ( ! class_exists( 'Gloo' ) ) {

	class Gloo {

		/**
		 * Plugin Version
		 *
		 * @var string The plugin version.
		 */
		private $version = '1.3.59';

		public $modules;

		/**
		 * Instance
		 *
		 * @access private
		 * @static
		 *
		 * @var Plugin The single instance of the class.
		 */
		private static $_instance = null;

		/**
		 * Holder for base plugin path
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_path = null;

		/**
		 * Holder for base plugin url
		 *
		 * @since  1.0.0
		 * @access private
		 * @var    string
		 */
		private $plugin_url = null;


		/**
		 * Gloo menu page slug
		 *
		 * @var string
		 */
		public $admin_page = 'gloo-dashboard';

		public $dashboard;

		/**
		 * Instance
		 *
		 * Ensures only one instance of the class is loaded or can be loaded.
		 *
		 * @return Gloo An instance of the class.
		 * @access public
		 *
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 *  Plugin class constructor
		 *
		 * Register plugin action hooks and filters
		 *
		 * @access public
		 */
		public function __construct() {


			// Load files.
			add_action( 'init', array( $this, 'init' ), - 999 );

			// Register activation and deactivation hook.
			register_activation_hook( __FILE__, array( $this, 'activation' ) );
			register_deactivation_hook( __FILE__, array( $this, 'deactivation' ) );

		}

		public function init() {

			// require module files
			require $this->plugin_path( 'includes/core/modules-manager.php' );

			$this->admin_init();

			// initialize modules
			$this->modules = new Gloo_Modules();
			// add widgets category of gloo
			add_action( 'elementor/init', [ $this, 'register_category' ] );

			// enqueue styles
			add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts_and_styles' ] );
			add_action( 'admin_enqueue_scripts', array($this, 'register_scripts_and_styles') );
			add_action( 'elementor/editor/before_enqueue_styles', [ $this, 'enqueue_editor_styles' ] );
			add_action( 'elementor/editor/after_enqueue_styles', [ $this, 'icons_font_styles' ] );
			add_action( 'elementor/preview/enqueue_styles', [ $this, 'icons_font_styles' ] );
			add_action( 'elementor/dynamic_tags/register_tags', [ $this, 'register_dynamic_tags' ] );

			do_action( 'gloo/init', $this );
		}


		public function admin_init() {
			if ( ! is_admin() ) {
				return;
			}

			require $this->plugin_path( 'includes/dashboard/manager.php' );

			$this->dashboard = new Gloo_Dashboard();
		}


		public function enqueue_editor_styles() {
			wp_enqueue_style( 'gloo-elementor-editor', $this->plugin_url() . '/assets/css/editor.css', array(), $this->version );
		}

		public function register_scripts_and_styles() {
			

				wp_register_script(
					'gloo-interactor-button-js',
					$this->plugin_url() . '/assets/js/admin/gloo-interactor-button.js',
					[ 'jquery' ],
					$this->version
				);
				

			wp_register_style(
				'gloo-for-elementor',
				$this->plugin_url() . '/assets/css/gloo-for-elementor.css',
				[],
				$this->version
			);

			wp_register_script(
				'gloo-form-image-ui',
				$this->plugin_url() . '/assets/js/gloo-form-image-ui.js',
				[ 'jquery' ],
				$this->version
			);

			/* filepond js library for multiple file upload */ 
			wp_register_script( 
				'gloo-filepond-js', 
				$this->plugin_url() . '/assets/js/filepond/filepond.js',
				[ 'jquery' ],
				$this->version
			);

			wp_register_script( 
				'gloo-filepond-image-preview', 
				$this->plugin_url() . '/assets/js/filepond/filepond-plugin-image-preview.js',
				[ 'jquery' ],
				$this->version
			);
 		
			wp_register_script(
				'gloo-form-filepond-image',
				$this->plugin_url() . '/assets/js/filepond/gloo-form-filepond-image.js',
				[ 'jquery' ],
				$this->version
			);
			
			/* filepond css library for multiple file upload */ 
			wp_register_style( 
				'gloo-filepond', 
				$this->plugin_url() . '/assets/css/filepond/filepond.css',
				array(),
				$this->version
			);

			wp_register_style(
				'gloo-filepond-image-preview', 
				$this->plugin_url() . '/assets/css/filepond/filepond-plugin-image-preview.css',
				array(),
				$this->version
			);
		}

		/**
		 * Enqueue icons font styles
		 *
		 * @return void
		 */
		public function icons_font_styles() {

			wp_enqueue_style(
				'gloo-elements-font',
				$this->plugin_url() . '/assets/css/admin/gloo-icons.css',
				array(),
				$this->version
			);
		
		}

		public function register_dynamic_tags() {

			\Elementor\Plugin::$instance->dynamic_tags->register_group( 'gloo-dynamic-tags', [
				'title' => 'Gloo Dynamic Tags'
			] );

		}

		/**
		 * Register gloo category for elementor if not exists
		 *
		 * @return void
		 */
		public function register_category() {

			$elements_manager = Elementor\Plugin::instance()->elements_manager;
			$cherry_cat       = 'gloo';

			$elements_manager->add_category(
				$cherry_cat,
				array(
					'title' => esc_html__( 'Gloo', 'gloo_for_elementor' ),
					'icon'  => 'font',
				)
			);
		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function activation() {

		}

		/**
		 * Do some stuff on plugin activation
		 *
		 * @return void
		 * @since  1.0.0
		 */
		public function deactivation() {
		}

		/**
		 * Returns plugin version
		 *
		 * @return string
		 */
		public function get_version() {
			return $this->version;
		}


		/**
		 * Check if theme has elementor
		 *
		 * @return boolean
		 */
		public function has_elementor() {
			return defined( 'ELEMENTOR_VERSION' );
		}

		/**
		 * Check if theme has elementor
		 *
		 * @return boolean
		 */
		public function has_elementor_pro() {
			return defined( 'ELEMENTOR_PRO_VERSION' );
		}

		public function plugin_url( $path = null ) {

			if ( ! $this->plugin_url ) {
				$this->plugin_url = trailingslashit( plugin_dir_url( __FILE__ ) );
			}

			return $this->plugin_url . $path;
		}


		public function plugin_path( $path = null ) {

			if ( ! $this->plugin_path ) {
				$this->plugin_path = trailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path . $path;
		}

		public function modules_path( $path = null ) {
			return $this->plugin_path( 'includes/modules/' ) . $path;
		}

		public function get_interactor_images( $number = 4 ) {
			$array = [];

			$image_url = $this->plugin_url( 'assets/images/admin/gloo-thumbs/' );

			$features = array(
				'acftag.jpg'               => 'https://youtu.be/MpE5edTcNuc',
				'bundlemaker.jpg'                => 'https://youtu.be/u_a_GxicBcQ',
				'cartvalues.jpg'         => 'https://youtu.be/QJnm3_yyO7c',
				'Checkout.jpg'                 => 'https://youtu.be/6nKvh_0TcMw',
				'CLICKABLe.jpg'              => 'https://youtu.be/KIMqAA86rAQ',
				'colorpicker.jpg'             => 'https://youtu.be/-PByjw6GsFc',
				'commentcover.jpg'    => 'https://youtu.be/DedM6SgJCSo',
				'Composer.jpg'  => 'https://youtu.be/BBHtZ7qp-RQ',
				'CPT.jpg'    => 'https://youtu.be/JHCia1lFaxw',
				'DRAGGABLE.jpg'          => 'https://youtu.be/GrUItAMznIc',
				'dynamicnav.jpg'          => 'https://youtu.be/yk_VZEV78fA',
				'dynamify.jpg'                     => 'https://youtu.be/P8hGQkN-C9c',
				'fluiddynamic.jpg' => 'https://youtu.be/sIKz2pXP_lA',
				'FormsExtensions.jpg'=> 'https://youtu.be/sU9EI46tP0E',
				'globaltag.jpg'=> 'https://youtu.be/LgTx2qZDpv8',
				'googletag.jpg'=> 'https://youtu.be/Bm8nmFh_Yho',
				'HTMLTAG.jpg'=> 'https://youtu.be/Bm8nmFh_Yho',
				'interactorextensions.jpg'=> 'https://youtu.be/SnuuEFs8gqk',
				'interactorforms.jpg'=> 'https://youtu.be/YfdEaluNV24',
				'interactoroverview.jpg'=> 'https://youtu.be/iVO6hjYCsuA',
				'jsfpagination.jpg'=> 'https://youtu.be/xV2d-KUpbLU',
				'nativegat.jpg'=> 'https://youtu.be/iAeqEKdMMxw',
				'powergloo.jpg'=> 'https://youtu.be/CqFGyd0oOr4',
				'pricewidget.jpg'=> 'https://youtu.be/9fWMWUFg1dE',
				'querycontrol.jpg'=> 'https://youtu.be/lRqpfUwYTr8',
				'randomtag.jpg'=> 'https://youtu.be/kaH6yCKZc40',
				'relatedwoo.jpg'=> 'https://youtu.be/XD1bjDidJxA',
				'RepeaterField.jpg'=> 'https://youtu.be/dkxv_qmU4hA',
				'RepeaterTags.jpg'=> 'https://youtu.be/pwvxkw4NX8Q',
				'SchemaControl.jpg'=> 'https://youtu.be/zFVnqseZDPQ',
				'select2.jpg'=> 'https://youtu.be/mBZr9c3VDbk',
				'tageverywhere.jpg'=> 'https://youtu.be/XKYX86P4Aec',
				'taxonomy.jpg'=> 'https://youtu.be/-u7MdyPXN5I',
				'timetag.jpg'=> 'https://youtu.be/lnYxdSavOpY',
				'TYPO.jpg'=> 'https://youtu.be/5ezkf1s5vh0',
				'useragent.jpg'=> 'https://youtu.be/W_akFChTgNM',
				'validation.jpg'=> 'https://youtu.be/xVOwnYAUktM',
				'woodiscount.jpg'=> 'https://youtu.be/SSvlSTf21QY',
				'woogloo.jpg'=> 'https://youtu.be/m_2M0zlIlBE',
				'wootags.jpg'=> 'https://youtu.be/vuhw91i39bA',
				'wysiwyg.jpg'=> 'https://youtu.be/bghZ-e-8chI',
			);

			$random_array = array_rand( $features, $number );

			foreach ( $random_array as $file_name ) {
				$array[] = [
					'link'    => $features[ $file_name ],
					'img_url' => $image_url . $file_name,
				];
			}

			return $array;

		}

	}
}

if ( ! function_exists( 'gloo' ) ) {
	/**
	 * Returns instance of the plugin class.
	 *
	 * @return Gloo
	 * @since  1.0.0
	 */
	function gloo() {
		return Gloo::instance();
	}
}
// Instantiate Plugin Class
gloo();